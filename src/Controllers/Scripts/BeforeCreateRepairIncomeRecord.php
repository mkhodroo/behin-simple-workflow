<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use Illuminate\Http\Request;

class BeforeCreateRepairIncomeRecord extends Controller
{
    private $case;

    public function __construct()
    {
        // $this->case = CaseController::getById($case->id);
    }

    public static function execute(Request $request)
    {
        if ($request->rowData) {
            $data = $request->rowData;
            if(!$data['payment_method']){
                return response(trans('fields.payment_method is required'), 402);
            }
            if($data['payment_method'] == "نقدی"){
                if(
                    !$data['payment_amount'] or
                    !$data['payment_date'] or
                    !isset($data['payment_receipt'])
                ){
                    return response(trans('fields.payment_amount and payment_date and payment_receipt is required'), 402);
                }
            }
            if($data['payment_method'] == "چک"){
                if(
                    !$data['cheque_number'] or
                    !$data['cheque_due_date'] or
                    !$data['cheque_payee'] or
                    !$data['cheque_amount']
                ){
                    return response(trans('fields.cheque_number and cheque_due_date and cheque_payee and cheque_amount is required'), 402);
                }
            }
            
        }
    }

}