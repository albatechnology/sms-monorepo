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
            $table->foreignId('company_id')->constrained();
            $table->smallInteger('brand_category_id');
            $table->string('name')->index();


            $table->unsignedTinyInteger('hpp_calculation')->default(0);
            $table->integer('currency_id')->nullable();
            $table->boolean('show_in_moves')->default(1);
            $table->boolean('show_in_sms')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_brands');
    }
}
