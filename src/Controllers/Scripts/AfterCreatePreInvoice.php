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
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Carbon;

class AfterCreatePreInvoice extends Controller
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
        $today = Carbon::today()->format('Y-m-d');
        $todayShamsi = toJalali(Carbon::today());
        // return $row->unit_price;
        $row->description = $request->pre_invoice_description;
        $row->number = '2'. $todayShamsi->format('y') . $todayShamsi->format('m'). $todayShamsi->format('d'). $case->number;
        // return $row->price;
        $row->save();
    }

}