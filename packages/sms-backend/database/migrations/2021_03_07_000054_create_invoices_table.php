<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('fulfilled_by_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
