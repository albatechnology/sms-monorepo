<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateReportTargetTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('order_detail_target');
        Schema::dropIfExists('order_target');
        Schema::dropIfExists('targets');
        Schema::dropIfExists('target_schedules');

        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->dateTime('start_date')->index();
            $table->dateTime('end_date')->index();
            $table->morphs('reportable');
            $table->string('reportable_label');
            $table->bigInteger('time_diff')->default(0);
            $table->timestamps();
        });

        Schema::create('targets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('model');
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('type')->index();
            $table->unsignedBigInteger('target')->default(0);
            $table->unsignedBigInteger('value')->default(0);
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('target_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label');
            $table->morphs('model');
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('target')->default(0);
            $table->unsignedBigInteger('value')->default(0);
            $table->timestamps();
        });

        Schema::create('target_maps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('model');
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('value');
            $table->json('context')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('target_maps');
        Schema::dropIfExists('target_lines');
        Schema::dropIfExists('targets');
        Schema::dropIfExists('reports');
    }
}
