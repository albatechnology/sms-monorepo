<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('subscribtion_user_id')->constrained('subscribtion_users');
            // $table->foreignId('company_id')->constrained(); // unused
            $table->foreignId('product_category_id')->constrained();
            $table->foreignId('product_brand_id')->constrained();
            $table->smallInteger('brand_category_id')->nullable();
            $table->string('name')->index();
            $table->string('description')->nullable();
            $table->string('sku')->index()->nullable();
            $table->bigInteger('price')->nullable();
            $table->boolean('is_active')->default(0)->nullable();
            $table->integer('uom')->default(1);
            $table->unsignedBigInteger('production_cost')->default(0);
            $table->unsignedSmallInteger('product_category')->nullable();
            $table->float('volume')->nullable();
            $table->text('tags')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
