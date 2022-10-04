<?php

namespace App\Classes;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class CustomCollectionBuilder extends QueryBuilder
{
    const DEFAULT_PER_PAGE = 15;
    const MAX_PER_PAGE = 100;
    const MIN_PER_PAGE = 1;

    const RESOURCE_SHIPPING_ZONES = 'shipping-zones';
    const RESOURCE_SUPPLIER_LOCATIONS = 'supplier-locations';
    const RESOURCE_COUNTRIES = 'countries';

    public static function getAllowedFilterMap($resource = null)
    {
        $data = [
            self::RESOURCE_SHIPPING_ZONES => [
                [
                    "key" => "group",
                    "schema" => Schema::string('group')->example('country'),
                ],
                [
                    "key" => "name",
                    "schema" => Schema::string('name')->example('Australia'),
                ],
            ],
            self::RESOURCE_SUPPLIER_LOCATIONS => [
                [
                    "key" => "label",
                    "schema" => Schema::string('label')->example('resurge'),
                ],
            ],
            self::RESOURCE_COUNTRIES => [
                [
                    "key" => "name",
                    "schema" => Schema::string('name')->example('united'),
                ],
            ],
        ];

        return $resource ? $data[$resource] : $data;
    }

    public static function getKeys($resource)
    {
        return collect(self::getAllowedFilterMap()[$resource])->pluck("key")->toArray();
    }

    public static function getSchemas($resource)
    {
        return collect(self::getAllowedFilterMap()[$resource])->pluck("schema")->toArray();
    }

    public static function build($collection, string $resource, bool $paginate = true)
    {

        $filterKeys = self::getKeys($resource);

        if(!empty($filterKeys) && !empty(request('filter'))){
            $collection = $collection->filter(function ($data) use ($filterKeys){

                foreach(request('filter') as $key => $val) {
                    // calling filter on keys not allowed, simply ignore it
                    if(!(in_array($key, $filterKeys))) continue;

                    if(! Str::contains(Str::lower($data[$key]), Str::lower($val))) return false;
                }

                return $data;
            });
        }

        if($paginate){
            return $collection->paginate(self::getQueryPerPage());
        }else{
            return $collection;
        }
    }

    public static function getQueryPerPage()
    {
        $perPage = request('perPage') ?? self::DEFAULT_PER_PAGE;

        return max(self::MIN_PER_PAGE, min(self::MAX_PER_PAGE, (int) $perPage));
    }
}
