<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityBrandTable extends Migration
{
    public function up()
    {
        Schema::create('activity_product_brand', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_brand_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('estimated_value')->default(0);
            $table->unsignedInteger('order_value')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_product_brand');
    }
}
