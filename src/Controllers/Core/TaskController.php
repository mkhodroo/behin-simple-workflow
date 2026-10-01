<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Elements\ElementRegistry;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index($process_id)
    {
        $process = ProcessController::getById($process_id);
        $elements = app(ElementRegistry::class);
        return view('SimpleWorkflowView::Core.Task.create')->with([
            'process' => $process,
            'forms' => FormController::getAll(),
            'scripts'=> ScriptController::getAll(),
            'conditions'=> ConditionController::getAll(),
            'elementRegistry' => $elements,
            'elementOptions' => $elements->options(),
            'elementDiagramStyles' => $elements->diagramStyles(),
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $data['type'] = $this->validatedType($request->input('type'));
        $data['is_preview'] = true;
        $data['show_save_button'] = $request->boolean('show_save_button');
        $data['show_reminder_button'] = $request->boolean('show_reminder_button');
        $task = Task::create($data);
        if (!$request->parent_id) {
            $task->parent_id = $task->id;
            $task->save();
        }
        ProcessController::processHasError($task->process_id);
        return redirect(route('simpleWorkflow.task.index', ['process_id'=> $task->process_id]));
    }

    public function edit(Task $task)
    {
        $registry = app(ElementRegistry::class);
        return view('SimpleWorkflowView::Core.Task.edit', [
            'task' => $task,
            'taskElement' => $registry->forTask($task),
        ]);
    }

    /** فهرست ستون‌هایی که برای نوع المان این تسک مجاز به‌روزرسانی هستند. */
    protected function updatableFields(Request $request, Task $task)
    {
        $base = [
            'name', 'parent_id', 'next_element_id', 'assignment_type', 'case_name', 'color', 'background',
            'duration', 'order', 'number_of_task_to_back', 'script_before_open', 'allow_cancel', 'is_preview',
            'show_save_button', 'show_reminder_button',
        ];

        // ستون‌های اختصاصی المان (مثل executive_element_id یا فیلدهای زمان‌بندی)
        $elementFields = app(ElementRegistry::class)->settingFieldsFor($task->type);

        $fields = array_values(array_unique(array_merge($base, $elementFields)));

        // فقط ستون‌هایی که واقعاً در مدل وجود دارند و مجاز به‌روزرسانی هستند
        return array_values(array_intersect($fields, (new Task)->getFillable()));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->only($this->updatableFields($request, $task));
        $data['is_preview'] = $request->boolean('is_preview');
        $data['show_save_button'] = $request->boolean('show_save_button');
        $data['show_reminder_button'] = $request->boolean('show_reminder_button');
        $task->update($data);
        return redirect()->back()->with('success', trans('Updated Successfully'));
    }

    public function destroy(Request $request, Task $task)
    {
        $request->validate([
            'transfer_task_id' => 'required|exists:wf_task,id',
        ]);

        $transferTaskId = $request->transfer_task_id;

        $inboxes = InboxController::getAllByTaskId($task->id);
        foreach ($inboxes as $inbox) {
            $inbox->task_id = $transferTaskId;
            $newTask = self::getById($transferTaskId);
            $caseName = InboxController::createCaseName($newTask, $inbox->case_id);
            InboxController::editCaseName($inbox->id, $caseName);
            $inbox->save();
        }

        $processId = $task->process_id;
        $task->delete();

        return redirect()->route('simpleWorkflow.task.index', ['process_id' => $processId])
            ->with('success', trans('fields.Task deleted successfully'));
    }

    public function countTransferInboxes(Request $request, Task $task)
    {
        $request->validate([
            'from_actor' => 'required|exists:users,id',
        ]);

        $count = Inbox::where('task_id', $task->id)
            ->where('actor', $request->from_actor)
            ->open()
            ->count();

        return response()->json(['count' => $count]);
    }

    public function transferInboxes(Request $request, Task $task)
    {
        $request->validate([
            'from_actor' => 'required|exists:users,id',
            'to_actor' => 'required|exists:users,id|different:from_actor',
        ]);

        $query = Inbox::where('task_id', $task->id)
            ->where('actor', $request->from_actor)
            ->open();

        $count = $query->count();

        if ($count === 0) {
            return redirect()->back()->with('error', trans('fields.No open inboxes to transfer'));
        }

        $query->update(['actor' => $request->to_actor]);

        return redirect()->back()->with('success', trans('fields.Inboxes transferred successfully', ['count' => $count]));
    }

    public static function getById($id){
        return Task::find($id);
    }

    public static function getAll(){
        return Task::get();
    }

    public static function getProcessTasks($process_id, $includePreview = true)
    {
        $query = Task::where('process_id', $process_id);
        if (!$includePreview) {
            $query->where(function ($subQuery) {
                $subQuery->where('is_preview', false)
                    ->orWhereNull('is_preview');
            });
        }

        return $query->get();
    }

    public static function getProcessStartTasks($process_id, $includePreview = true)
    {
        $query = Task::where('process_id', $process_id)->whereColumn('id', 'parent_id');

        if (!$includePreview) {
            $query->where(function ($subQuery) {
                $subQuery->where('is_preview', false)
                    ->orWhereNull('is_preview');
            });
        }

        return $query->get();
    }

    public static function TaskHasError($taskId){
        $task = TaskController::getById($taskId);
        if (!$task) {
            return false;
        }
        if ($task->is_preview) {
            return false;
        }

        $errors = app(ElementRegistry::class)->validate($task);

        if (count($errors) > 0) {
            return [
                'hasError' => count($errors),
                'descriptions' => $errors,
            ];
        }

        return false;
    }

    /**
     * بررسی اینکه نوع ارسالی یکی از المان‌های ثبت‌شده در رجیستری هست یا نه.
     */
    protected function validatedType(?string $type): string
    {
        $registry = app(ElementRegistry::class);

        abort_if(!$registry->has($type), 422, 'Unknown element type: ' . (string) $type);

        return $type;
    }

}
