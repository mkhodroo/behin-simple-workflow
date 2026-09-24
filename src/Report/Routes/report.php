<?php

use Behin\SimpleWorkflow\Report\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| روت‌های گزارش کارتابل تسک‌ها (کاملا مجزا)
|--------------------------------------------------------------------------
| این فایل هیچ ارتباطی با src/Routes/web.php ندارد و به صورت مستقل
| در SimpleWorkflowProvider بارگذاری می‌شود.
|
| آدرس‌ها:
|   GET  workflow/report          => صفحه گزارش (انتخاب فرایند و تسک)
|   GET  workflow/report/tasks    => لیست تسک‌های یک فرایند (JSON)
*/

Route::middleware(['web', 'auth'])
    ->prefix('workflow/report')
    ->name('simpleWorkflow.report.')
    ->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('tasks', [ReportController::class, 'tasks'])->name('tasks');
    });
