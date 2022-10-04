<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('company_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->foreignId('company_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('company_account_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_account_id');
        });

        Schema::dropIfExists('company_accounts');
    }
}