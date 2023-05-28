<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyUserPivotTable extends Migration
{
    public function up()
    {
        // Schema::create('company_user', function (Blueprint $table) {
        //     $table->unsignedBigInteger('user_id');
        //     $table->foreign('user_id', 'user_id_fk_3366005')->references('id')->on('users')->onDelete('cascade');
        //     $table->unsignedBigInteger('company_id'); // unused
        //     $table->foreign('company_id', 'company_id_fk_3366005')->references('id')->on('companies')->onDelete('cascade');
        // });
    }
}
