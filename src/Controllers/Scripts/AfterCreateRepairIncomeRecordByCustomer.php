<?php



namespace Behin\SimpleWorkflow\Controllers\Scripts;



use App\Http\Controllers\Controller;

use Behin\SimpleWorkflow\Controllers\Core\CaseController;

use Behin\SimpleWorkflow\Controllers\Core\InboxController;

use Behin\SimpleWorkflow\Controllers\Core\ProcessController;

use Behin\SimpleWorkflow\Controllers\Core\TaskController;

use Behin\SimpleWorkflow\Controllers\Core\VariableController;

use Behin\SimpleWorkflow\Models\Core\Process;

use Behin\SimpleWorkflow\Models\Core\Task;

use Behin\SimpleWorkflow\Models\Core\Variable;

use Behin\SimpleWorkflow\Models\Core\Cases;

use Behin\SimpleWorkflow\Models\Entities\Repair_reports;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use Behin\SimpleWorkflow\Models\Entities\Configs;

use Behin\SimpleWorkflow\Models\Entities\Transactions;
use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
use Behin\SimpleWorkflow\Models\Entities\Customers;
use BaleBot\Controllers\TelegramController;

class AfterCreateRepairIncomeRecordByCustomer extends Controller
{
    private $case;
    public function __construct()
    {
        // $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)

    {
        $case = CaseController::getById($request->caseId);
        $viewModel = ViewModelController::getById($request->viewModelId);
        
        $model = ViewModelController::getModelById($viewModel->id);

        $row = $model::findOrNew($request->rowId);
        
        $bale = new TelegramController();

        $bale->sendMessage([
            'chat_id' => '681208098',
            'text' => "رسید پرداخت پرونده " . $case->number . " بارگذاری شد." . $row->payment_receipt(),
        ]);
        $bale->sendDocument([
            'chat_id'  => '1347076498',
            'document' => curl_file_create($row->payment_receipt()),
            'caption'  => "رسید پرداخت پرونده " . $case->number . " بارگذاری شد.",
        ]);
        
    }

}