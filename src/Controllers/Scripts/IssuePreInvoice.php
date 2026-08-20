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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use BehinUserRoles\Models\User;
use BehinUserRoles\Controllers\DepartmentController;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use Mkhodroo\Cities\Controllers\CityController;
use Behin\SimpleWorkflow\Models\Entities\Pre_invoices;
use Behin\SimpleWorkflow\Models\Entities\Case_customer;
use Behin\SimpleWorkflow\Models\Entities\Pre_invoice_items;
use NumberToWord\Dictionary;
use NumberToWord\PersianNumberToWords;
 use Behin\SimpleWorkflow\Models\Entities\Counter_parties;



class IssuePreInvoice extends Controller
{
    private $case;
    public function __construct($case = null)
    {
        // $this->case = CaseController::getById($case->id);
    }

    public function execute(Request $request = null)
    {
        // $case = $this->case;
        $caseId = $request->caseId;
        $case = CaseController::getById($caseId);
        $caseCustomer = Case_customer::where('case_id', $case->id)->orWhere('case_number', $case->number)->first();
        $preInvoice = Pre_invoices::where('case_id', $case->id)->first();
        if(!$preInvoice){
            return response(trans('fields.pre invoice info not found'), 402);
        }
        if(is_null($preInvoice->pre_invoice_type)){
            return response(trans('fields.pre invoice type is required'), 402);
        }
        if(is_null($preInvoice->counter_party_id)){
            return response(trans('fields.pre invoice counter party is required'), 402);
        }
        $preInvoiceItems = Pre_invoice_items::where('case_number', $case->number)->orderBy('updated_at', 'asc')->get();
        $counterParty = Counter_parties::find($preInvoice->counter_party_id);
        if(!count($preInvoiceItems)){
            return response("آیتم های پیش فاکتور خالی است", 402);
        }
        $phpword = new TemplateProcessor(public_path( 'pre_invoice_non_official.docx'));
        
        if($preInvoice->pre_invoice_type == 'پیش فاکتور رسمی بدون ارزش افزوده'){
            $phpword = new TemplateProcessor(public_path( 'official_pre_invoice.docx'));
        }
        
        if($preInvoice->pre_invoice_type == 'پیش فاکتور رسمی با ارزش افزوده'){
            $phpword = new TemplateProcessor(public_path( 'official_pre_invoice_with_added_value.docx'));
        }
        
        $phpword->setValue('date', $preInvoice->date);
        $phpword->setValue('number', $preInvoice->number);
        $phpword->setValue('has_attachment', $preInvoice->has_attachment);
        $phpword->setValue('description', $preInvoice->description);
        $phpword->setValue('account_owner_name', $counterParty->account_owner_name);
        $phpword->setValue('card_number', $counterParty->card_number);
        $phpword->setValue('account_number', $counterParty->account_number);
        $phpword->setValue('sheba_number', $counterParty->sheba_number);
        $phpword->setValue('bank_name', $counterParty->bank_name);

        
        $phpword->setValue('c_name', $caseCustomer->fullname);
        $phpword->setValue('c_mobile', $caseCustomer->mobile);
        $phpword->setValue('c_address', $caseCustomer->address);
        if(in_array($preInvoice->pre_invoice_type, ['پیش فاکتور رسمی بدون ارزش افزوده', 'پیش فاکتور رسمی با ارزش افزوده']) ){
            $phpword->setValue('c_postal_code', $caseCustomer->postal_code ?? '');
            $phpword->setValue('c_eco_number', $caseCustomer->eco_number ?? '');
            $phpword->setValue('c_national_id', $caseCustomer->national_id ?? '');
        }
        $totalPrice = 0;
        for($i=1;$i<=6;$i++){
            if (isset($preInvoiceItems[$i - 1])) {
                $item = $preInvoiceItems[$i-1];
                $phpword->setValue('item_' . $i , $item->name);
                $phpword->setValue('unit_price_' . $i , $item->unit_price);
                $phpword->setValue('no' . $i , $item->number);
                $phpword->setValue('price_' . $i , number_format($item->price));
                $totalPrice += (int)str_replace(',', '', $item->price);
            }else{
                $phpword->setValue('item_' . $i , '');
                $phpword->setValue('unit_price_' . $i , '');
                $phpword->setValue('no' . $i , '');
                $phpword->setValue('price_' . $i , '');
            }
        }
        
          
        $dictionary = new Dictionary();
        $converter = new PersianNumberToWords($dictionary);
        $totalPriceWord =  $converter->convert($totalPrice);
        $phpword->setValue('total_price', number_format($totalPrice));
        if($preInvoice->pre_invoice_type == 'پیش فاکتور رسمی با ارزش افزوده'){
            $addedValue = 0.1 * $totalPrice;
            $phpword->setValue('added_value', number_format($addedValue));
            $totalPayment = $addedValue + $totalPrice;

            $dictionary = new Dictionary();
            $converter = new PersianNumberToWords($dictionary);
            $totalPaymentWord =  $converter->convert($totalPayment);
            $phpword->setValue('total_payment', number_format($totalPayment));
            $phpword->setValue('total_payment_phrase', $totalPaymentWord);
        }
        $phpword->setValue('total_price_phrase', $totalPriceWord);
        if($preInvoice->has_oppa_sign == "باشد"){
            if(in_array($request->type, ['legal', 'legal_with_added_value']) ){
                $image = public_path('oppa_sign.png');
            }else{
                $image = public_path('non_official_sign.png');
            }
            $phpword->setImageValue('sign', [
                'path' => $image,
                'width' => 226,
                'height' => 168,
              ]);
        }else{
            $phpword->setValue('sign', '');
        }
        
        $preInvociceFileName = "simpleWorkflow/$preInvoice->name" . "-$case->number" . ".docx";
        $phpword->saveAs(public_path($preInvociceFileName));
        
        $preInvoice->file = $preInvociceFileName;
        $preInvoice->save();
        return url('public/'. $preInvoice->file);
        
    }
}