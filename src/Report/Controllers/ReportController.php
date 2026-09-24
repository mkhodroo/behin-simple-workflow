<?php

namespace Behin\SimpleWorkflow\Report\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Morilog\Jalali\Jalalian;

/**
 * گزارش کارتابل تسک‌ها
 * ---------------------------------------------------------------------
 * این بخش کاملا مجزا از سایر فایل‌های پکیج نوشته شده است:
 *   روت      : src/Report/Routes/report.php
 *   کنترلر   : src/Report/Controllers/ReportController.php
 *   ویو      : src/Report/Views/index.blade.php
 * هدف: انتخاب یک تسک از یک فرایند و مشاهده تعداد کارهای موجود در آن
 * تسک و لیست کاربرانی که این کارها در کارتابل آن‌ها قرار دارد.
 */
class ReportController extends Controller
{
    /** حداکثر تعداد پرونده‌ای که برای هر کاربر در جزییات نمایش داده می‌شود */
    const CASES_PER_USER = 10;

    /** حداکثر تعداد رکوردی که برای ساخت جزییات خوانده می‌شود */
    const DETAIL_ROW_LIMIT = 5000;

    /**
     * صفحه گزارش: انتخاب فرایند/تسک + نمایش نتیجه
     */
    public function index(Request $request): View
    {
        $processes = Process::orderBy('name')->get();
        $processId = $request->input('process_id');
        $taskId = $request->input('task_id');

        $tasks = $processId ? $this->tasksOfProcess($processId) : collect();
        // کارهای کارتابل فقط از جنس فرم هستند؛ تسک‌های غیر فرم (اسکریپت، شرط و ...) نادیده گرفته می‌شوند
        $task = $taskId ? Task::with('process')->where('type', 'form')->find($taskId) : null;

        // اگر تسک انتخاب شده متعلق به فرایند انتخاب شده نباشد، نادیده گرفته می‌شود
        if ($task && $processId && $task->process_id !== $processId) {
            $task = null;
        }

        return view('SimpleWorkflowReportView::index', [
            'processes' => $processes,
            'tasks' => $tasks,
            'selectedProcessId' => $processId,
            'task' => $task,
            'report' => $task ? $this->buildReport($task) : null,
        ]);
    }

    /**
     * لیست تسک‌های یک فرایند به صورت JSON (برای انتخاب‌گر وابسته به فرایند)
     */
    public function tasks(Request $request): JsonResponse
    {
        $processId = $request->input('process_id');
        $tasks = $processId ? $this->tasksOfProcess($processId) : collect();

        return response()->json([
            'status' => 200,
            'tasks' => $tasks->map(function (Task $task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'type' => $task->type,
                    'is_preview' => (bool) $task->is_preview,
                ];
            })->values(),
        ]);
    }

    /**
     * تسک‌های قابل نمایش در کارتابل (فقط تسک‌های فرم، با ترتیب نام)
     */
    private function tasksOfProcess(string $processId)
    {
        return collect(TaskController::getProcessTasks($processId))
            ->filter(function (Task $task) {
                return $task->type === 'form';
            })
            ->sortBy(function (Task $task) {
                return $task->name;
            })
            ->values();
    }

    /**
     * ساخت داده‌های گزارش برای یک تسک
     */
    private function buildReport(Task $task): array
    {
        $openStatuses = $this->openStatuses();
        $statusLabels = $this->statusLabels();

        // تعداد کارهای تسک به تفکیک وضعیت
        $countsByStatus = Inbox::where('task_id', $task->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statuses = [];
        $openTotal = 0;
        foreach ($statusLabels as $status => $info) {
            $count = (int) ($countsByStatus[$status] ?? 0);
            $statuses[] = [
                'status' => $status,
                'label' => $info['label'],
                'type' => $info['type'],
                'count' => $count,
            ];
            if ($info['type'] === 'open') {
                $openTotal += $count;
            }
        }

        // وضعیت‌های ثبت شده در دیتابیس که در کانفیگ وجود ندارند
        $knownStatuses = collect($statuses)->pluck('status')->all();
        foreach ($countsByStatus as $status => $count) {
            if (!in_array($status, $knownStatuses, true)) {
                $statuses[] = [
                    'status' => $status,
                    'label' => $status === '' ? 'نامشخص' : $status,
                    'type' => 'other',
                    'count' => (int) $count,
                ];
            }
        }

        // کارهای باز بدون کاربر (کارتابل سیستمی)
        $withoutActor = Inbox::where('task_id', $task->id)
            ->whereIn('status', $openStatuses)
            ->whereNull('actor')
            ->count();

        // تجمیع کاربران دارای کار در کارتابل
        $userRows = Inbox::where('task_id', $task->id)
            ->whereIn('status', $openStatuses)
            ->whereNotNull('actor')
            ->select(
                'actor',
                DB::raw('count(*) as jobs'),
                DB::raw('min(created_at) as first_job_at'),
                DB::raw('max(created_at) as last_job_at')
            )
            ->groupBy('actor')
            ->orderByDesc('jobs')
            ->get();

        $users = User::whereIn('id', $userRows->pluck('actor')->unique()->all())
            ->get()
            ->keyBy('id');

        // جزییات پرونده‌های باز هر کاربر
        $details = collect();
        if ($openTotal > 0) {
            $details = Inbox::with('case')
                ->where('task_id', $task->id)
                ->whereIn('status', $openStatuses)
                ->whereNotNull('actor')
                ->orderBy('created_at')
                ->limit(self::DETAIL_ROW_LIMIT)
                ->get()
                ->groupBy('actor');
        }

        $rows = $userRows->map(function ($row) use ($users, $details, $openTotal) {
            $user = $users->get($row->actor);
            $inboxes = $details->get($row->actor, collect());

            return [
                'id' => $row->actor,
                'name' => $user ? $user->name : 'کاربر شناسه ' . $row->actor,
                'number' => $user ? $user->number : null,
                'jobs' => (int) $row->jobs,
                'share' => $openTotal > 0 ? round($row->jobs * 100 / $openTotal, 1) : 0,
                'first_job_jalali' => $this->jalali($row->first_job_at),
                'last_job_jalali' => $this->jalali($row->last_job_at),
                'waiting_days' => $row->first_job_at
                    ? (int) Carbon::parse($row->first_job_at)->diffInDays(now())
                    : null,
                'cases' => $inboxes->take(self::CASES_PER_USER)->map(function (Inbox $inbox) {
                    return [
                        'inbox_id' => $inbox->id,
                        'case_number' => $inbox->case ? $inbox->case->number : null,
                        'name' => $inbox->case_name ?: ($inbox->case ? $inbox->case->name : null),
                        'created_at' => $this->jalali($inbox->created_at),
                    ];
                })->values()->all(),
                'more_cases' => max($inboxes->count() - self::CASES_PER_USER, 0),
            ];
        })->values();

        $totalInboxes = (int) $countsByStatus->sum();

        return [
            'statuses' => $statuses,
            'open_statuses' => $openStatuses,
            'open_total' => $openTotal,
            'total_inboxes' => $totalInboxes,
            'closed_total' => max($totalInboxes - $openTotal, 0),
            'user_count' => $rows->count(),
            'without_actor' => $withoutActor,
            'oldest_job_jalali' => $this->jalali(
                Inbox::where('task_id', $task->id)->whereIn('status', $openStatuses)->min('created_at')
            ),
            'users' => $rows->all(),
            'detail_limit' => self::DETAIL_ROW_LIMIT,
            'detail_truncated' => $openTotal > self::DETAIL_ROW_LIMIT,
        ];
    }

    /**
     * وضعیت‌هایی که به معنی باز بودن کار در کارتابل کاربر هستند
     */
    private function openStatuses(): array
    {
        return collect(config('workflow.inboxStatus'))
            ->where('type', 'open')
            ->keys()
            ->values()
            ->all();
    }

    /**
     * برچسب فارسی و نوع وضعیت‌ها (کاملا مجزا از فایل‌های زبان پکیج)
     */
    private function statusLabels(): array
    {
        $faLabels = [
            'new' => 'جدید',
            'opened' => 'باز شده',
            'inProgress' => 'در حال انجام',
            'draft' => 'پیش‌نویس',
            'done' => 'انجام شده',
            'doneByOther' => 'انجام شده توسط دیگران',
            'doneBySystem' => 'انجام شده سیستمی',
            'canceled' => 'لغو شده',
            '' => 'نامشخص',
        ];

        $result = [];
        foreach ((array) config('workflow.inboxStatus') as $status => $info) {
            $result[$status] = [
                'label' => isset($faLabels[$status]) ? $faLabels[$status] : $status,
                'type' => isset($info['type']) ? $info['type'] : 'open',
            ];
        }

        return $result;
    }

    /**
     * تبدیل تاریخ میلادی به تاریخ شمسی
     */
    private function jalali($date, string $format = 'Y-m-d H:i'): ?string
    {
        if (!$date) {
            return null;
        }

        return Jalalian::fromCarbon(Carbon::parse($date))->format($format);
    }
}
