<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\SoftDeletes;
 class Pre_invoices extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_pre_invoices'; 
    protected $fillable = ['case_id', 'case_number', 'name', 'date', 'number', 'has_attachment', 'description', 'file', 'counter_party_id', 'pre_invoice_description', 'has_oppa_sign', 'invoice_issued_date', 'invoice_file', 'pre_invoice_type', 'invoice_type', 'invoice_pdf_file',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function counterParty(){
    return Counter_parties::find($this->counter_party_id);
}}