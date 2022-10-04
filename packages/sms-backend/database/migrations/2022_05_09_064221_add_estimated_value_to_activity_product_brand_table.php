<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstimatedValueToActivityProductBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_product_brand', function (Blueprint $table) {
            $table->unsignedInteger('estimated_value')->default(0);
            $table->unsignedInteger('order_value')->default(0);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->integer('parent_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_product_brand', function (Blueprint $table) {
            $table->dropColumn(['estimated_value', 'order_value']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['parent_id']);
        });
    }
}
