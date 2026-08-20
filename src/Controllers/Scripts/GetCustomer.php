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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Configs;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Carbon;
use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
use Morilog\Jalali\Jalalian;
use Behin\SimpleWorkflow\Models\Entities;



class GetCustomer extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        // $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        $customers = Entities\Customers::where('fullname', 'like', '%' . $request->term . '%')->orWhere('id', 'like', '%' . $request->term . '%')->get();
        $data =[];
        foreach ($customers as $c){
            $data[] = [
                'id' => $c->id,
                'label' => $c->fullname. ' | ' . $c->mobile . ' | ' . $c->address
                ];
        }
        return response()->json($data);
    }
}