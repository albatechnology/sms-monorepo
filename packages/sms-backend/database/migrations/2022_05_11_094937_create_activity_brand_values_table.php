<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityBrandValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_brand_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('product_brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->integer('activity_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->unsignedInteger('estimated_value')->default(0);
            $table->unsignedInteger('order_value')->default(0);
            $table->double('total_discount', 15, 3)->default(0);
            $table->double('total_order_value', 15, 3)->default(0);
            $table->timestamps();

            $table->index(['activity_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_brand_values');
    }
}
