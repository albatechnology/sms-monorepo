<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerDiscountUse extends Migration
{
    public function up()
    {
        Schema::create('customer_discount_uses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('discount_id')->constrained();
            $table->unsignedInteger('use_count')->default(0);
            $table->json('order_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_discount_uses');
    }
}