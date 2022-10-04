<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisorTypesTable extends Migration
{
    public function up()
    {
        Schema::create('supervisor_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code');
            $table->integer('level')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
