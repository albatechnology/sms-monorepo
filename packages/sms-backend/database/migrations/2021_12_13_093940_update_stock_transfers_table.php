<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->foreignId('stock_from_id')->nullable()->change();
            $table->foreignId('stock_to_id')->nullable()->change();
            $table->foreignId('requested_by_id')->nullable()->change();
            $table->foreignId('approved_by_id')->nullable()->change();
            $table->integer('status')->nullable()->change();

            $table->foreignId('from_channel_id')->constrained('channels');
            $table->foreignId('to_channel_id')->constrained('channels');
            $table->foreignId('product_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->foreignId('stock_from_id')->nullable('false')->change();
            $table->foreignId('stock_to_id')->nullable('false')->change();
            $table->foreignId('requested_by_id')->nullable('false')->change();
            $table->foreignId('approved_by_id')->nullable('false')->change();
            $table->unsignedTinyInteger('status')->nullable('false')->change();

            $table->dropConstrainedForeignId('from_channel_id');
            $table->dropConstrainedForeignId('to_channel_id');
            $table->dropConstrainedForeignId('product_id');
        });
    }
}
