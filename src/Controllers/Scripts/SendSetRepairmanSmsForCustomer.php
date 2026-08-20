<?php



namespace Behin\SimpleWorkflow\Controllers\Scripts;



use App\Http\Controllers\Controller;

use Behin\SimpleWorkflow\Controllers\Core\VariableController;

use Behin\SimpleWorkflow\Models\Core\Process;

use Behin\SimpleWorkflow\Models\Core\Task;

use Behin\SimpleWorkflow\Models\Core\Variable;

use Behin\Sms\Controllers\SmsController;

use Illuminate\Http\Request;

use Behin\SimpleWorkflow\Models\Entities\Case_customer;
use Behin\SimpleWorkflow\Models\Entities\Device_repair;



class SendSetRepairmanSmsForCustomer extends Controller

{

    protected $case;

    public function __construct($case)

    {

        $this->case = $case;

        // return VariableController::save(

        //     $this->case->process_id, $this->case->id, 'manager', 2

        // );

    }



    public function execute()

    {

        $case = $this->case;

        $caseNumber = $this->case->number;

        $variables = $this->case->variables();

        $caseCustomer = Case_customer::where('case_number', $case->number)->first();
        
        $deviceRepair = Device_repair::where('case_number', $case->number)->first();

        $customerMobile = $caseCustomer->mobile;
        $customerMobile = convertPersianToEnglish($customerMobile);
        $customerMobile = preg_replace('/\D/', '', $customerMobile);
        $customerFullname = $caseCustomer->fullname;
        
        $repairman = getUserInfo($deviceRepair?->repairman);

        // return $customerMobile;
        if (!preg_match('/^09\d{9}$/', $customerMobile)) {
            return "شماره موبایل باید 11 رقم و با 09 شروع شود";
        }

        if(strlen($customerMobile) != 11){
            return "شماره موبایل مشتری جهت ارسال پیامک باید 11 رقم باشد";
        }
        
        if(!$repairman?->name){
            return "تعمیرکار به درستی انتخاب نشده است";
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
                    "value" => $caseNumber

                ],
                [

                    "name" => "REPAIRMAN",
                    "value" => $repairman?->name

                ],
                [

                    "name" => "UNIQUE_PART",
                    "value" => $uniquePart

                ],

            );

            $result = SmsController::sendByTemp($customerMobile, 648833, $params);
            
            //پیامک برای اقای سیفی
            // $result = SmsController::sendByTemp("09330340134", 223118, $params);

        }
    }

}