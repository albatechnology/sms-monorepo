<?php

namespace App\Http\Resources\V1\Order;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\DiscountError;
use App\Enums\OrderApprovalStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Http\Resources\V1\Address\BaseAddressResource;
use App\Http\Resources\V1\CartDemand\CartDemandResource;
use App\Http\Resources\V1\Channel\ChannelResource;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\Discount\DiscountResource;
// use App\Http\Resources\V1\Discount\BaseDiscountResource;
use App\Http\Resources\V1\OrderDetailResource;
use App\Http\Resources\V1\OrderDiscount\OrderDiscountResource;
use App\Http\Resources\V1\User\UserResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Resources\MissingValue;

class OrderResource extends BaseResource
{

    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('original_price', Schema::TYPE_INTEGER, 1),
            ResourceData::make('total_discount', Schema::TYPE_INTEGER, 1),
            // ResourceData::make('total_voucher', Schema::TYPE_INTEGER, 1),
            ResourceData::make('total_price', Schema::TYPE_INTEGER, 1),
            ResourceData::make('shipping_fee', Schema::TYPE_INTEGER, 0),
            ResourceData::make('packing_fee', Schema::TYPE_INTEGER, 0),
            ResourceData::make('additional_discount', Schema::TYPE_INTEGER, 0),
            ResourceData::make('additional_discount_ratio', Schema::TYPE_INTEGER, 0),
            ResourceData::make('amount_paid', Schema::TYPE_INTEGER, 0, value: fn ($q) => $q->amount_paid ?? 0),

            ResourceData::make('invoice_number', Schema::TYPE_STRING, 'INV2010123100001')->nullable(),

            ResourceData::makeEnum('status', OrderStatus::class),

            ResourceData::makeEnum('payment_status', OrderPaymentStatus::class),
            // we need a special case payment status for invoice page document.

            ResourceData::make(
                'payment_status_for_invoice',
                Schema::TYPE_STRING,
                OrderPaymentStatus::getDefaultInstance()->key,
                OrderPaymentStatus::getKeys(),
                function ($q) {
                    return $q->payment_status_for_invoice?->key ?? new MissingValue();
                }
            )->nullable(false),


            ResourceData::makeEnum('approval_status', OrderApprovalStatus::class),
            // ResourceData::make('discount_id', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::makeRelationship('discount', DiscountResource::class),
            ResourceData::makeEnum('discount_error', DiscountError::class, true),

            ResourceData::make('lead_id', Schema::TYPE_INTEGER, 1),
            ResourceData::make('user_id', Schema::TYPE_INTEGER, 1),
            ResourceData::makeRelationship(
                'approved_by',
                UserResource::class,
                'approvedBy'
            ),
            ResourceData::make('channel_id', Schema::TYPE_INTEGER, 1),
            // ResourceData::make('company_id', Schema::TYPE_INTEGER, 1),
            // ResourceData::make('interior_design_id', Schema::TYPE_INTEGER, 1)->nullable(),

            //ResourceData::makeRelationship('activity', ActivityResource::class),

            //ResourceData::make('customer_id', Schema::TYPE_INTEGER, 1),
            ResourceData::makeRelationship('user', UserResource::class),
            ResourceData::makeRelationship('channel', ChannelResource::class),
            ResourceData::makeRelationship('customer', CustomerResource::class),
            ResourceData::make(
                'discount_take_over_by',
                Schema::TYPE_OBJECT,
                [],
                value: function ($q) {
                    return [
                        'name' => $q->discountTakeOverBy?->name,
                        'channel' => $q->discountTakeOverBy?->channel?->name
                    ];
                }
            ),
            ResourceData::makeRelationshipCollection('order_details', OrderDetailResource::class),
            ResourceData::makeRelationship(
                'cart_demand',
                CartDemandResource::class,
                'cartDemand'
            ),

            // records
            ResourceData::makeRelationship('billing_address', BaseAddressResource::class, null, fn ($q) => $q->records['billing_address']),
            ResourceData::makeRelationship('shipping_address', BaseAddressResource::class, null, fn ($q) => $q->records['shipping_address']),
            // ResourceData::makeRelationship('discount', BaseDiscountResource::class, null, fn ($q) => $q->records['discount'] ?? new MissingValue()),
            // ResourceData::makeRelationshipCollection('order_discounts', OrderDiscountResource::class),
            // TODO: TAX invoice resource

            ResourceData::make('expected_shipping_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),
            ResourceData::make('quotation_valid_until_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),
            ResourceData::make('note', Schema::TYPE_STRING, 'order note')->nullable(),
            ResourceData::make('approval_note', Schema::TYPE_STRING, 'approval additional discount note')->nullable(),
            ResourceData::make('discount_approval_limit_percentage', Schema::TYPE_INTEGER, 'user discount approval limit percentage')->nullable(),
            ...ResourceData::timestamps()
        ];
    }
}
