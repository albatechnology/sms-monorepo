<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('product_id')->constrained();
            $table->foreignId('order_id')->constrained();
            // $table->foreignId('company_id')->constrained(); // unused

            $table->unsignedInteger('quantity');
            $table->unsignedInteger('quantity_fulfilled')->default(0);
            $table->unsignedTinyInteger('status');

            $table->longText('records')->nullable();

            $table->bigInteger('unit_price');
            $table->bigInteger('total_discount')->default(0);
            $table->bigInteger('total_price')->default(0);
            $table->unsignedBigInteger('total_cascaded_discount')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['order_id', 'status']);
        });
    }
}
