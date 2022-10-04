<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTargetPivotTable extends Migration
{
    public function up()
    {
        Schema::create('order_target', function (Blueprint $table) {
            $table->unsignedBigInteger('target_id');
            $table->foreign('target_id', 'target_id_fk_3361247')->references('id')->on('targets')->onDelete('cascade');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id', 'order_id_fk_3361247')->references('id')->on('orders')->onDelete('cascade');
        });
    }
}
