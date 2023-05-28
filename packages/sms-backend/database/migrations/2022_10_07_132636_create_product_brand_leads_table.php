<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductBrandLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_brand_leads', function (Blueprint $table) {
            $table->foreignId('lead_id')->constrained();
            $table->foreignId('product_brand_id')->constrained();
            $table->boolean('is_available')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_brand_leads');
    }
}
