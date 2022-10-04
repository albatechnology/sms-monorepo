<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromoIdToDiscountsTable extends Migration
{
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->foreignId('promo_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn(['promo_id']);
        });
    }
}