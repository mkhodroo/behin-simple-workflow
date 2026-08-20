<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Cases;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\TaskActor;
use BehinProcessMaker\Controllers\ToDoCaseController;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Controllers\Core\ScriptController;
use App\Events\NewInboxEvent;
use App\Models\User;
use BaleBot\Controllers\BotController;
use Behin\SimpleWorkflow\Jobs\SendPushNotification;
use Behin\SimpleWorkflow\Models\Entities\CasesManual;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Support\Str;
use Behin\SimpleWorkflow\Jobs\SendTaskReminderNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CopyInboxController extends Controller
{
    public function copy($id)
    {
        $inbox = Inbox::findOrFail($id);

        $newInbox = $inbox->replicate();

        $newInbox->id = (string) Str::uuid();
        $newInbox->created_at = now();
        $newInbox->updated_at = now();

        $newInbox->save();

        return back()->with('success', 'رکورد با موفقیت کپی شد.');
    }
}
