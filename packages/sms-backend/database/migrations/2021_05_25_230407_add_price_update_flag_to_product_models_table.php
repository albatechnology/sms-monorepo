<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceUpdateFlagToProductModelsTable extends Migration
{
    public function up()
    {
        // flag to indicate that this model need price range refresh
        Schema::table('product_models', function (Blueprint $table) {
            $table->boolean('need_price_range_update')->default(0);
        });
    }

    public function down()
    {
        Schema::table('product_models', function (Blueprint $table) {
            $table->dropColumn(['need_price_range_update']);
        });
    }
}