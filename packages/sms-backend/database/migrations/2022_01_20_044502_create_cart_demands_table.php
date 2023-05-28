<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cart_demands', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->integer('order_id')->nullable();
        //     $table->json('items')->nullable();
        //     $table->foreignId('user_id')->constrained();
        //     $table->foreignId('customer_id')->nullable()->constrained();

        //     $table->foreignId('discount_id')->nullable()->constrained();
        //     $table->string('discount_error')->nullable();
        //     $table->unsignedBigInteger('total_discount')->default(0);
        //     $table->unsignedBigInteger('total_price')->default(0);

        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('cart_demands');
    }
}
