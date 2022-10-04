<?php

namespace App\Http\Resources\V1\Lead;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\LeadType;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\Product\ProductBrandResource;
use App\Http\Resources\V1\SmsChannel\SmsChannelResource;
use App\Http\Resources\V1\User\UserResource;
use App\Models\Order;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use App\Http\Resources\V1\Generic\MediaResource;

class LeadSmsResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::makeEnum("type", LeadType::class),
            // ResourceData::makeEnum("status", LeadStatus::class),
            ResourceData::make('status', Schema::TYPE_STRING, 'Lead status', value: function($data){
                if($data->is_unhandled == 1){
                    return 'Unhandled';
                } elseif(Order::where('lead_id', $data->id)->whereIn('status', [1,2])->whereIn('payment_status', [3,4])->count() > 0) {
                    return 'Deals';
                } elseif ($data->has_activity == 1){
                    return 'Follow Up';
                } else {
                    return 'Handled';
                }
            }),
            ResourceData::make('label', Schema::TYPE_STRING, 'my prospect')->description('User provided/auto generated description for the lead.'),
            ResourceData::make('interest', Schema::TYPE_STRING, 'Lagi Pengen LazyBoy'),
            ResourceData::makeRelationship('customer', CustomerResource::class),
            ResourceData::makeRelationship('user_sms', UserResource::class, 'userSms'),
            ResourceData::makeRelationship('sms_channel', SmsChannelResource::class, 'smsChannel'),
            ResourceData::makeRelationship('product_brand', ProductBrandResource::class, 'productBrand'),
            ResourceData::make('is_unhandled', Schema::TYPE_BOOLEAN, true),
            ResourceData::make('has_activity', Schema::TYPE_BOOLEAN, true),
            ResourceData::make('voucher', Schema::TYPE_STRING, 'Voucher Order'),
            ResourceData::makeRelationshipCollection(
                'voucher_image',
                MediaResource::class,
                null,
                fn($q) => $q->voucher_image ?? []
            )->notNested(),
            ...ResourceData::timestamps()
        ];
    }


    public static function getSortableFields(): array
    {
        return ['id', 'status', 'updated_at'];
    }
}
