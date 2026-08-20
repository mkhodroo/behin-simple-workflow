<?php



namespace Behin\SimpleWorkflow\Controllers\Scripts;



use App\Http\Controllers\Controller;

use Behin\SimpleWorkflow\Controllers\Core\VariableController;

use Behin\SimpleWorkflow\Models\Core\Process;

use Behin\SimpleWorkflow\Models\Core\Task;

use Behin\SimpleWorkflow\Models\Core\Variable;

use Illuminate\Http\Request;
use Behin\SimpleWorkflow\Models\Entities;



class ChangeLastStatusToWaitingForPreInvoiceIssuing extends Controller

{

    protected $case;

    public function __construct($case) {

        $this->case = $case;

        

    }



    public function execute()

    {
        $case = $this->case;
        $repairCost = Entities\Repair_cost::where('case_number', $case->number)->first();
        
        if(!$repairCost){
            return "هیچ ردیف تعیین هزینه ای وجود ندارد";
        }
        
        VariableController::save(

            $this->case->process_id, $this->case->id, 'last_status', 'در انتظار صدور پیش فاکتور'

        );

    }



}