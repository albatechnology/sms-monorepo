<?php

namespace App\Providers;

use App\Enums\ReportableType;
use App\Models\PersonalAccessToken;
use App\Services\CacheService;
use App\Services\CoreService;
use App\Services\MultiTenancyService;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MultiTenancyService::class, function ($app) {
            return new MultiTenancyService();
        });

        $this->app->singleton(CoreService::class, function ($app) {
            return new CoreService();
        });

        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });

        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Schema::defaultStringLength(191);

        Relation::morphMap(ReportableType::getMorphMap());
    }
}
