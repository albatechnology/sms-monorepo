<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailTargetPivotTable extends Migration
{
    public function up()
    {
        Schema::create('order_detail_target', function (Blueprint $table) {
            $table->unsignedBigInteger('target_id');
            $table->foreign('target_id', 'target_id_fk_3361248')->references('id')->on('targets')->onDelete('cascade');
            $table->unsignedBigInteger('order_detail_id');
            $table->foreign('order_detail_id', 'order_detail_id_fk_3361248')->references('id')->on('order_details')->onDelete('cascade');
        });
    }
}
