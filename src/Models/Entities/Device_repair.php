<?php 
namespace Behin\SimpleWorkflow\Models\Entities; 
use Behin\SimpleWorkflow\Controllers\Core\VariableController; 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Str; 
use Illuminate\Database\Eloquent\SoftDeletes;
use BehinUserRoles\Models\User;
use Behin\SimpleWorkflow\Models\Core\Cases;
 class Device_repair extends Model 
{ 
    use SoftDeletes; 
    public $incrementing = false; 
    protected $keyType = 'string'; 
    public $table = 'wf_entity_device_repair'; 
    protected $fillable = ['case_id', 'case_number', 'device_id', 'repairman', 'repair_type', 'repair_subtype', 'repair_start_date', 'repair_start_date_alt', 'repair_pic', 'repairman_assitant', 'repair_report', 'repair_is_approved', 'repair_is_approved_by', 'repair_is_approved_description', 'repair_is_approved_2', 'repair_is_approved_by_2', 'repair_is_approved_description_2', 'repair_is_approved_3', 'repair_is_approved_by_3', 'repair_is_approved_description_3', 'repair_end_date', 'repair_end_date_alt',  'created_by', 'updated_by', 'contributers', ]; 
protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {
                $model->id = $model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }
public function cases(){
            return $this->belongsTo(Cases::class, 'case_number', 'number');
        }
public function repairman(){
    return User::find($this->repairman);
}

public function getRepairTypeTextAttribute()
{
    $value = $this->repair_type;

    // اگه رشته یونیکد باشه
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return implode(', ', $decoded); // اینجا به فارسی می‌بینی
        }
    }

    return $value;
}



public function getRepairSubTypeTextAttribute()
{
    $value = $this->repair_subtype;
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return implode(', ', $decoded); // اینجا به فارسی می‌بینی
        }
    }

    return $value;
}

public function getRepairmanAssitantTextAttribute()
{
    if (is_array($this->repairman_assitant)) {
        $users = User::whereIn('id', $this->repairman_assitant)->get();
        return $users->pluck('name')->implode(', ');
    }

    return User::find($this->repairman_assitant)?->name;
}



public function repairDuration(string $unit = 'hours'): string
{
    $unit_persian_map = [
        'seconds' => 'ثانیه',
        'minutes' => 'دقیقه',
        'hours'   => 'ساعت',
        'days'    => 'روز',
    ];
    // Input validation for unit
    $allowed_units = ['hours', 'minutes', 'seconds', 'days'];
    $unit_lower = strtolower($unit);
    if (!in_array($unit_lower, $allowed_units)) {
        $difference =  "واحد وارد شده نامعتبر است. واحدهای مجاز عبارتند از: " . implode(', ', $allowed_units);
        return $difference;
    }

    // Handle cases where one or both dates might be null
    if ($this->repair_start_date_alt === null || $this->repair_end_date_alt === null) {
        $difference =  'تاریخ شروع یا پایان تعمیر مشخص نشده است.';
        return $difference;
    }

    

    // Calculate the difference in seconds
    $difference_in_seconds = $this->repair_end_date_alt - $this->repair_start_date_alt;

    // Handle cases where the end date might be before the start date
    if ($difference_in_seconds < 0) {
        // Depending on business logic, you might want to return an error,
        // return 0, or return the absolute difference. Here, we return an error.
        $difference =  'تاریخ پایان تعمیر قبل از تاریخ شروع تعمیر است.';
        return $difference;
    }

    // Convert the difference to the desired unit
    $difference = 0;
    switch (strtolower($unit)) {
        case 'seconds':
            $difference = $difference_in_seconds;
            break;
        case 'minutes':
            $difference = $difference_in_seconds / 60;
            break;
        case 'hours':
            $difference = $difference_in_seconds / 3600;
            break;
        case 'days':
            $difference = $difference_in_seconds / (3600 * 24);
            break;
        default:
            // This case should technically not be reached due to earlier validation
            $difference =  'خطای داخلی در محاسبه واحد.';
            return $difference;
    }

    // Return the successful result
    $persian_unit_name = $unit_persian_map[$unit_lower];
    return  $difference . ' ' . $persian_unit_name;
}

public function totalRepairDuration(string $unit = 'hours')
{
    $unit_persian_map = [
        'seconds' => 'ثانیه',
        'minutes' => 'دقیقه',
        'hours'   => 'ساعت',
        'days'    => 'روز',
    ];
    // Input validation for unit
    $allowed_units = ['hours', 'minutes', 'seconds', 'days'];
    $unit_lower = strtolower($unit);
    if (!in_array($unit_lower, $allowed_units)) {
        $difference =  "واحد وارد شده نامعتبر است. واحدهای مجاز عبارتند از: " . implode(', ', $allowed_units);
        return $difference;
    }

    // Handle cases where one or both dates might be null
    if ($this->repair_end_date_alt === null) {
        $difference =  'تاریخ شروع یا پایان تعمیر مشخص نشده است.';
        return $difference;
    }
    

    // Calculate the difference in seconds
    $difference_in_seconds = $this->repair_end_date_alt - ($this->cases->created_at->timestamp * 1000);

    // Handle cases where the end date might be before the start date
    if ($difference_in_seconds < 0) {
        // Depending on business logic, you might want to return an error,
        // return 0, or return the absolute difference. Here, we return an error.
        $difference =  'تاریخ پایان تعمیر قبل از تاریخ پذیرش است.';
        return $difference;
    }

    // Convert the difference to the desired unit
    $difference = 0;
    switch (strtolower($unit)) {
        case 'seconds':
            $difference = $difference_in_seconds / 1000;
            break;
        case 'minutes':
            $difference = $difference_in_seconds / 60 / 1000;
            break;
        case 'hours':
            $difference = $difference_in_seconds / 3600 / 1000;
            break;
        case 'days':
            $difference = $difference_in_seconds / (3600 * 24) / 1000;
            break;
        default:
            // This case should technically not be reached due to earlier validation
            $difference =  'خطای داخلی در محاسبه واحد.';
            return $difference;
    }

    // Return the successful result
    $persian_unit_name = $unit_persian_map[$unit_lower];
    return  round($difference, 2) . ' ' . $persian_unit_name;
}}