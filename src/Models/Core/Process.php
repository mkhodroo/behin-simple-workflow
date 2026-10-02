<?php

namespace Behin\SimpleWorkflow\Models\Core;

use Behin\SimpleWorkflow\Controllers\Core\TaskController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Process extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    public $table = 'wf_process';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'name',
        'number_of_error',
        'report_form_id',
        'category',
        'case_prefix',
        'script_before_start',
    ];

    function cases(){
        return $this->hasMany(Cases::class);
    }

    /** اسکریپتی که در لحظه استارت شدن فرایند اجرا می‌شود */
    function scriptBeforeStart()
    {
        return $this->belongsTo(Script::class, 'script_before_start');
    }

    function tasks($includePreview = true){
        return TaskController::getProcessTasks($this->id, $includePreview);
    }

    function startTasks($includePreview = true){
        return TaskController::getProcessStartTasks($this->id, $includePreview);
    }
}
