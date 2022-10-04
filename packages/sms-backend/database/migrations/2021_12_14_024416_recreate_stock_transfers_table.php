<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropForeign(['stock_from_id']);
            $table->dropForeign(['stock_to_id']);
            $table->dropForeign(['requested_by_id']);
            $table->dropForeign(['approved_by_id']);
            $table->dropColumn(['stock_from_id', 'stock_to_id', 'requested_by_id', 'approved_by_id', 'status']);
        });
    }
}
