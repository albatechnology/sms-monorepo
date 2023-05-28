<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('stock')->default(0);
            $table->foreignId('channel_id')->constrained();
            // $table->foreignId('company_id')->constrained(); // unused
            $table->foreignId('product_id')->constrained();
            $table->timestamps();

            $table->unique(['channel_id', 'product_id']);
        });
    }
}
