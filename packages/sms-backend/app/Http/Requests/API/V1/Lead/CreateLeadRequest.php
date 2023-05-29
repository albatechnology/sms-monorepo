<?php

namespace App\Http\Requests\API\V1\Lead;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Enums\LeadType;
use App\Models\Lead;
use BenSampo\Enum\Rules\EnumValue;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateLeadRequest extends BaseApiRequest
{
    protected ?string $model = Lead::class;

    public static function getSchemas(): array
    {
        return [
            Schema::string('type')->enum(LeadType::class)->example(LeadType::LEADS()->description),
            Schema::string('label')->example('My Leads'),
            Schema::integer('customer_id')->example(1),
            Schema::boolean('is_unhandled')->nullable()->example(true),
            Schema::integer('lead_category_id')->example(1),
            Schema::string('interest')->example('Lagi Pengen LazyBoy'),
            // Schema::integer('user_referral_id')->example(1),
            Schema::integer('channel_id')->example(1)->nullable(),

            Schema::array('product_brand_ids')->items(
                Schema::integer('id')->example(1)->description('Product brand id'),
            )->nullable(),

            // Schema::array('vouchers')->items(
            //     Schema::object()->properties(
            //         Schema::string('id')->example("GratisOngkir")->description('Voucher id'),
            //         Schema::integer('value')->example(1000000)->description('Voucher value in nominal'),
            //     ),
            // )->nullable(),
        ];

        // $channelIdValidation = 'nullable|exists:channels,id';
        // if (user()->type->value == 3 && user()->supervisor_type_id == 1) $channelIdValidation = 'required|exists:channels,id';

        // return [
        //     RequestData::makeEnum('type', LeadType::class, true),
        //     RequestData::make('label', Schema::TYPE_STRING, 'My Leads', 'nullable|string|min:2|max:100'),
        //     RequestData::make('customer_id', Schema::TYPE_INTEGER, 1, 'required|exists:customers,id'),
        //     RequestData::make(
        //         'is_unhandled',
        //         Schema::TYPE_BOOLEAN,
        //         true,
        //         ['nullable', 'boolean', function ($attribute, $value, $fail) {
        //             if (!$value) {
        //                 return;
        //             }

        //             // sales not allowed to make unhandled lead
        //             if (user()->is_sales) {
        //                 $fail('Only supervisor is allowed to make unhandled leads.');
        //             }
        //         }],
        //         'nullable|boolean'
        //     ),
        //     RequestData::make('lead_category_id', Schema::TYPE_INTEGER, 1, 'required|exists:lead_categories,id'),
        //     RequestData::make('interest', Schema::TYPE_STRING, 'Lagi Pengen LazyBoy', 'nullable'),
        //     RequestData::make('user_referral_id', Schema::TYPE_INTEGER, 1, 'nullable|exists:users,id'),
        //     RequestData::make('channel_id', Schema::TYPE_INTEGER, 1, $channelIdValidation),
        //     RequestData::make('product_brand_ids', Schema::TYPE_ARRAY, [1, 2, 3], 'nullable|array')->schema(Schema::array('product_brand_ids')->items(Schema::integer('id')->example(1))),

        //     RequestData::make('vouchers', Schema::TYPE_ARRAY, [1, 2, 3], 'nullable|array')->schema(
        //         Schema::array('vouchers')->items(
        //             Schema::object()->properties(
        //                 Schema::integer('id')->example(1)->description('Voucher id'),
        //                 Schema::integer('value')->example(1)->description('Voucher value in nominal'),
        //             ),
        //         )->nullable(),
        //     ),
        // ];
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new EnumValue(LeadType::class)],
            'label' => 'nullable|string|min:2|max:100',
            'customer_id' => 'required|exists:customers,id',
            'is_unhandled' => 'nullable|boolean',
            'lead_category_id' => 'required|exists:lead_categories,id',
            'interest' => 'nullable',
            // 'user_referral_id' => 'nullable|exists:users,id',
            'channel_id' => 'nullable|exists:channels,id',
            'product_brand_ids' => 'nullable|array',
            'product_brand_ids.*' => 'exists:product_brands,id',
            // 'vouchers' => 'nullable|array',
            // 'vouchers.*.id' => 'string',
            // 'vouchers.*.value' => 'integer',
        ];
    }

    protected static function data()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }
}
