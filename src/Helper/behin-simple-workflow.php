<?php

use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Controllers\Core\ConditionController;
use Behin\SimpleWorkflow\Controllers\Core\FieldController;
use Behin\SimpleWorkflow\Controllers\Core\FormController;
use Behin\SimpleWorkflow\Controllers\Core\ProcessController;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


if (!function_exists('getProcesses')) {
    function getProcesses()
    {
        // Log::info("function getProcess Used By user". Auth::user()->name);
        return ProcessController::getAll();
    }
}

if (!function_exists('getCases')) {
    function getCases()
    {
        // Log::info("function getCases Used By user". Auth::user()->name);
        return CaseController::getAll();
    }
}

if (!function_exists('getProcessForms')) {
    function getProcessForms()
    {
        // Log::info("function getProcessForms Used By user". Auth::user()->name);
        return FormController::getAll();
    }
}


if (!function_exists('getProcessScripts')) {
    function getProcessScripts()
    {
        // Log::info("function getProcessScripts Used By user". Auth::user()->name);
        return ScriptController::getAll();
    }
}

if (!function_exists('getProcessConditions')) {
    function getProcessConditions()
    {
        // Log::info("function getProcessConditions Used By user". Auth::user()->name);
        return ConditionController::getAll();
    }
}

if (!function_exists('getProcessTasks')) {
    function getProcessTasks()
    {
        // Log::info("function getProcessTasks Used By user". Auth::user()->name);
        return TaskController::getAll();
    }
}

if (!function_exists('getProcessFields')) {
    function getProcessFields()
    {
        // Log::info("function getProcessFields Used By user". Auth::user()->name);
        return FieldController::getAll();
    }
}

if (!function_exists('getFieldDetailsByName')) {
    function getFieldDetailsByName($fieldName)
    {
        // Log::info("function getFieldDetailsByName Used By user". Auth::user()->name);
        return FieldController::getByName($fieldName);
    }
}

if (!function_exists('previewForm')) {
    function previewForm($id)
    {
        return FormController::preview($id);
    }
}

if (!function_exists('taskHasError')) {
    function taskHasError($taskId)
    {
        // Log::info("function taskHasError Used By user". Auth::user()->name);
        return TaskController::TaskHasError($taskId);
    }
}

if (!function_exists('getUserInfo')) {
    function getUserInfo($userId)
    {
        // Log::info("function getUserInfo Used By user". Auth::user()->name);
        if (!$userId) {
            return null;
        }

        return User::query()
            ->where('id', $userId)
            ->orWhere('pm_user_uid', $userId)
            ->first();
    }
}

if (!function_exists('runScript')) {
    function runScript($id, $caseId)
    {
        // Log::info("function runScript Used By user". Auth::user()->name);
        return ScriptController::runScript($id, $caseId);
    }
}

if (!function_exists('toJalali')) {
    function toJalali($date)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        if (is_int($date)) {
            $date = Carbon::createFromTimestamp($date, 'Asia/Tehran');
        }
        // Log::info("function toJalali Used By user". Auth::user()->name);
        $jDate = Jalalian::fromCarbon($date);
        return $jDate;
    }
}

if (!function_exists('getFormInformation')) {
    function getFormInformation($id)
    {
        // Log::info("function getFormInformation Used By user". Auth::user()->name);
        return FormController::getById($id);
    }
}

if (!function_exists('convertPersianToEnglish')) {
    function convertPersianToEnglish($string)
    {
        static $map = [
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',
        ];

        return strtr($string, $map);
    }
}


if (!function_exists('convertPersianDateToTimestamp')) {
    function convertPersianDateToTimestamp($string, $to = 'seconds')
    {
        $date = convertPersianToEnglish($string);
        if (strlen($date) == 10) {
            $response = Jalalian::fromFormat('Y-m-d', $date)->toCarbon()->timestamp;
        } elseif (strlen($date) == 16) {
            $dateTime = explode(' ', $date);
            if (count($dateTime) == 2) {
                $date = $dateTime[0];
                $time = $dateTime[1];
                $dateString = "$date $time";
                $response = Jalalian::fromFormat('Y-m-d H:i', $dateString)->toCarbon()->timestamp;
            }
        }
        if ($to == 'seconds') {
            return $response;
        }
        return $response * 1000;
    }
}

if (!function_exists('normalizeList')) {
    function normalizeList($value): Collection
    {
        if ($value instanceof Collection) {
            return $value->filter(fn($item) => $item !== null && $item !== '')->values();
        }

        if (is_array($value)) {
            return collect($value)->filter(fn($item) => $item !== null && $item !== '')->values();
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return normalizeList($decoded);
            }

            return collect(array_map('trim', array_filter(explode(',', $value))))
                ->filter(fn($item) => $item !== '')
                ->values();
        }

        return collect();
    }
}

if (!function_exists('createPagingLinks')) {
    function createPagingLinks($rows)
    {
        if ($rows->hasPages()) {
            $s = '';
            $s .= '
            <div class="text-center row">
                <span class="col-sm-12">
                    صفحه ' . $rows->currentPage() . ' از ' . $rows->lastPage() . '
                </span>
                <a href="' . $rows->previousPageUrl() . '" class="btn btn-outline-secondary btn-sm">قبلی</a>
            ';
            for ($i = 1; $i <= $rows->lastPage(); $i++) {
                $s .= '<a href="' . $rows->url($i) . '" class="btn btn-outline-secondary btn-sm" ';
                if ($rows->currentPage() == $i) {
                    $s .= 'style="background-color: #007bff" ';
                }
                $s .= '>' . $i . '</a>';
            }
            $s .= '<a href="' . $rows->nextPageUrl() . '" class="btn btn-outline-secondary btn-sm">بعدی</a>';
            $s .= '</div>';
            return $s;
        }
    }
}

if (!function_exists('exploreModel')) {
    function exploreModel($model)
    {
        // اگر $model یک رشته نام کلاس بود، آن را به instance تبدیل کن
        if (is_string($model) && class_exists($model)) {
            $model = new $model();
        } elseif (!is_object($model) || !$model instanceof Model) {
            // اگر $model شیء مدل نیست، خطا بده یا null برگردان
            // return null;
            throw new \InvalidArgumentException('Provided argument must be an Eloquent Model instance or a valid model class name.');
        }


        $reflection = new \ReflectionClass($model);
        $data = [
            'class' => $reflection->getName(),
            'properties' => [],
            'methods' => [],
            'relations' => [],
            'columns' => [],
        ];

        // 1. گرفتن پراپرتی‌های عمومی
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            // لیست فیلتر شده پراپرتی‌های رایج Eloquent
            $eloquentCommonProperties = ['timestamps', 'casts', 'fillable', 'guarded', 'hidden', 'visible', 'with', 'withCount', 'appends', 'observables', 'localScales', 'perPage', 'resource', 'keyType', 'incrementing', 'primaryKey', 'table', 'connection', 'rules', 'exists'];
            if (!in_array($property->getName(), $eloquentCommonProperties)) {
                $data['properties'][] = $property->getName();
            }
        }

        // 2. گرفتن متدهای عمومی
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();

            // تعریف لیستی از متدهای خاص Eloquent و PHP که می‌خواهیم فیلتر کنیم
            $eloquentAndMagicMethods = [
                '__call',
                '__callStatic',
                '__clone',
                '__debugInfo',
                '__get',
                '__isset',
                '__invoke',
                '__serialize',
                '__sleep',
                '__toString',
                '__unset',
                '__unserialize',
                'getQueueableRelations',
                'newQueryWithoutScopes',
                'newQuery',
                'resolveRouteBinding',
                'getRouteKeyName',
                'getRouteKey',
                'getKeyName',
                'getKey',
                'getIncrementing',
                'increment',
                'decrement',
                'update',
                'save',
                'delete',
                'create',
                'find',
                'all',
                'exists',
                'count',
                'fresh',
                'refresh',
                'replicate',
                'fill',
                'fromRawAttributes',
                'forceDelete',
                'performUpdate',
                'performInsert',
                'performDelete',
                'performUpdateAndSave',
                'performInsertAndSave',
                'getFillable',
                'getGuarded',
                'getHidden',
                'getVisible',
                'getAppends',
                'getDates',
                'getDateFormat',
                'getPerPage',
                'getTable',
                'getConnectionName',
                'getKeyName',
                'getIncrementing',
                'getQueueableRelations',
                'hasAttribute',
                'getAttribute',
                'setAttribute',
                'getAttributes',
                'getOriginal',
                'getQueueableConnection',
                'getQueueableEntity',
                'getQueueableId',
                'resolveScope',
                'scopeQuery',
                'applyScopes',
                'newCollection',
                'fireModelEvent',
                'getConnection',
                'getPerPage',
                'getQueueableConnection',
                'newEloquentBuilder',
                'newBaseQueryBuilder',
                'newModelQuery',
                'newCollection',
                'newFromBuilder'
            ];

            // فیلتر کردن متدهای نامرتبط
            if (Str::startsWith($methodName, ['__']) || in_array($methodName, $eloquentAndMagicMethods)) {
                continue;
            }

            // 3. تشخیص روابط Eloquent و صدا زدن متدها
            try {
                // صدا زدن متد روی instance مدل
                $reflectionMethod = $reflection->getMethod($methodName);
                // بررسی اینکه آیا متد استاتیک است یا نه
                if ($reflectionMethod->isStatic()) {
                    // اگر متد استاتیک بود، آن را نادیده می‌گیریم (چون ما instance داریم)
                    // یا اگر خواستید متدهای استاتیک را هم لیست کنید، اینجا اضافه کنید
                    continue;
                }

                // اطمینان از اینکه متد پارامتر ندارد یا پارامترهایش Optional هستند
                // این بخش را ساده‌تر می‌کنیم: فقط متدهایی که پارامتر اجباری ندارند را صدا می‌زنیم
                $parameters = $reflectionMethod->getParameters();
                $canInvoke = true;
                foreach ($parameters as $param) {
                    if (!$param->isOptional() && !$param->isVariadic()) {
                        $canInvoke = false;
                        break;
                    }
                }

                if ($canInvoke) {
                    $return = $reflectionMethod->invoke($model);

                    // بررسی نوع بازگشتی برای تشخیص روابط Eloquent
                    if ($return instanceof Relation) {
                        $data['relations'][] = $methodName;
                    } else {
                        // اگر متد بود ولی رابطه نبود، آن را به لیست متدها اضافه می‌کنیم
                        $data['methods'][] = $methodName . '()';
                    }
                } else {
                    // متدهایی که پارامتر اجباری دارند و نمی‌توانیم صدا بزنیم
                    $data['methods'][] = $methodName . '() (requires parameters)';
                }
            } catch (\Throwable $e) {
                // اگر خطایی در هنگام صدا زدن متد رخ داد (مثلا متد استاتیک بود یا پارامتر لازم داشت)
                // اینجا متدهای استاتیک را جداگانه بررسی می‌کنیم
                if (is_object($model) && method_exists($model, $methodName)) {
                    $methodReflection = new \ReflectionMethod($model, $methodName);
                    if (!$methodReflection->isStatic()) {
                        // اگر متد instance بود ولی صدا زدنش خطا داد (مثلا پارامتر لازم داشت)
                        $data['methods'][] = $methodName . '()';
                    }
                }
            }
        }

        // 4. گرفتن ستون‌های دیتابیس
        if ($model instanceof Model) {
            try {
                // ابتدا نام جدول را بدست می‌آوریم
                $tableName = $model->getTable();

                // اگر جدول وجود داشت، ستون‌ها را لیست می‌کنیم
                if (Schema::hasTable($tableName)) {
                    $columns = Schema::getColumnListing($tableName);
                    $data['columns'] = $columns;
                } else {
                    $data['columns'] = ["Table '{$tableName}' does not exist."];
                }
            } catch (\Throwable $e) {
                $data['columns'] = ['Error fetching columns: ' . $e->getMessage()];
            }
        }

        return $data;
    }
}
