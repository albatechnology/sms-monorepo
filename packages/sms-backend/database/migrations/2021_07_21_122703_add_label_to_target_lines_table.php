<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLabelToTargetLinesTable extends Migration
{
    public function up()
    {
        Schema::table('target_lines', function (Blueprint $table) {
            $table->string('label');
        });
    }

    public function down()
    {
        Schema::table('target_lines', function (Blueprint $table) {
            $table->dropColumn(['label']);
        });
    }
}