<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use Illuminate\Http\RedirectResponse;

class DeleteInboxController extends Controller
{


    public function destroy(string $inboxId): RedirectResponse
    {
        $inbox = Inbox::findOrFail($inboxId);

        $caseId = $inbox->case_id;

        $inbox->delete(); // Soft Delete

        return redirect()
            ->back()
            ->with('success', 'رکورد تاریخچه با موفقیت حذف شد.');
    }
}
