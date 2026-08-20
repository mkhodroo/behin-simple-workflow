<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Behin\SimpleWorkflow\Models\Entities\Device_repair;

class ChangeLastStatusToWaitingForRepairReport extends Controller
{
    protected $case;
    public function __construct($case) {
        $this->case = $case;
        
    }

    public function execute()
    {
        $case = $this->case;
        $repairman = Device_repair::where('case_id', $case->id)->first()?->repairman;
        if(!$repairman){
            return "تعمیرکار تعریف نشده است";
        }
        $case->saveVariable('repairman', $repairman);
        // $last_status = $this->case->variables()->where('key', 'last_status')->frist()->value;
        VariableController::save(
            $this->case->process_id, $this->case->id, 'last_status', 'در انتظار انجام تعمیرات'
        );
    }

}