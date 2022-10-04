<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionCostToProductUnitsTable extends Migration
{
    public function up()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->unsignedBigInteger('production_cost')->default(0);
        });
    }

    public function down()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn(['product_units']);
        });
    }
}