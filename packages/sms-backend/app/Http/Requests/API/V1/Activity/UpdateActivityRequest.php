<?php

namespace App\Http\Requests\API\V1\Activity;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\ProductBrand;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateActivityRequest extends BaseApiRequest
{
    protected ?string $model = Activity::class;
    protected static $lead_id;
    public function __construct()
    {
        self::$lead_id = request()->lead_id;
    }

    public static function data(): array
    {
        return [
            RequestData::make('follow_up_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP, 'required|string|date'),
            RequestData::makeEnum('follow_up_method', ActivityFollowUpMethod::class, true, ActivityFollowUpMethod::getSelectableInstances()),
            RequestData::makeEnum('status', ActivityStatus::class, true),
            RequestData::make('feedback', Schema::TYPE_STRING, 'customer feedback', 'nullable|string|min:1|max:200'),
            RequestData::make('lead_id', Schema::TYPE_INTEGER, 1, 'required|exists:leads,id'),
            RequestData::make('estimated_value', Schema::TYPE_INTEGER, 1000, 'nullable|min:0'),
            RequestData::make('reminder_datetime', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP, 'nullable|string|date|after:now'),
            RequestData::make('reminder_note', Schema::TYPE_STRING, 'remind myself', 'nullable|required_with:reminder_datetime|string|min:1|max:200'),
            RequestData::make('interior_design_id', Schema::TYPE_INTEGER, 1, 'nullable|exists:interior_designs,id'),
            RequestData::make(
                'brand_ids',
                Schema::TYPE_ARRAY,
                [1, 2, 3],
                ['nullable', 'array', function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    // TODO: we may want to limit to the brand in the company context
                    $brandIds = ProductBrand::whereIn('id', $value)
                        ->get(['id'])
                        ->pluck('id');

                    $invalidBrandIds = collect($value)->diff($brandIds);

                    if ($invalidBrandIds->isNotEmpty()) {
                        $fail("Invalid brand id provided: {$invalidBrandIds->implode(', ')}");
                    }
                }],
                'nullable|array',
            )->schema(Schema::array('brand_ids')->items(Schema::integer('id')->example(1))),
            RequestData::make(
                'estimations',
                Schema::TYPE_OBJECT,
                [1, 2, 3],
                ['nullable', 'array', function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $lead = Lead::find(self::$lead_id);
                    if ($lead->channel->company_id == 1) {
                        $filteredValue = collect($value)->pluck('estimated_value')->filter(function ($value, $key) {
                            return $value < 5000000;
                        });
                        if ($filteredValue->count() > 0) $fail("Estimated value must be greather then 5.000.000");
                    }

                    $product_brand_ids = collect($value)->pluck('product_brand_id');
                    // TODO: we may want to limit to the brand in the company context
                    $brandIds = ProductBrand::whereIn('id', $product_brand_ids)
                        ->get(['id'])
                        ->pluck('id');

                    $invalidBrandIds = $product_brand_ids->diff($brandIds);

                    if ($invalidBrandIds->isNotEmpty()) {
                        $fail("Invalid brand id provided: {$invalidBrandIds->implode(', ')}");
                    }
                }],
                'nullable|array',
            )->schema(Schema::array('estimations')->items(
                Schema::object()->properties(
                    Schema::integer('product_brand_id')->example(1)->description('Product brand id'),
                    Schema::integer('estimated_value')->example(1000000)->description('Estimated value of product brand')
                ),
            )),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
