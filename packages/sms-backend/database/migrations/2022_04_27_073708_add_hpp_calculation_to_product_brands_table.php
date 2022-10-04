<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHppCalculationToProductBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_brands', function (Blueprint $table) {
            $table->unsignedTinyInteger('hpp_calculation');
            $table->integer('currency_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_brands', function (Blueprint $table) {
            $table->dropColumn(['hpp_calculation']);
        });
    }
}
