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
            $table->foreignId('subscribtion_user_id')->constrained('subscribtion_users');
            // $table->foreignId('company_id')->constrained(); // unused
            $table->smallInteger('brand_category_id');
            $table->string('name')->index();

            $table->unsignedTinyInteger('hpp_calculation')->default(0);
            $table->integer('currency_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_brands');
    }
}
