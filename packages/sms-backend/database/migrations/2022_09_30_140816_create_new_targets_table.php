<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('new_targets', function (Blueprint $table) {
        //     $table->id();
        //     $table->morphs('model');
        //     $table->string('name')->nullable();
        //     $table->smallInteger('target_id')->nullable();
        //     $table->string('target_name', 100)->nullable();
        //     $table->unsignedSmallInteger('type');
        //     $table->unsignedBigInteger('target')->default(0);
        //     $table->unsignedBigInteger('value')->default(0);
        //     $table->dateTime('start_date')->index();
        //     $table->dateTime('end_date')->index();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('new_targets');
    }
}
