<?php

namespace App\Models;

use App\Enums\CacheTags;
use App\Services\CoreService;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends BaseModel
{
    use IsCompanyTenanted, SoftDeletes;
    public $table = 'locations';
    protected $guarded = [];
    protected $casts = [
        'company_id' => 'integer',
    ];

    public static function boot()
    {
        self::created(function (self $model) {
            // Create stock of all product units for this location
            CoreService::createStocksForLocation($model);
        });

        self::saved(function (self $model) {
            cache_service()->forget([CacheTags::LOCATION]);
        });

        self::deleted(function (self $model) {
            cache_service()->forget([CacheTags::LOCATION]);
        });

        parent::boot();
    }
}
