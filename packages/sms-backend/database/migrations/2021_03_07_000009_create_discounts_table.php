<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscribtion_user_id')->constrained('subscribtion_users');
            $table->foreignId('promo_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('scope');
            $table->string('activation_code')->nullable()->index();
            $table->float('value')->default(0);
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->boolean('is_active')->default(0)->nullable();
            $table->unsignedBigInteger('max_discount_price_per_order')->nullable();
            $table->unsignedInteger('max_use_per_customer')->nullable();
            $table->unsignedBigInteger('min_order_price')->nullable();
            // $table->foreignId('company_id')->constrained(); // unused
            $table->mediumText('product_ids')->nullable();

            $table->string('product_category')->nullable();
            $table->foreignId('product_brand_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
