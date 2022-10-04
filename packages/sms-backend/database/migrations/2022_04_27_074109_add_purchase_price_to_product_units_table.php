<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchasePriceToProductUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->unsignedInteger('purchase_price')->nullable();
            $table->unsignedBigInteger('calculated_hpp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'calculated_hpp']);
        });
    }
}
