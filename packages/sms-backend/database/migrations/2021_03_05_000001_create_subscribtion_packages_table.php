<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribtionPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribtion_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('max_users')->nullable();
            $table->unsignedInteger('max_customers')->nullable();
            $table->unsignedInteger('max_activities')->nullable();
            $table->unsignedInteger('max_leads')->nullable();
            $table->unsignedInteger('max_orders')->nullable();
            $table->unsignedInteger('max_brands')->nullable();
            $table->unsignedInteger('max_categories')->nullable();
            $table->unsignedInteger('max_products')->nullable();
            $table->boolean('can_discount')->default(0);
            $table->boolean('can_approval')->default(0);
            $table->boolean('can_multi_companies')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscribtion_packages');
    }
}
