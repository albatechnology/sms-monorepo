<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductListsTable extends Migration
{
    public function up()
    {
        Schema::create('product_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('model');
            $table->json('product_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_lists');
    }
}