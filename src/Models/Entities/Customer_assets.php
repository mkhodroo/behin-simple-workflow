<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\SoftDeletes;
 class Customer_assets extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_customer_assets'; 
    protected $fillable = ['case_id', 'case_number', 'customer_id', 'high_power', 'low_power', 'welding_gun', 'cutting_head', 'high_power_cutting', 'air_cooled_welding', 'water_cooled_welding', 'power', '20_w', '30_w', '50_w', '0_5_kw', '0_75_kw', '1_kw', '2_kw', '3_kw', '4_kw', '5_kw', '6_kw', '12_kw',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
}