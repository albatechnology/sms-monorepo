<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteriorDesignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interior_designs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // relationships
            $table->foreignId('bum_id')->nullable()->index();
            $table->foreignId('sales_id')->nullable()->index();
            $table->foreignId('religion_id')->nullable()->constrained();

            // globals
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('owner')->nullable();
            $table->string('npwp')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('bank_account_number')->nullable();

            // timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interior_designs');
    }
}
