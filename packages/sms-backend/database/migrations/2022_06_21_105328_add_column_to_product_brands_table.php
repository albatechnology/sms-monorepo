<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToProductBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_brands', function (Blueprint $table) {
            $table->boolean('show_in_moves')->default(0);
            $table->boolean('show_in_sms')->default(0);
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
            $table->dropColumn(['show_in_moves', 'show_in_sms']);
        });
    }
}
