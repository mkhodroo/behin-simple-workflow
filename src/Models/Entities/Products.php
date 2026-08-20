<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
 class Products extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_products'; 
    protected $fillable = ['name', 'sku', 'category_id', 'unit', 'description', 'barcode', 'min_stock', 'max_stock',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function balance() {
        $inventoryTransaction = Inventory_transactions::where('product_id', $this->id)->get();
        $totalDebit = $totalCredit = 0;
        foreach($inventoryTransaction as $row){
            if($row->inventory_transaction_type == 'افزایش'){
                $totalDebit += (int) $row->quantity * (int) $row->purchase_price;
            }
            if($row->inventory_transaction_type == 'کاهش'){
                $totalCredit += (int) $row->quantity * (int) $row->purchase_price;
            }
        }
        return $totalDebit - $totalCredit;
    }
    
public function remainder() {
    $inventoryTransaction = Inventory_transactions::where('product_id', $this->id)->get();
    $increase = $decrease = 0;
    foreach($inventoryTransaction as $row){
        if($row->inventory_transaction_type == 'افزایش'){
            $increase += (int) $row->quantity;
        }
        if($row->inventory_transaction_type == 'کاهش'){
            $decrease += (int) $row->quantity;
        }
    }
    return $increase - $decrease;
}}