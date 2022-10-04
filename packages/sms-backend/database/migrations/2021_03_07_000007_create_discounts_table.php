<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->string('description')->nullable();
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('scope');
            $table->string('activation_code')->nullable()->index();
            $table->integer('value')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->boolean('is_active')->default(0)->nullable();
            $table->unsignedBigInteger('max_discount_price_per_order')->nullable();
            $table->unsignedInteger('max_use_per_customer')->nullable();
            $table->unsignedBigInteger('min_order_price')->nullable();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('product_list_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
