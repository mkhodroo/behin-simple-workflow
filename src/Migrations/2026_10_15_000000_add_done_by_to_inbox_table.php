<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wf_inbox', function (Blueprint $table) {
            if (!Schema::hasColumn('wf_inbox', 'done_by')) {
                $table->unsignedBigInteger('done_by')->nullable()->after('actor');
            }
        });
    }

    public function down()
    {
        Schema::table('wf_inbox', function (Blueprint $table) {
            if (Schema::hasColumn('wf_inbox', 'done_by')) {
                $table->dropColumn('done_by');
            }
        });
    }
};
