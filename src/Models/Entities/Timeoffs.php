<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\SoftDeletes;
 class Timeoffs extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_timeoffs'; 
    protected $fillable = ['case_id', 'case_number', 'user', 'type', 'duration', 'approved', 'approved_by', 'uniqueId', 'start_year', 'start_month', 'start_day', 'end_year', 'end_month', 'end_day', 'start_timestamp', 'end_timestamp', 'request_timestamp', 'description',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function user(){
    return getUserInfo($this->user);
}

public function case(){
    return Cases::find($this->case_id);
}}