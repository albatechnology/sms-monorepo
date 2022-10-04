<?php

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountApprovalLimitToSupervisorTypesTable extends Migration
{
    public function up()
    {
        Schema::table('supervisor_types', function (Blueprint $table) {
            $table->unsignedInteger('discount_approval_limit_percentage')->default(0);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('additional_discount_ratio')->nullable();
        });

        // calculate the additional_discount_ratio of existing orders
        Order::query()
            ->where('additional_discount', '>', 0)
            ->get()
            ->each(function (Order $order){
                app(OrderService::class)->calculateOrderAdditionalDiscountRatio($order, true);
            });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['additional_discount_ratio']);
        });

        Schema::table('supervisor_types', function (Blueprint $table) {
            $table->dropColumn(['discount_approval_limit_percentage']);
        });
    }
}