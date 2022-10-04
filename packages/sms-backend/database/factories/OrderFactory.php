<?php

namespace Database\Factories;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderStockStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PaymentType;
use App\Models\TaxInvoice;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $price = $this->faker->randomNumber(5);

        $address = Address::factory()->create();

        return [
            'note'             => $this->faker->text($maxNbChars = 50),
            'invoice_number'   => sprintf('INV%s%04d', now()->format('Ymd'), $this->faker->randomDigit % 10000),
            'tax_invoice_sent' => 0,
            'total_discount'   => 0,
            'total_price'      => $price,
            'status'           => OrderStatus::QUOTATION,
            'stock_status'     => OrderStockStatus::INDENT,
            'payment_status'   => OrderPaymentStatus::NONE,
            'user_id'          => User::all()->whereNotNull('type')->random()->id,
            'customer_id'      => Customer::first()->id ?? Customer::factory()->create()->id,
            'channel_id' => Channel::first()->id ?? Channel::factory()->create()->id,
            'company_id' => Company::first()->id ?? Company::factory()->create()->id,
            'lead_id'    => Lead::first()->id ?? Lead::factory()->create()->id,
            'records'    => [
                'billing_address'  => $address->toRecord(),
                'shipping_address' => $address->toRecord(),
                'tax_invoice'      => TaxInvoice::factory()->create()->toRecord(),
                'discount'         => null //Discount::factory()->create()->toRecord(),
            ],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * @param OrderDetail ...$orderDetails
     * @return OrderFactory
     */
    public function withOrderDetails(OrderDetail ...$orderDetails): OrderFactory
    {

        return $this->afterCreating(function (Order $order) use ($orderDetails){

            if(!isset($orderDetails) || empty($orderDetails)){
                $orderDetails = OrderDetail::factory()
                    ->count(2)
                    ->for($order)
                    ->create();
            }else{
                $orderDetails = collect($orderDetails);
                $orderDetails->each(function (OrderDetail $detail) use ($order){
                    $detail->order_id = $order->id;
                    $detail->save();
                });
            }

            // recalculate prices on order
            $order->total_price = $orderDetails->sum('total_price') - $order->total_discount;
            $order->save();
        });
    }

    public function asDeal()
    {
        return $this->afterCreating(function (Order $order) {
            app(OrderService::class)->makeOrderPayment(
                $order->total_price,
                PaymentType::factory()->forOrder($order)->create()->id,
                $order->id,
                status: PaymentStatus::APPROVED()
            );
        });
    }
}
