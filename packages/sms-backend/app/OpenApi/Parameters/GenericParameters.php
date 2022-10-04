<?php


namespace App\OpenApi\Parameters;

use App\Classes\CustomCollectionBuilder;
use App\Classes\CustomQueryBuilder;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class GenericParameters
{
    /**
     * return array of parameter query for pagination
     */
    public static function paginationQueryParameter(): array
    {
        return [
            Parameter::query()
                ->name('page')
                ->required(false)
                ->example(1)
                ->schema(Schema::number()->minimum(1)),

            Parameter::query()
                ->name('perPage')
                ->required(false)
                ->example(15)
                ->schema(Schema::number()->minimum(1)),
        ];
    }

    /**
     * return array of parameter query for pagination
     * @param  string  $class
     * @return array
     */
    public static function sortQueryParameter(): array
    {
        return [
            Parameter::query()
                ->name('sort')
                ->required(false)
                ->example('-id')
                ->schema(Schema::string())
                ->description('Takes in field name to sort. Append "-" (e.g., -id to sort id in descending order)'),
        ];
    }

    public static function filterQueryParameter($class): array
    {
        $resources = CustomQueryBuilder::getAllowedFilterMap($class);
        $queries = [];

        foreach ($resources as $resource) {
            $query = Parameter::query()
                ->name('filter['.$resource["key"].']')
                ->required(false)
                ->schema($resource['schema']);

            $queries[] = $query;
        }

        return $queries;
    }

    public static function filterCollectionParameter($resource): array
    {
        $resources = CustomCollectionBuilder::getAllowedFilterMap($resource);
        $queries = [];

        foreach ($resources as $res) {
            $query = Parameter::query()
                ->name('filter[' . $res["key"] . ']')
                ->required(false)
                ->schema($res['schema']);

            $queries[] = $query;
        }

        return $queries;
    }
}
