<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProductListsTable extends Migration
{
    public function up()
    {
        Schema::create('product_product_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('product_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_product_list');
    }
}