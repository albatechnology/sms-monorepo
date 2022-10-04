<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUnitsTable extends Migration
{
    public function up()
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('sku')->index()->nullable();
            $table->bigInteger('price')->nullable();
            $table->boolean('is_active')->default(0)->nullable();
            $table->integer('uom')->default(1);
            $table->foreignId('product_id')->constrained();
            $table->foreignId('colour_id')->nullable()->constrained();
            $table->foreignId('covering_id')->nullable()->constrained();
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'product_id']);
        });
    }
}
