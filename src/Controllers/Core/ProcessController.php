<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Elements\ElementRegistry;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskJump;
use BehinUserRoles\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProcessController extends Controller
{
    public function index(): View
    {
        $processes = self::getAll();
        return view('SimpleWorkflowView::Core.Process.index', compact('processes'));
    }

    public function create(): View
    {
        return view('SimpleWorkflowView::Core.Process.create');
    }

    public function store(Request $request): Process
    {
        return Process::create($request->all());
    }

    public function edit($processId): View
    {
        $process = Process::findOrFail($processId);
        return view('SimpleWorkflowView::Core.Process.edit', compact('process'));
    }

    public function update(Request $request, $processId)
    {
        $process = Process::findOrFail($processId);
        $process->update($request->only(['name', 'category', 'case_prefix']));
        return redirect()->route('simpleWorkflow.process.index');
    }

    public static function getById($id): Process
    {
        return Process::find($id);
    }

    public static function getAll(): object
    {
        return Process::orderBy('created_at','desc')->get();
    }

    public static function listOfProcessThatUserCanStart($userId = null):array
    {
        $userId = $userId ? $userId : Auth::id();
        $processes = self::getAll();
        $ar = [];
        foreach($processes as $process)
        {
            $startTasks = TaskController::getProcessStartTasks($process->id, false);
            foreach($startTasks as $startTask)
            {
                $result = TaskActorController::userIsAssignToTask($startTask->id, $userId);
                if($result)
                {
                    $process->task = $startTask;
                    $ar[] = $process;
                }
            }
        }

        return $ar;
    }

    public static function startListView():View
    {
        return view('SimpleWorkflowView::Core.Process.start-list')->with([
            'processes' => self::listOfProcessThatUserCanStart()
        ]);
    }

    public static function start($taskId, $force = false, $redirect = true, $inDraft = false, $caseNumber = null, $creator = null, $parentId = null)
    {
        $task = TaskController::getById($taskId);
        if (!$task) {
            return response()->json([
                'msg' => trans('fields.Task not found')
            ], 404);
        }
        if ($task->is_preview) {
            return response()->json([
                'msg' => trans('fields.Task is in preview mode and cannot be started')
            ], 403);
        }
        if(!$force)
        {
            $listOfProcessThatUserCanStart = collect(self::listOfProcessThatUserCanStart(Auth::id()))->pluck('id')->toArray();
            if(!in_array($task->process_id, $listOfProcessThatUserCanStart))
            {
            return response()->json([
                    'msg' => trans("You don't have permission to start this process")
                ], 403);
            }
        }
        if($creator){
            
        }elseif(Auth::check()){
            $creator = Auth::user()->id;
        }else{
            $ip = request()->ip();
            $tempUser = User::create([
                'name' => $ip,
                'email' => $ip.'-'.Str::random(5).'@temp.local',
                'password' => Hash::make(Str::random(16)),
            ]);
            $creator = $tempUser->id;
            Auth::login($tempUser);
        }

        $case = CaseController::create($task->process_id, $creator, null, $inDraft, $caseNumber, $parentId);
        $status = $inDraft ? 'draft' : 'new';
        $inbox = InboxController::create($taskId, $case->id, $creator, $status);
        if($redirect)
        {
            // return InboxController::view($inbox->id);
            return redirect()->route('simpleWorkflow.inbox.view', $inbox->id);
        }
        return $inbox;
    }

    public static function processHasError($processId){
        $process = ProcessController::getById($processId);
        $hasError = 0;
        foreach($process->tasks() as $task){
            if ($task->is_preview) {
                continue;
            }
            if(TaskController::TaskHasError($task->id)){
                $hasError++;
            }
        }
        $process->number_of_error =  $hasError;
        $process->save();
        return $hasError;
    }

    public static function startFromScript($taskId, $creator, $caseNumber = null, $parentId){
        return self::start($taskId, true, false, false, $caseNumber, $creator, $parentId);
    }

    /**
     * ساخت بخش «executive» یک تسک برای اکسپورت؛ ستون‌ها را المان خودش اعلام می‌کند.
     */
    public static function exportExecutiveElement(Task $task)
    {
        $executive = $task->executiveElement();
        $element = $task->element();

        if (!$executive || !$element) {
            return null;
        }

        return Arr::only($executive->toArray(), $element->exportColumns());
    }

    /**
     * ساخت/به‌روزرسانی المان اجرایی یک تسک هنگام ایمپورت.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function importExecutiveElement(array $executive, ?string $type)
    {
        $element = app(ElementRegistry::class)->get($type);
        $modelClass = $element?->executiveModelClass();

        if (!$modelClass) {
            return null;
        }

        $data = Arr::only($executive, $element->importColumns());

        $existing = !empty($executive['id']) ? $modelClass::find($executive['id']) : null;

        if ($existing) {
            $existing->update($data);

            return $existing;
        }

        return $modelClass::create($data);
    }

    public function exportView($processId): View
    {
        $process = Process::findOrFail($processId);
        $data = $process->toArray();
        $data['tasks'] = [];

        foreach ($process->tasks() as $task) {
            $taskArr = $task->toArray();
            $taskArr['actors'] = $task->actors()->get()->toArray();
            $taskArr['jumps'] = $task->jumps()->get()->toArray();

            $executive = self::exportExecutiveElement($task);
            if ($executive) {
                $taskArr['executive'] = $executive;
            }

            $data['tasks'][] = $taskArr;
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return view('SimpleWorkflowView::Core.Process.export', compact('json'));
    }

    public function importView(): View
    {
        return view('SimpleWorkflowView::Core.Process.import');
    }
    /**
     * Export the complete definition of a process including its tasks,
     * actors and jumps. The result is returned as JSON so it can be
     * imported in another instance of the application.
     */
    public function export($processId)
    {
        $process = Process::findOrFail($processId);
        $data = $process->toArray();
        $data['tasks'] = [];

        foreach ($process->tasks() as $task) {
            $taskArr = $task->toArray();
            $taskArr['actors'] = $task->actors()->get()->toArray();
            $taskArr['jumps'] = $task->jumps()->get()->toArray();
            $executive = self::exportExecutiveElement($task);
            if ($executive) {
                $taskArr['executive'] = $executive;
            }
            $data['tasks'][] = $taskArr;
        }

        return response()->json($data);
    }

    /**
     * Import a process definition previously exported by the export
     * method. All tasks and their relations are recreated while the
     * new identifiers are mapped so relationships stay intact.
     */
    public function import(Request $request)
    {
        $payload = $request->json()->all();
        $process = null;

        DB::transaction(function () use ($payload, &$process) {
            $processData = Arr::except($payload, ['tasks']);
            $process = Process::create($processData);

            $tasksMap = [];

            foreach ($payload['tasks'] ?? [] as $task) {
                $oldId = $task['id'] ?? null;
                $actors = $task['actors'] ?? [];
                $jumps = $task['jumps'] ?? [];
                $parentOld = $task['parent_id'] ?? null;
                $nextOld = $task['next_element_id'] ?? null;
                $executive = $task['executive'] ?? null;

                $taskData = Arr::except($task, ['id', 'actors', 'jumps', 'parent_id', 'next_element_id', 'executive']);
                $taskData['process_id'] = $process->id;
                $taskData['parent_id'] = null;
                $taskData['next_element_id'] = null;

                $newTask = Task::create($taskData);

                $tasksMap[$oldId] = [
                    'model' => $newTask,
                    'parent_old' => $parentOld,
                    'next_old' => $nextOld,
                    'jumps' => $jumps,
                    'element' => $newTask->element(),
                    'executive_data' => $executive,
                    'executive_model' => null,
                ];

                foreach ($actors as $actor) {
                    TaskActor::create([
                        'task_id' => $newTask->id,
                        'actor' => $actor['actor'] ?? null,
                    ]);
                }
                if ($executive) {
                    $executiveModel = self::importExecutiveElement($executive, $newTask->type);

                    if ($executiveModel) {
                        $newTask->executive_element_id = $executiveModel->id;
                        $newTask->save();
                        $tasksMap[$oldId]['executive_model'] = $executiveModel;
                    }
                }
            }

            // نگاشت نهایی تسک‌ها: شناسهٔ قدیمی => مدل تسک جدید
            $flatTasksMap = collect($tasksMap)->map(fn ($entry) => $entry['model'])->all();

            foreach ($tasksMap as $oldId => $entry) {
                $task = $entry['model'];

                if ($entry['parent_old'] && isset($tasksMap[$entry['parent_old']])) {
                    $task->parent_id = $tasksMap[$entry['parent_old']]['model']->id;
                }

                if ($entry['next_old'] && isset($tasksMap[$entry['next_old']])) {
                    $task->next_element_id = $tasksMap[$entry['next_old']]['model']->id;
                }

                $task->save();

                foreach ($entry['jumps'] as $jump) {
                    $next = $jump['next_task_id'] ?? null;
                    if ($next && isset($tasksMap[$next])) {
                        TaskJump::create([
                            'task_id' => $task->id,
                            'next_task_id' => $tasksMap[$next]['model']->id,
                        ]);
                    }
                }

                // المان اجرایی خودش تصمیم می‌گیرد با ارجاع‌هایش به تسک‌های دیگر چه کند
                if ($entry['element']) {
                    $entry['element']->afterImport(
                        $entry['executive_model'],
                        (array) ($entry['executive_data'] ?? []),
                        $flatTasksMap
                    );
                }
            }
        });

        return response()->json(['status' => 'ok', 'process_id' => $process->id]);
    }
}
