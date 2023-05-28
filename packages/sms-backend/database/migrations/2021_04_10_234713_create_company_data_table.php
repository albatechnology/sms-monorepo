<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyDataTable extends Migration
{
    public function up()
    {
        // Schema::create('company_data', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->bigInteger('next_invoice_id')->default(1);
        //     $table->foreignId('company_id')->constrained();
        //     $table->timestamps();
        // });
    }

    public function down()
    {
        // Schema::dropIfExists('company_data');
    }
}
