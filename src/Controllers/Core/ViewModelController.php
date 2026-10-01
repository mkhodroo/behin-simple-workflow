<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Entity;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\ViewModel;
use BehinFileControl\Controllers\FileController;
use BehinUserRoles\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ViewModelController extends Controller
{
    public function index()
    {
        $viewModels = ViewModel::get();
        return view('SimpleWorkflowView::Core.ViewModel.index', compact('viewModels'));
    }

    public function create()
    {
        $entities = EntityController::getAll();
        $forms = FormController::getAll();
        return view('SimpleWorkflowView::Core.ViewModel.create', compact('entities', 'forms'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        $data['api_key'] = $request->api_key ? $request->api_key : Str::random(16);
        $viewModel = ViewModel::create($data);
        return redirect()->back()->with(['success' => trans('Created Successfully')]);
    }

    public function edit(ViewModel $view_model)
    {
        $entities = EntityController::getAll();
        $forms = FormController::getAll();
        $scripts = ScriptController::getAll();
        return view('SimpleWorkflowView::Core.ViewModel.edit', compact('view_model', 'entities', 'forms', 'scripts'));
    }

    public function update(Request $request, ViewModel $view_model)
    {
        $data = $request->except('_token');
        $data['api_key'] = $request->api_key ? $request->api_key : Str::random(16);
        $nullableArrayFields = [
            'which_rows_user_can_delete',
            'which_rows_user_can_update',
            'which_rows_user_can_read'
        ];

        foreach ($nullableArrayFields as $field) {
            $data[$field] = $request->has($field) ? $request->$field : [];
        }

        $view_model->update($data);
        return redirect()->back()->with(['success' => trans('Updated Successfully')]);
    }

    public function copy(ViewModel $view_model)
    {
        // کپی اطلاعات رکورد
        $newViewModel = $view_model->replicate();

        // در صورت نیاز، فیلدهایی که باید منحصر به‌فرد باشن رو تغییر بده (مثلاً نام یا شناسه)
        $newViewModel->name = $newViewModel->name . ' (Copy)';

        // ذخیره رکورد جدید
        $newViewModel->save();

        return redirect()->back()->with(['success' => trans('fields.Copy Successfully')]);
    }

    public function export(Request $request)
    {
        $ids = $request->input('view_model_ids', []);
        if (empty($ids)) {
            return redirect()->route('simpleWorkflow.view-model.index')->with('error', 'No view models selected for export.');
        }
        $viewModels = ViewModel::whereIn('id', $ids)->get();
        $fileName = 'view-models-' . date('Ymd_His') . '.json';

        if ($viewModels->count() === 1) {
            $content = $viewModels->first()->toJson(JSON_PRETTY_PRINT);
        } else {
            $content = $viewModels->toJson(JSON_PRETTY_PRINT);
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'view_models_file' => 'required|file',
        ]);

        $content = file_get_contents($request->file('view_models_file')->getRealPath());
        $data = json_decode($content, true);

        if (is_null($data)) {
            return redirect()->route('simpleWorkflow.view-model.index')->with('error', 'Invalid import file.');
        }

        $items = isset($data[0]) ? $data : [$data];
        $fillable = (new ViewModel())->getFillable();

        foreach ($items as $item) {
            $attributes = [];
            foreach ($fillable as $field) {
                if (array_key_exists($field, $item)) {
                    $attributes[$field] = $item[$field];
                }
            }

            $id = $item['id'] ?? Str::uuid()->toString();
            $attributes['id'] = $id;

            ViewModel::updateOrCreate(['id' => $id], $attributes);
        }

        return redirect()->route('simpleWorkflow.view-model.index')->with('success', 'View models imported successfully.');
    }


    public static function getById($id)
    {
        return ViewModel::find($id);
    }

    public static function resolveColumnPath($model, string $columnPath)
    {
        try {
            $parts = explode('()->', $columnPath);
            $current = $model;

            foreach ($parts as $index => $part) {
                if (!$current) {
                    return null;
                }

                if ($index === count($parts) - 1) {
                    return $current->$part ?? null;
                }

                // استفاده از property به جای method
                $current = $current->$part();
            }

            return null;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * تبدیل امن مقدار ستون‌های which_rows_user_can_* به آرایه‌ای از رشته‌ها
     * (مقدار ممکن است آرایه، JSON یا رشته کاما-جدا باشد)
     */
    public static function normalizeRowCondition($condition): array
    {
        if (is_array($condition)) {
            $items = $condition;
        } elseif (is_string($condition) && trim($condition) !== '') {
            $decoded = json_decode($condition, true);
            $items = is_array($decoded) ? $decoded : explode(',', $condition);
        } else {
            $items = [];
        }

        return array_values(array_filter(array_map(
            fn ($item) => trim((string) $item),
            $items
        ), fn ($item) => $item !== ''));
    }

    /**
     * ارزیابی شرط دسترسی روی یک رکورد.
     * ورودی userId جدا شده تا این منطق مستقل از Laravel قابل تست باشد.
     */
    public static function evaluateRowConditionForUser(?int $userId, $condition, $row): bool
    {
        $condition = self::normalizeRowCondition($condition);

        if (in_array('all', $condition, true)) {
            return true;
        }

        if (empty($condition) || $userId === null) {
            return false;
        }

        $userId = (int) $userId;

        if (
            in_array('user-created-it', $condition, true) &&
            (int) ($row['created_by'] ?? 0) === $userId
        ) {
            return true;
        }

        if (
            in_array('user-updated-it', $condition, true) &&
            (int) ($row['updated_by'] ?? 0) === $userId
        ) {
            return true;
        }

        if (in_array('user-contributed-it', $condition, true)) {
            foreach (explode(',', (string) ($row['contributers'] ?? '')) as $contributor) {
                $contributor = trim($contributor);

                if ($contributor !== '' && (int) $contributor === $userId) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function userCanRow($row, $condition): bool
    {
        return self::evaluateRowConditionForUser(
            Auth::id() === null ? null : (int) Auth::id(),
            $condition,
            [
                'created_by' => $row->created_by ?? null,
                'updated_by' => $row->updated_by ?? null,
                'contributers' => $row->contributers ?? null,
            ]
        );
    }

    public static function userCanUpdateRow($row, $updateCondition)
    {
        return self::userCanRow($row, $updateCondition);
    }

    public static function userCanDeleteRow($row, $deleteCondition)
    {
        return self::userCanRow($row, $deleteCondition);
    }

    public function createNewBtnHtml(Request $request)
    {
        $viewModel = self::getById($request->viewModel_id);

        if (!$viewModel) {
            return response(trans('fields.View model not found'), 404);
        }

        if ($viewModel->api_key != $request->api_key) {
            return response(
                trans("fields.Api key is not valid"),
                403
            );
        }

        if (!$viewModel->allow_create_row) {
            return '';
        }

        $model = self::getModelById($viewModel->id);
        $case = CaseController::getById($request->case_id);
        $max_number_of_rows = (int) $viewModel->max_number_of_rows;

        /*
         * دریافت رکوردها
         */
        $rows = $model::query()->whereNull('deleted_at');

        if ($case) {
            if ($viewModel->show_rows_based_on == 'case_id') {
                $rows->where('case_id', $case->id);
            } elseif ($viewModel->show_rows_based_on == 'case_number') {
                $rows->where('case_number', $case->number);
            }
        }

        $rowCount = (clone $rows)->count();

        /*
         * ایجاد HTML دکمه
         */
        $s = '';

        if ($max_number_of_rows <= 0 || $rowCount < $max_number_of_rows) {
            $btnLabel = '';//trans('fields.Create new');

            $s .= "<button
            type='button'
            class='btn btn-success'
            style='
                height: 100%;
                border-radius: 0;
                white-space: nowrap;
                border-top: 0;
                border-bottom: 0;
            '
            onclick='open_view_model_create_new_form(
                `{$viewModel->create_form}`,
                `{$viewModel->id}`,
                `{$viewModel->api_key}`
            )'
        >";

            $s .= "<i class='fa fa-plus'></i> ";

            $s .= $btnLabel;

            $s .= "</button>";
        }

        return $s;
    }


    public function getRows(Request $request)
    {
        $viewModel = self::getById($request->viewModel_id);

        if (!$viewModel) {
            return response(trans('fields.View model not found'), 404);
        }

        if ($viewModel->api_key != $request->api_key) {
            return response(trans("fields.Api key is not valid"), 403);
        }

        try {
            $case = CaseController::getById($request->case_id);

            $columns = array_values(array_filter(array_map(
                'trim',
                explode(',', (string) $viewModel->default_fields)
            ), fn ($column) => $column !== ''));

            $max_number_of_rows = (int) $viewModel->max_number_of_rows;

            // ✅ تبدیل ایمن به آرایه
            $readCondition = self::normalizeRowCondition($viewModel->which_rows_user_can_read);
            $updateCondition = self::normalizeRowCondition($viewModel->which_rows_user_can_update);
            $deleteCondition = self::normalizeRowCondition($viewModel->which_rows_user_can_delete);

            $model = self::getModelById($viewModel->id);
            $s = '';
            $rows = collect();

            if ($viewModel->allow_read_row) {
                $query = $model::query()->whereNull('deleted_at');

                if ($case) {
                    if ($viewModel->show_rows_based_on == 'case_id') {
                        $query->where('case_id', $case->id);
                    } elseif ($viewModel->show_rows_based_on == 'case_number') {
                        $query->where('case_number', $case->number);
                    }
                }

                // اگر 'all' انتخاب شده یا هیچ شرطی تعریف نشده، محدودیتی اعمال نمی‌شود
                if ($readCondition && !in_array('all', $readCondition, true)) {
                    $query->where(function ($q) use ($readCondition) {
                        if (in_array('user-created-it', $readCondition, true)) {
                            $q->orWhere('created_by', Auth::id());
                        }

                        if (in_array('user-updated-it', $readCondition, true)) {
                            $q->orWhere('updated_by', Auth::id());
                        }

                        if (in_array('user-contributed-it', $readCondition, true)) {
                            $q->orWhereRaw('FIND_IN_SET(?, contributers)', [Auth::id()]);
                        }
                    });
                }

                $rows = $query->orderBy('updated_at', 'desc')
                    ->get()
                    ->each(function ($row) use ($viewModel, $updateCondition, $deleteCondition) {
                        $row->show_as = $viewModel->show_as;

                        // ✅ ابتدا سوییچ allow_* و سپس شرط اختصاصی کاربر بررسی می‌شود
                        $row->allow_update = (bool) $viewModel->allow_update_row &&
                            self::userCanUpdateRow($row, $updateCondition);

                        $row->allow_delete = (bool) $viewModel->allow_delete_row &&
                            self::userCanDeleteRow($row, $deleteCondition);
                    });

                if ($viewModel->script_before_show_rows) {
                    $request->merge(['rows' => $rows]);
                    $rows = collect(ScriptController::runFromView($request, $viewModel->script_before_show_rows));
                }

                foreach ($rows as $row) {
                    if ($row->show_as == 'table') {
                        $s .= "<tr>";
                        foreach ($columns as $column) {
                            try {
                                if (str_contains($column, '()->')) {
                                    $value = self::resolveColumnPath($row, $column);
                                } elseif (Str::endsWith($column, '()')) {
                                    $method = Str::beforeLast($column, '()');

                                    if ($method && method_exists($row, $method)) {
                                        $value = $row->$method();
                                    } else {
                                        $value = 'تابع تعریف نشده است';
                                    }
                                } else {
                                    $value = $row->$column ?? null;
                                }

                                $s .= "<td style='border-top: 0px;border-left: solid gray 1px;'>" .
                                    (is_scalar($value) || $value === null ? e((string) $value) : (string) $value) .
                                    "</td>";
                            } catch (\Throwable $e) {
                                $s .= "<td>" . e($e->getMessage()) . "</td>";
                            }
                        }
                        $s .= "<td style='border: 0px'>";
                        if ($row->allow_update) {
                            $s .= "<i class='fa fa-edit btn btn-sm btn-success ml-1' onclick='open_view_model_form(`$viewModel->update_form`, `$viewModel->id`,`$row->id`, `$viewModel->api_key`)'></i>";
                        }

                        if ($row->allow_delete) {
                            $s .= "<i class='fa fa-trash btn btn-sm btn-danger ml-1' onclick='delete_view_model_row(`$viewModel->id`,`$row->id`, `$viewModel->api_key`)'></i>";
                        }
                        $s .= "</td>";
                        $s .= "</tr>";
                    }
                    if ($row->show_as == 'box') {
                        $formRequest = new Request([
                            'api_key' => $viewModel->api_key,
                            'row_id' => $row->id,
                            'case_id' => $request->case_id,
                            'inbox_id' => $request->inbox_id,
                            'viewModel_id' => $viewModel->id,
                        ]);
                        $s .= "<div class=''>";
                        if ($row->allow_update) {
                            $s .= FormController::open($formRequest, $viewModel->update_form, false);
                        } else {
                            $s .= FormController::openReadForm($formRequest, $viewModel->read_form, false);
                        }
                        $s .= "</div>";
                    }
                }
            }
            $body = $s;

            $footer = '';
            $s = '';
            if (
                $viewModel->allow_create_row &&
                ($max_number_of_rows <= 0 || $rows->count() < $max_number_of_rows)
            ) {
                $s .= "";
                $colspan = count($columns) + 1;
                $btnLabel = trans('fields.Create new');
                $s .= "<div class='card-footer' colspan='{$colspan}'>";
                $s .= "<button class='btn btn-sm btn-primary' onclick='open_view_model_create_new_form(`$viewModel->create_form`, `$viewModel->id`, `$viewModel->api_key`)'>";
                $s .= "<i class='fa fa-plus' aria-hidden='true'></i>{$btnLabel}</button></div>";
                $s .= "";
            }
            $footer = $s;
            $total = $body . $footer;


            return response()->json([
                'body' => $body,
                'footer' => $footer,
                'total' => $total,
            ]);
        } catch (\Throwable $e) {
            // ✅ ساختار پاسخ همیشه یکسان می‌ماند تا کلاینت دچار خطای JS نشود
            return response()->json([
                'body' => '',
                'footer' => '',
                'total' => '',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateRecord(Request $request)
    {
        try {
            $case = CaseController::getById($request->caseId);
            $viewModel = self::getById($request->viewModelId);

            if (!$viewModel) {
                return response(trans('fields.View model not found'), 404);
            }

            if ($viewModel->api_key != $request->api_key) {
                return response(trans("fields.Api key is not valid"), 403);
            }

            $model = self::getModelById($viewModel->id);

            $row = $model::findOrNew($request->rowId);

            $isNew = !$row->exists;
            if ($isNew) {
                // ✅ بررسی سوییچ ایجاد رکورد
                if (!$viewModel->allow_create_row) {
                    return response(trans('fields.Create row is not allowed'), 403);
                }

                $max_number_of_rows = (int) $viewModel->max_number_of_rows;

                if ($max_number_of_rows > 0) {
                    $rowsQuery = $model::query()->whereNull('deleted_at');

                    if ($case && $viewModel->show_rows_based_on == 'case_id') {
                        $rowsQuery->where('case_id', $case->id);
                    } elseif ($case && $viewModel->show_rows_based_on == 'case_number') {
                        $rowsQuery->where('case_number', $case->number);
                    }

                    if ($rowsQuery->count() >= $max_number_of_rows) {
                        return response(
                            trans("حداکثر تعداد رکورد مجاز " . $max_number_of_rows . " رکورد است"),
                            403
                        );
                    }
                }
            } else {
                // ✅ بررسی سوییچ ویرایش رکورد
                if (!$viewModel->allow_update_row) {
                    return response(trans('fields.Update row is not allowed'), 403);
                }

                if (!self::userCanUpdateRow($row, $viewModel->which_rows_user_can_update)) {
                    return response(trans('fields.Access denied'), 403);
                }
            }

            $data = $request->all();
            // بررسی داینامیک فایل‌ها
            foreach ($request->allFiles() as $fieldName => $file) {
                $savedPaths = [];
                $path = FileController::store($file, 'simpleWorkflow', true);
                if ($path['status'] == 200) {
                    $data[$fieldName] = $path['dir'];
                } else {
                    return response($path['message'], $path['status']);
                }
            }
            // $data[$fieldName] = $savedPaths;

            if ($isNew && $viewModel->script_before_create) {
                $request->merge(['rowData' => $data]);

                $result = ScriptController::runFromView($request, $viewModel->script_before_create);

                if ($result) {
                    return $result;
                }

                $data = $request->all();
                if (isset($data['rowData']) && is_array($data['rowData'])) {
                    $data = array_merge($data, $data['rowData']);
                }
                unset($data['rowData']);
                if ($request->has('rowData')) {
                    $request->request->remove('rowData');
                }
            }

            $fillable = $row->getFillable();
            foreach ($data as $key => $value) {
                if (in_array($key, $fillable)) {
                    $row->$key = $value;
                }
            }

            if ($isNew) {
                if ($case && in_array('case_id', $fillable)) {
                    $row->case_id = $case->id;
                }
                if ($case && in_array('case_number', $fillable)) {
                    $row->case_number = $case->number;
                }
                $row->created_by = Auth::id();
                $row->contributers = Auth::id();
            }
            $row->updated_by = Auth::id();

            // اضافه کردن کاربر فعلی به contributers (بدون تکرار)
            $contribs = array_filter(array_map('trim', explode(',', (string) ($row->contributers ?? ''))));
            if (!in_array(Auth::id(), $contribs)) {
                $contribs[] = Auth::id();
            }
            $row->contributers = implode(',', array_filter($contribs, fn ($id) => $id !== '' && $id !== null));

            $row->save();

            if ($isNew && $viewModel->script_after_create) {
                $request->merge(['rowId' => $row->id]);

                $result = ScriptController::runFromView($request, $viewModel->script_after_create);

                if ($result) {
                    return $result;
                }
            }

            if (!$isNew && $viewModel->script_after_update) {
                $request->merge(['rowId' => $row->id]);

                $result = ScriptController::runFromView($request, $viewModel->script_after_update);

                if ($result) {
                    return $result;
                }
            }
        } catch (\Throwable $th) {
            return response($th->getMessage(), 500);
        }


        return response(trans('fields.updated'));
    }

    public function deleteRecord(Request $request)
    {
        $viewModel = self::getById($request->viewModel_id);

        if (!$viewModel) {
            return response(trans('fields.View model not found'), 404);
        }

        if ($viewModel->api_key != $request->api_key) {
            return response(trans("fields.Api key is not valid"), 403);
        }

        // ✅ بدون اجازه حذف، حذفی انجام نمی‌شود
        if (!$viewModel->allow_delete_row) {
            return response(trans('fields.Delete row is not allowed'), 403);
        }

        $model = self::getModelById($viewModel->id);

        $row = $model::find($request->row_id);

        if (!$row) {
            return response(trans('fields.Record not found'), 404);
        }

        if (!self::userCanDeleteRow($row, $viewModel->which_rows_user_can_delete)) {
            return response(trans('fields.Access denied'), 403);
        }

        try {
            $row->delete();

            if ($viewModel->script_after_delete) {
                $request->merge(['rowId' => $request->row_id]);
                ScriptController::runFromView($request, $viewModel->script_after_delete);
            }
        } catch (\Throwable $th) {
            return response($th->getMessage(), 500);
        }

        return response(trans('fields.deleted'));
    }


    public static function getModelById($id)
    {
        $viewModel = ViewModel::find($id);
        $entity = Entity::find($viewModel->entity_id);
        $tableNamespace = $entity->namespace;
        $tableModelName = $entity->model_name;
        $model = "\\" . $tableNamespace . "\\" . $tableModelName;
        return $model;
    }
}
