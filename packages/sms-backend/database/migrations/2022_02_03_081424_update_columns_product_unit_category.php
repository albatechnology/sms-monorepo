<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsProductUnitCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->unsignedSmallInteger('product_unit_category')->nullable()->change();
        });
        Schema::table('product_units', function (Blueprint $table) {
            $table->unsignedSmallInteger('product_unit_category')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn(['product_unit_category']);
        });
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn(['product_unit_category']);
        });
    }
}
