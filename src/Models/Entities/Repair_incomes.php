<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Support\HtmlString;use Illuminate\Database\Eloquent\SoftDeletes;use Behin\SimpleWorkflow\Models\Core\Cases;
 class Repair_incomes extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_repair_incomes'; 
    protected $fillable = ['case_id', 'case_number', 'payment_method', 'payment_receipt', 'payment_date', 'payment_amount', 'payment_description', 'transaction_number', 'cheque_number', 'cheque_due_date', 'customer_account_status_image', 'cheque_image', 'income_is_approved',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function amount(){
    if($this->payment_amount){
        if(str_contains(',', $this->payment_amount)){
            return $this->payment_amount;
        }else{
            return number_format($this->payment_amount);
        }
    }
    
    if($this->cheque_amount){
        if(str_contains(',', $this->cheque_amount)){
            return $this->cheque_amount;
        }else{
            return number_format($this->cheque_amount);
        }
    }
}

public function receiptLink(){
    // ✅ HtmlString => ViewModelController خروجی را بدون escape رندر می‌کند
    if (str_contains($this->payment_receipt, 'http')){
        $href = e($this->payment_receipt);
    }
    else{
        $href = e(url('public/' . $this->payment_receipt));
    }

    return new HtmlString("<a href=\"{$href}\" download>" . trans('SimpleWorkflowLang::fields.Download') . "</a>");
}

public function receipt_link(){
    // ✅ نام قرینه بدون فاصله — سازگار با منطق resolveColumnPath در ViewModelController
    return $this->receiptLink();
}

public function payment_receipt(){
    if (str_contains($this->payment_receipt, 'http')){
        return $this->payment_receipt;
    }
    else{
        return 'public/' . $this->payment_receipt;
    }
}

public function case()
{
    return $this->belongsTo(Cases::class, 'case_number', 'number');
    // 'case_number' نام ستون در این جدول است که به case مربوط می‌شود.
    // 'your_case_id_column' نام ستون کلید اصلی در جدول cases است که case_number به آن اشاره دارد (معمولا 'id').
}}