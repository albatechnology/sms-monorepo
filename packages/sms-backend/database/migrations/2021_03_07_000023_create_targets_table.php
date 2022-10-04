<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetsTable extends Migration
{
    public function up()
    {
        // note: this table has been dropped. instead, see
        // 2021_06_23_004512_recreate_report_target_table
        Schema::create('targets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('value');
            $table->string('type');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('name');
            $table->integer('type_identifier')->nullable();
            $table->string('subject');
            $table->string('subject_type');
            $table->string('scope_model')->nullable();
            $table->integer('scope_identifier')->nullable();
            $table->string('value_type');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
