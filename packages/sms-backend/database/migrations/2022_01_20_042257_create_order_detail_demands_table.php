<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('order_detail_demands', function (Blueprint $table) {
        //     $table->bigIncrements('id');

        //     $table->unsignedInteger('quantity');
        //     $table->unsignedInteger('quantity_fulfilled')->default(0);
        //     $table->unsignedTinyInteger('status')->default(1);

        //     $table->longText('records')->nullable();

        //     $table->bigInteger('unit_price');
        //     $table->bigInteger('total_discount')->default(0);
        //     $table->bigInteger('total_price')->default(0);

        //     $table->foreignId('product_id')->nullable();
        //     $table->foreignId('order_id')->constrained();
        //     $table->foreignId('company_id')->constrained();
        //     $table->unsignedSmallInteger('shipment_status')->default(0);
        //     $table->unsignedBigInteger('total_cascaded_discount')->default(0);

        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->index(['order_id', 'status']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('order_detail_demands');
    }
}
