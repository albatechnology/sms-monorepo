<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    public function up()
    {
        // Schema::create('shipments', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('status');
        //     $table->longText('note')->nullable();
        //     $table->string('reference')->nullable();
        //     $table->date('estimated_delivery_date')->nullable();
        //     $table->datetime('arrived_at')->nullable();
        //     $table->foreignId('order_id')->constrained();
        //     $table->foreignId('fulfilled_by_id')->nullable()->constrained('users');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }
}
