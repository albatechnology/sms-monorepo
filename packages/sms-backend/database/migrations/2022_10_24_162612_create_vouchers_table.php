<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->string('id', 100)->unique()->primary();
            $table->string('description')->nullable();
            $table->integer('value')->default(0);
            $table->date('start_time')->nullable();
            $table->date('end_time')->nullable();
            $table->boolean('is_active')->default(1)->nullable();
            $table->unsignedBigInteger('min_order_price')->nullable();
            $table->foreignId('company_id')->constrained();
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
        Schema::dropIfExists('vouchers');
    }
}
