<?php



namespace Behin\SimpleWorkflow\Controllers\Scripts;



use App\Http\Controllers\Controller;

use Behin\SimpleWorkflow\Controllers\Core\VariableController;

use Behin\SimpleWorkflow\Models\Core\Process;

use Behin\SimpleWorkflow\Models\Core\Task;

use Behin\SimpleWorkflow\Models\Core\Variable;

use Behin\Sms\Controllers\SmsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Behin\SimpleWorkflow\Models\Entities\Case_customer;
use Behin\SimpleWorkflow\Models\Core\Inbox;
use ShortenerUrl\Shortener\Http\Controllers\ShortLinkController;



class SendReceptionSmsToCustomer extends Controller

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

        $customerMobile = $caseCustomer->mobile;

        $customerMobile = convertPersianToEnglish($customerMobile);
        $customerMobile = preg_replace('/\D/', '', $customerMobile);
        $customerFullname = $caseCustomer->fullname;

        // return $customerMobile;
        if (!preg_match('/^09\d{9}$/', $customerMobile)) {
            return "شماره موبایل باید 11 رقم و با 09 شروع شود";
        }

        if ($customerMobile) {
            $inbox = Inbox::where('task_id', '36020682-a32b-4d71-93fd-bfdafea3026f')->where('case_id', $case->id)->first();
            if($inbox){
                if($case->getVariable('tracking_url')){
                    $shortUrl = $case->getVariable('tracking_url');
                }else{
                    $url = route('simpleWorkflow.inbox.view', ['inboxId' => $inbox->id]);
                    $shortUrl = ShortLinkController::make($url);
                    $case->saveVariable('tracking_url', $shortUrl);
                }
                
                $uniquePart = explode('/s/', $shortUrl);
                $uniquePart = $uniquePart[1] ?? null;
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
                        'name' => "UNIQUE_PART",
                        "value" => $uniquePart
                    ]
                );
                $resultCustomer = SmsController::sendByTemp($customerMobile, 223118, $params);
                
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
                        'name' => "UNIQUE_PART",
                        "value" => $uniquePart
                    ]
                );
                // Log::info($params);
                // sleep(2);
                //پیامک برای اقای سیفی
                // $resultAdmin = SmsController::sendByTemp("09330340134", 223118, $params);
                // return $result;
            }
        }

    }

}