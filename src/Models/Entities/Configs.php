<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
 class Configs extends Model 
{ 
    public $table = 'wf_entity_configs'; 
    protected $fillable = ['key', 'value', ]; 
}