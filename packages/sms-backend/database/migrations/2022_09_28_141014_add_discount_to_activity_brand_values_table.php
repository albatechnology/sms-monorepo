<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountToActivityBrandValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_brand_values', function (Blueprint $table) {
            $table->double('total_discount', 15, 3)->default(0);
            $table->double('total_order_value', 15, 3)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_brand_values', function (Blueprint $table) {
            $table->dropColumn(['total_discount', 'total_order_value']);
        });
    }
}
