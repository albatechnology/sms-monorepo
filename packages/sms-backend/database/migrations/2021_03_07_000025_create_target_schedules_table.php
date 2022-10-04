<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('target_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('duration_type');
            $table->date('start_date');
            $table->string('target_name')->nullable();
            $table->integer('value');
            $table->string('target_type');
            $table->string('target_subject');
            $table->string('target_subject_type');
            $table->string('custom_scope_json')->nullable();
            $table->integer('target_type_identifier')->nullable();
            $table->string('target_scope_model')->nullable();
            $table->integer('target_scope_identifier')->nullable();
            $table->string('target_value_type');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
