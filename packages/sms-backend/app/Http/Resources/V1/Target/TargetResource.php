<?php

namespace App\Http\Resources\V1\Target;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;
use App\Classes\TargetBreakdown;
use App\Enums\ActivityStatus;
use App\Enums\TargetBreakdownType;
use App\Enums\TargetChartType;
use App\Enums\TargetType;
use App\Http\Resources\V1\Channel\ChannelResource;
use App\Http\Resources\V1\Company\CompanyResource;
use App\Http\Resources\V1\Report\ReportResource;
use App\Http\Resources\V1\User\UserResource;
use App\Models\Activity;
use Cache;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Resources\MissingValue;

class TargetResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make('id', Schema::TYPE_INTEGER, 1, value: fn ($q) => $q->id),
            ResourceData::make('report_id', Schema::TYPE_INTEGER, 1),
            ResourceData::makeEnum('type', TargetType::class),
            ResourceData::makeEnum('chart_type', TargetChartType::class),
            ResourceData::make('target', Schema::TYPE_INTEGER, 1, value: function ($q) {
                return [
                    'value' => $q->target,
                    'format' => rupiah($q->target),
                ];
            }),
            ResourceData::make(
                'value',
                Schema::TYPE_INTEGER,
                1,
                value: function ($q) {
                    if ($q->relationLoaded('targetLines') && $q->targetLines->count() > 0) {
                        return $q->type->in([TargetType::DEALS_INVOICE_PRICE,TargetType::DEALS_PAYMENT_PRICE,TargetType::DEALS_BRAND_PRICE,TargetType::DEALS_MODEL_PRICE]) ? potongPPN($q->targetLines->sum('value')) : $q->targetLines->sum('value');
                    }

                    $value = $q->type->in([TargetType::DEALS_INVOICE_PRICE,TargetType::DEALS_PAYMENT_PRICE,TargetType::DEALS_BRAND_PRICE,TargetType::DEALS_MODEL_PRICE]) ? potongPPN($q->value) : $q->value;
                    return [
                        'value' => $value,
                        'format' => rupiah($value),
                    ];
                }
            ),

            ResourceData::makeRelationship('company', CompanyResource::class, value: fn ($q) => $q->model_type == 'company' ? new CompanyResource($q->model) : new MissingValue()),
            ResourceData::makeRelationship('channel', ChannelResource::class, value: fn ($q) => $q->model_type == 'channel' ? new ChannelResource($q->model) : new MissingValue()),
            ResourceData::makeRelationship('user', UserResource::class, value: fn ($q) => $q->model_type == 'user' ? new UserResource($q->model) : new MissingValue()),

            ResourceData::makeRelationship('report', ReportResource::class),
            ResourceData::makeRelationshipCollection('target_lines', TargetLineResource::class),

            ResourceData::make(
                'breakdown',
                Schema::TYPE_ARRAY,
                value: function ($q) {

                    if ($q->type->is(TargetType::ACTIVITY_COUNT)) {

                        $cacheKey = "target_breakdown#{$q->id}";
                        $cacheSeconds = 60 * 60 * 6; // 6 hours

                        return Cache::remember($cacheKey, $cacheSeconds, function () use ($q) {
                            return Activity::whereTargetId($q->id)
                                ->get(['status'])
                                ->countBy(fn ($q) => $q->status->value)
                                ->map(function ($value, $enumValue) {
                                    return new TargetBreakdown(
                                        TargetBreakdownType::ACTIVITY_STATUS(),
                                        ActivityStatus::fromValue($enumValue),
                                        $value
                                    );
                                })
                                ->values()
                                ->map(function (TargetBreakdown $breakdown) {
                                    return [
                                        'enum_type' => $breakdown->enumType->key,
                                        'enum_value' => $breakdown->enumValue->key,
                                        'value' => $breakdown->value,
                                    ];
                                });
                        });
                    }

                    return new MissingValue();
                },
                schema: Schema::array('breakdown')->items(
                    Schema::object('breakdown')->properties(
                        Schema::string('enum_type')
                            ->enum(...TargetBreakdownType::getKeys())
                            ->example(TargetBreakdownType::getDefaultInstance()->key),

                        Schema::string('enum_value')
                            ->enum(...ActivityStatus::getKeys())
                            ->example(ActivityStatus::getDefaultInstance()->key),

                        Schema::integer('value')
                            ->example(1),
                    )
                )
            ),

            //ResourceData::make('context', Schema::TYPE_INTEGER, 1),
            ...ResourceData::timestamps()
        ];
    }
}
