<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('order_discounts', function (Blueprint $table) {
        //     $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('discount_id')->constrained()->cascadeOnDelete();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('order_discounts');
    }
}
