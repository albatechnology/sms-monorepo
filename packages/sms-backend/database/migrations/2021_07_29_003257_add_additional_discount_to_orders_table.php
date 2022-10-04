<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalDiscountToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('additional_discount')->nullable()->default(0);
            $table->smallInteger('approval_status')->default(0);
            $table->foreignId('approved_by')->nullable();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['additional_discount', 'approval_status', 'approved_by']);
        });
    }
}