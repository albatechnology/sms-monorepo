<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductBrandsTable extends Migration
{
    public function up()
    {
        Schema::create('product_brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_brands');
    }
}