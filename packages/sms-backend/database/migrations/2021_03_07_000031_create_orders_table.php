<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            // user submitted data
            $table->json('raw_source')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('shipping_fee')->default(0);
            $table->unsignedBigInteger('packing_fee')->default(0);
            $table->dateTime('expected_shipping_datetime')->nullable();

            // system generated
            $table->boolean('tax_invoice_sent')->default(0)->nullable();
            $table->longText('records')->nullable();
            $table->string('invoice_number')->index()->nullable();
            $table->bigInteger('amount_paid')->default(0);

            // statuses
            $table->unsignedTinyInteger('status')->index();
            $table->unsignedTinyInteger('payment_status')->index();
            $table->unsignedTinyInteger('stock_status')->index();

            // relationship
            $table->foreignId('user_id')->constrained();
            $table->foreignId('lead_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('channel_id')->constrained();
            $table->foreignId('company_id')->constrained();

            // discount
            $table->foreignId('discount_id')->nullable()->constrained();
            $table->string('discount_error')->nullable();
            $table->bigInteger('total_discount')->default(0);
            $table->bigInteger('total_price')->default(0);
            $table->unsignedInteger('total_voucher')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }
}
