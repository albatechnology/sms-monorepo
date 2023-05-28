<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('stock_id')->constrained();
            $table->bigInteger('quantity');

            // We do not use polymorphic here
            $table->unsignedTinyInteger('type');
            $table->foreignId('order_detail_id')->nullable()->constrained();
            $table->foreignId('stock_transfer_id')->nullable()->constrained();

            // closest PIC for this stock transaction
            $table->foreignId('user_id')->constrained();
            // $table->foreignId('company_id')->constrained(); // unused

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_histories');
    }
}
