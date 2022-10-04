<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('items')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();

            $table->foreignId('discount_id')->nullable()->constrained();
            $table->string('discount_error')->nullable();
            $table->unsignedBigInteger('total_discount')->default(0);
            $table->unsignedBigInteger('total_price')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
}