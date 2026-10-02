<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * اسکریپتی که بلافاصله پس از استارت شدن فرایند و قبل از ورود به مراحل بعدی اجرا می‌شود.
     */
    public function up(): void
    {
        Schema::table('wf_process', function (Blueprint $table) {
            if (!Schema::hasColumn('wf_process', 'script_before_start')) {
                $table->uuid('script_before_start')->nullable()->after('case_prefix');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wf_process', function (Blueprint $table) {
            if (Schema::hasColumn('wf_process', 'script_before_start')) {
                $table->dropColumn('script_before_start');
            }
        });
    }
};