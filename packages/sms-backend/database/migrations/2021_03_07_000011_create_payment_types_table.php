<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTypesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('subscribtion_user_id')->constrained('subscribtion_users');
            $table->string('name');
            $table->boolean('require_approval')->default(0)->nullable();
            $table->foreignId('payment_category_id')->constrained();
            // $table->foreignId('company_id')->constrained(); // unused
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
