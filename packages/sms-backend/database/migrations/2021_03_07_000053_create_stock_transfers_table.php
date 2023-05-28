<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('stock_from_id')->constrained('stocks');
            $table->foreignId('stock_to_id')->constrained('stocks');

            $table->foreignId('requested_by_id')->constrained('users');
            $table->foreignId('approved_by_id')->constrained('users');

            $table->unsignedInteger('amount');
            $table->unsignedTinyInteger('status');
            // $table->foreignId('company_id')->constrained(); // unused

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
