<?php

namespace App\Http\Resources\V1\User;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\UserType;
// use App\Http\Resources\V1\Company\BaseCompanyResource;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Support\Facades\Gate;

class UserResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::make("name", Schema::TYPE_STRING, 'Admin')->sortable(),
            ResourceData::make("initial", Schema::TYPE_STRING, 'NF', value: function ($q) {
                $words = explode(' ', trim($q->name));
                $initial = '';
                if (count($words) > 1) {
                    foreach ($words as $w) {
                        $initial .= $w[0] ?? '';
                    }
                } else {
                    $initial = $q->name;
                }
                return strtoupper(substr($initial, 0, 2));
            }),
            ResourceData::make("as", Schema::TYPE_STRING, 'SALES', value: function ($q) {
                if ($q->type->is(UserType::DIRECTOR())) {
                    return 'DIRECTOR';
                } elseif ($q->type->is(UserType::SUPERVISOR()) &&  $q->supervisor_type_id == 2) {
                    return 'BUM';
                } elseif ($q->type->is(UserType::SUPERVISOR()) &&  $q->supervisor_type_id == 1) {
                    return 'STORE_LEADER';
                } elseif ($q->type->is(UserType::SALES())) {
                    return 'SALES';
                } else {
                    return 'ADMIN';
                }
            }),
            ResourceData::make("reportable_type", Schema::TYPE_STRING, 'USER', value: function ($q) {
                if ($q->type->is(UserType::SALES())) {
                    return 'USER';
                } elseif ($q->type->is(UserType::SUPERVISOR())) {
                    return 'CHANNEL';
                } else {
                    return 'COMPANY';
                }
            }),
            ResourceData::make("email", Schema::TYPE_STRING, ApiDataExample::EMAIL)->sortable(),
            ResourceData::make("email_verified_at", Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->notNested(),
            ResourceData::makeEnum('type', UserType::class)
                ->description("User type determine the feature should be made avaiable to them. The available enum options are hard coded."),

            // ResourceData::makeRelationship('company', BaseCompanyResource::class),
            // ResourceData::make("company_id", Schema::TYPE_INTEGER, 1)
            //     ->description("The company that this user belongs to")
            //     ->nullable()
            //     ->sortable(),

            ResourceData::make("channel_id", Schema::TYPE_INTEGER, 1)
                ->description("The default channel currently selected for this user")
                ->nullable()
                ->sortable(),

            ResourceData::make("supervisor_id", Schema::TYPE_INTEGER, 1)
                ->description("The user id of this user's supervisor")
                ->nullable(),

            ResourceData::make("supervisor_type_id", Schema::TYPE_INTEGER, 1)
                ->description("If this user is a supervisor, this return the supervisor type id")
                ->nullable(),
            ResourceData::make('discount_approval_limit_percentage', Schema::TYPE_INTEGER, null, value: function ($q) {
                return $q->supervisorType?->discount_approval_limit_percentage;
            }),
            ResourceData::make('app_show_hpp', Schema::TYPE_BOOLEAN, true, null, value: fn () => Gate::check('app_show_hpp')),
            ResourceData::make('app_approve_discount', Schema::TYPE_BOOLEAN, true, null, value: fn () => Gate::check('app_approve_discount')),
            ResourceData::make('app_create_lead', Schema::TYPE_BOOLEAN, true, null, value: fn () => Gate::check('app_create_lead')),
            // ResourceData::make("abilities", Schema::TYPE_ARRAY, null, null, value: function () {
            //     return [
            //         'app_show_hpp' => Gate::check('app_show_hpp'),
            //         'app_approve_discount' => Gate::check('app_approve_discount'),
            //         'app_create_lead' => Gate::check('app_create_lead'),
            //     ];
            // }),

            // uncomment this to show an array of channel ids available to this user
            //
            //            ResourceData::make("channel_ids", Schema::TYPE_ARRAY, null,
            //                null, fn($data) => $data->channelsPivot ? $data->channelsPivot->pluck('channel_id') : []
            //            )->nullable(),
            //            ResourceData::make("channel_ids.*", Schema::TYPE_INTEGER, 1)
            //                ->parent("channel_ids"),


        ];
    }
}
