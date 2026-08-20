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

class SetNextTimeMarketing extends Controller
{
    private $case;
    public function __construct($case)
    {
        $this->case = $case;
    }

    public function execute(Request $request = null)

    {
        $next_time = $this->case->getVariable('next_time');
        $dayMinute = 1440;
        $weekMinute = $dayMinute * 7;
        if($next_time == 'یک هفته'){
            $a = 1;
        }
        
        if($next_time == 'دو هفته'){
            $a = 2;
        }
        if($next_time == 'سه هفته'){
            $a = 3;
        }
        if($next_time == 'یک ماه'){
            $a = 4;
        }
        if($next_time == 'دو ماه'){
            $a = 8;
        }
        if($next_time == 'سه ماه'){
            $a = 12;
        }
        $next_time = $a * $weekMinute;
        $this->case->saveVariable('next_time', $next_time);
    }

}