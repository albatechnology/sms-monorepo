<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTagsTable extends Migration
{
    public function up()
    {
        // Schema::create('product_tags', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('name');
        //     $table->string('slug')->index();
        //     $table->boolean('is_searchable')->default(true)->index();
        //     $table->foreignId('company_id')->constrained(); // unused
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }
}
