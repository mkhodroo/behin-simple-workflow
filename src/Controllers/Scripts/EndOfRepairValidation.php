<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Task;
use Behin\SimpleWorkflow\Models\Core\Variable;
use Illuminate\Http\Request;
use Behin\SimpleWorkflow\Models\Entities;

class EndOfRepairValidation extends Controller
{
    protected $case;
    public function __construct($case) {
        $this->case = $case;
        
    }

    public function execute()
    {
        $case = $this->case;
        $repairReport = Entities\Device_repair::where('case_number', $case->number)->first();
        
        if(!$repairReport){
            return "فیلدهای موجود در جدول گزارش تعمیر را کامل کنید.";
        }
        
        if(!$repairReport->repair_type){
            return "نوع تعمیر را وارد کنید";
        }
        
        if(!$repairReport->repair_subtype){
            return "جزئیات نوع تعمیر را وارد کنید";
        }
        
        if(!$repairReport->repair_start_date){
            return "تاریخ شروع تعمیر را وارد کنید";
        }
        
        if(!$repairReport->repair_end_date){
            return "تاریخ پایان تعمیر را وارد کنید";
        }
        
    }

}