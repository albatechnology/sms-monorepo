<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetTypePrioritiesTable extends Migration
{
    public function up()
    {
        Schema::create('target_type_priorities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('target_type')->unique();
            $table->unsignedSmallInteger('priority')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('target_type_priorities');
    }
}