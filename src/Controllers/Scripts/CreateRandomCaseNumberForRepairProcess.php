<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Core\CaseNumbering;
use Morilog\Jalali\Jalalian;

class CreateRandomCaseNumberForRepairProcess
{
    private $case;

    public function __construct($case)
    {
        $this->case = $case;   // شماره پرونده از قبل تولید شده است
    }

    public function execute()
    {
        $case = $this->case;

        // شماره تولیدشده را می‌بینی، مثلاً "RPR-0007"
        $current = $case->number;

        // --- اینجا منطق خودت را بنویس ---
        // مثال: تبدیل به ساختار دلخواه
        $now = Jalalian::now();
        $year = $now->format('y');
        $month = $now->format('m');
        $newNumber = $year . $month . rand(10,99);

        // اگر شماره از قبل null بود (draft) یعنی تولید نشده
        if (!$current) {
            $newNumber = CaseController::getNewCaseNumber($case->process_id);
        }

        $case->number = $newNumber;
        $case->save();

        // می‌توانی مقدار قبلی را هم به عنوان متغیر پرونده ذخیره کنی
        $case->saveVariable('previousCaseNumber', $current);
    }
}