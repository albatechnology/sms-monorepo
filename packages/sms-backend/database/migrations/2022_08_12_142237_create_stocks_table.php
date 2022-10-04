<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            // $table->foreignId('product_unit_id')->constrained();
            $table->integer('product_unit_id')->index();
            $table->integer('stock')->default(0);
            $table->integer('indent')->default(0);
            $table->integer('total_stock')->default(0);
            $table->timestamps();

            $table->unique(['location_id', 'product_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
