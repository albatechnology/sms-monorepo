<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAccountsTable extends Migration
{
    public function up()
    {
        // Schema::create('company_accounts', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('name');
        //     $table->string('bank_name')->nullable();
        //     $table->string('account_name')->nullable();
        //     $table->string('account_number')->nullable();
        //     $table->foreignId('company_id')->nullable();
        //     $table->softDeletes();
        //     $table->timestamps();
        // });
    }

    public function down()
    {
        // Schema::dropIfExists('company_accounts');
    }
}
