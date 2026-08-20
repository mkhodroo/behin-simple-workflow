<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Behin\SimpleWorkflow\Models\Entities\Products;
use Illuminate\Database\Eloquent\SoftDeletes;
 class Inventory_items extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_inventory_items'; 
    protected $fillable = ['warehouse_id', 'warehouse_name', 'product_id', 'product_name', 'quantity', 'purchase_price', 'purchase_price_unit', 'registerer', 'change_type', 'case_id', 'case_number',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function product(){
    return Products::find($this->product_id);
}}