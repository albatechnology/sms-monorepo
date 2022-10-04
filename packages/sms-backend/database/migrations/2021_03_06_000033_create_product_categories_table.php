<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedTinyInteger('type')->nullable();
            $table->string('slug')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories');
            $table->unsignedInteger('_lft');
            $table->unsignedInteger('_rgt');
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'type']);
        });
    }
}
