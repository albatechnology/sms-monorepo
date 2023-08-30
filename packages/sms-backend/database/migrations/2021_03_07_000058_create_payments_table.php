<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('subscribtion_user_id')->constrained('subscribtion_users');
            // $table->foreignId('channel_id')->constrained(); // unused
            $table->bigInteger('amount');
            $table->string('reference')->nullable()->index();
            $table->unsignedTinyInteger('status')->index();
            $table->foreignId('payment_type_id')->constrained();
            $table->foreignId('approved_by_id')->nullable()->constrained('users');
            $table->foreignId('added_by_id')->constrained('users');
            $table->foreignId('order_id')->constrained();
            // $table->foreignId('company_id')->constrained(); // unused
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
