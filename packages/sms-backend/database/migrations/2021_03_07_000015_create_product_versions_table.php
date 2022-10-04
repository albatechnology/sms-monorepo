<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVersionsTable extends Migration
{
    public function up()
    {
        Schema::create('product_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('height')->nullable();
            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_versions');
    }
}