<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoryCodesTable extends Migration
{
    public function up()
    {
        // Schema::create('product_category_codes', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('name');
        //     $table->foreignId('company_id')->constrained(); // unused
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    public function down()
    {
        // Schema::dropIfExists('product_category_codes');
    }
}
