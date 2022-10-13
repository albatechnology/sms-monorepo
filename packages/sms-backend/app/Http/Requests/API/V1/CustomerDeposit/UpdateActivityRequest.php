<?php

namespace App\Http\Requests\API\V1\CustomerDeposit;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\ProductBrand;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateActivityRequest extends BaseApiRequest
{
    protected ?string $model = Activity::class;

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
            RequestData::make('brand_ids', Schema::TYPE_ARRAY, [1,2,3],
                ['nullable', 'array', function($attribute, $value, $fail){
                    if(!$value){
                        return;
                    }

                    // TODO: we may want to limit to the brand in the company context
                    $brandIds = ProductBrand::whereIn('id', $value)
                        ->get(['id'])
                        ->pluck('id');

                    $invalidBrandIds = collect($value)->diff($brandIds);

                    if($invalidBrandIds->isNotEmpty()){
                        $fail("Invalid brand id provided: {$invalidBrandIds->implode(', ')}");
                    }

                }],
                'nullable|array',

            )->schema(Schema::array('brand_ids')->items(Schema::integer('id')->example(1))),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
