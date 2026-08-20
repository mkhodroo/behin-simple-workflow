<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
 class Inventory_transactions extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_inventory_transactions'; 
    protected $fillable = ['product_id', 'warehouse_id', 'quantity', 'reference_type', 'reference_id', 'note', 'inventory_transaction_type', 'purchase_price', 'sale_price', 'case_id', 'case_number', 'date', 'date_alt',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function productName(){
    return Products::find($this->product_id)?->name ?? 'محصول یافت نشد';
}

public function product(){
    return $this->belongsTo(Products::class, 'product_id', 'id');
}

public function warehouseName(){
    return Warehouses::find($this->warehouse_id)?->name ?? 'انبار یافت نشد';
}}