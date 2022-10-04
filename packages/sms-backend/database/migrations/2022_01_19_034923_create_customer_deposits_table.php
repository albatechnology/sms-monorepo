<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('activity_deposits');
        Schema::create('customer_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('lead_id')->constrained();
            $table->foreignId('approved_by_id')->nullable()->constrained('users');
            $table->string('product_brand')->nullable();
            $table->string('product_unit')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->integer('value');
            $table->text('description');
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
        Schema::dropIfExists('customer_deposits');
    }
}
