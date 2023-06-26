<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('subscribtion_user_id')->constrained('subscribtion_users');
            $table->string('name')->nullable();
            // $table->foreignId('company_id')->constrained(); // unused
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
