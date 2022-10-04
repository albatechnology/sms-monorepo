<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityProductPivotTable extends Migration
{
    public function up()
    {
        Schema::create('activity_product', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id');
            $table->foreign('activity_id', 'activity_id_fk_3286780')->references('id')->on('activities')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id', 'product_id_fk_3286780')->references('id')->on('products')->onDelete('cascade');
        });
    }
}
