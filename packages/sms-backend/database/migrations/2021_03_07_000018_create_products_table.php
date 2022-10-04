<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->bigInteger('price')->nullable();
            $table->boolean('is_active')->default(0)->index();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('product_brand_id')->constrained();
            $table->foreignId('product_model_id')->constrained();
            $table->foreignId('product_version_id')->constrained();
            $table->foreignId('product_category_code_id')->constrained();
            $table->foreignId('product_category_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'company_id',
                    'product_brand_id',
                    'product_model_id',
                    'product_version_id',
                    'product_category_code_id',
                ],
                'product_compound_index'
            );
        });
    }
}
