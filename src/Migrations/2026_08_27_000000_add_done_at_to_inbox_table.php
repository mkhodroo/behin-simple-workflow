<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wf_inbox', function (Blueprint $table) {
            $table->timestamp('done_at')
                ->nullable()
                ->after('status');
        });
    }

    public function down()
    {
        Schema::table('wf_inbox', function (Blueprint $table) {
            $table->dropColumn('done_at');
        });
    }
};