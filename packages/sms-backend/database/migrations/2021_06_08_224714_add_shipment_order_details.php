<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipmentOrderDetails extends Migration
{
    public function up()
    {
        // Schema::create('order_detail_shipment', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('order_detail_id')->constrained()->cascadeOnDelete();
        //     $table->unsignedInteger('quantity');
        //     $table->timestamps();
        // });

        // Schema::table('orders', function (Blueprint $table) {
        //     $table->unsignedSmallInteger('shipment_status')->default(0);
        // });

        // Schema::table('order_details', function (Blueprint $table) {
        //     $table->unsignedSmallInteger('shipment_status')->default(0);
        // });

        // Schema::table('shipments', function (Blueprint $table) {
        //     // dbal does not support changes to tinyint column
        //     $table->dropColumn(['status']);
        // });

        // Schema::table('shipments', function (Blueprint $table) {
        //     $table->unsignedSmallInteger('status')->default(0)->after('id');
        // });
    }

    public function down()
    {

        // Schema::table('shipments', function (Blueprint $table) {
        //     $table->string('status')->change();
        // });

        // Schema::table('order_details', function (Blueprint $table) {
        //     $table->dropColumn(['shipment_status']);
        // });

        // Schema::table('orders', function (Blueprint $table) {
        //     $table->dropColumn(['shipment_status']);
        // });

        // Schema::dropIfExists('order_detail_shipment');
    }
}
