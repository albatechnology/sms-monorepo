<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeedersTable extends Migration
{
    public function up()
    {
        Schema::create('seeders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('seeders');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seeders');
    }
}