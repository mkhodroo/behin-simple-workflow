<?php



namespace Behin\SimpleWorkflow\Controllers\Scripts;



use App\Http\Controllers\Controller;

use Behin\SimpleWorkflow\Controllers\Core\VariableController;

use Behin\SimpleWorkflow\Models\Core\Process;

use Behin\SimpleWorkflow\Models\Core\Task;

use Behin\SimpleWorkflow\Models\Core\Variable;

use Illuminate\Http\Request;
use Behin\SimpleWorkflow\Models\Entities\Pre_invoices;
use Behin\SimpleWorkflow\Models\Entities\Case_customer;
use Behin\Sms\Controllers\SmsController;


class PreInvoiceValidation extends Controller

{

    protected $case;

    public function __construct($case) {

        $this->case = $case;

        

    }



    public function execute()

    {

        $case = $this->case;
        $caseCustomer = Case_customer::where('case_number', $case->number)->first();
        $preInvoice = Pre_invoices::where('case_number', $case->number)->first();
        if(!$preInvoice){
            return "اطلاعات پیش فاکتور ثبت نشده است";
        }
        
        $customerMobile = $caseCustomer->mobile;
        $customerMobile = convertPersianToEnglish($customerMobile);
        $customerMobile = preg_replace('/\D/', '', $customerMobile);
        $customerFullname = $caseCustomer->fullname;

        // return $customerMobile;
        if (!preg_match('/^09\d{9}$/', $customerMobile)) {
            return "شماره موبایل باید 11 رقم و با 09 شروع شود";
        }

        if(strlen($customerMobile) != 11){
            return "شماره موبایل مشتری جهت ارسال پیامک باید 11 رقم باشد";
        }
        $shortUrl = $case->getVariable('tracking_url');
        $uniquePart = explode('/s/', $shortUrl);
        $uniquePart = $uniquePart[1] ?? null;
        
        

        if ($customerMobile) {

            $params = array(

                [

                    "name" => "NAME",
                    "value" => $customerFullname

                ],

                [

                    "name" => "CASE_NUMBER",
                    "value" => $case->number

                ],
                [

                    "name" => "UNIQUE_PART",
                    "value" => $uniquePart

                ],

            );

            $result = SmsController::sendByTemp($customerMobile, 133512, $params);
        }

    }



}