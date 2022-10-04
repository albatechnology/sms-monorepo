<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentTypeIdAndSoftdeletesToCustomerDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_deposits', function (Blueprint $table) {
            $table->foreignId('payment_type_id')->constrained();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_deposits', function (Blueprint $table) {
            $table->dropColumn(['payment_type_id']);
        });
    }
}
