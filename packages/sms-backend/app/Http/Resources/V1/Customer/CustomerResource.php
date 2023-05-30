<?php

namespace App\Http\Resources\V1\Customer;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\PersonTitle;
use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CustomerResource extends BaseResource
{
    /**
     * @return array
     * @throws Exception
     */
    public static function getSortableFields(): array
    {
        // default to return all properties as sortable
        return collect(self::data())
            ->map(fn(ResourceData $data) => $data->getKey())
            ->diff([])
            ->values()
            ->all();
    }

    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_INTEGER, 1),
            ResourceData::makeEnum('title', PersonTitle::class),
            // ResourceData::make('name', Schema::TYPE_STRING, 'Masfud Difa'),
            ResourceData::make('first_name', Schema::TYPE_STRING, 'Barrack'),
            ResourceData::make('last_name', Schema::TYPE_STRING, 'Obama')->nullable(),
            ResourceData::make('email', Schema::TYPE_STRING, ApiDataExample::EMAIL),
            ResourceData::make('phone', Schema::TYPE_STRING, '+923182732932'),
            ResourceData::make('date_of_birth', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP)->nullable(),
            ResourceData::make('description', Schema::TYPE_STRING, 'Custom note')
                        ->description('Possible place for a custom note from the user about the customer')
                        ->notNested()
                        ->nullable(),
            ResourceData::make('default_address_id', Schema::TYPE_INTEGER, 1)->nullable(),
            ResourceData::make('has_activity', Schema::TYPE_BOOLEAN, true),
        ];
    }
}
