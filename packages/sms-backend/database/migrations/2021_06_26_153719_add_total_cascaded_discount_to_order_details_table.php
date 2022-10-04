<?php

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalCascadedDiscountToOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('total_cascaded_discount')->default(0);
        });

        $orders = Order::query()
            ->whereHas('discount', function ($query) {
                $query->where('scope', \App\Enums\DiscountScope::TRANSACTION);
            })
            ->get();

        if ($orders->isNotEmpty()) {
            foreach ($orders as $order) {
                app(OrderService::class)->setOrderDetailCascadedDiscount($order, true);
            }
        }

    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['total_cascaded_discount']);
        });
    }
}