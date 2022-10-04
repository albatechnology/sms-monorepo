<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->unsignedTinyInteger('type');
            $table->string('phone')->nullable();
            $table->foreignId('customer_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('default_address_id')->references('id')->on('addresses')->nullOnDelete();
        });
    }
}
