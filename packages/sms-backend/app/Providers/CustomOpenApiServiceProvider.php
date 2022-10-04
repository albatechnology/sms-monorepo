<?php

namespace App\Providers;

use App\OpenApi\Customs\Builder\CustomParametersBuilder;
use App\OpenApi\Customs\Builder\CustomRequestBodyBuilder;
use App\OpenApi\Customs\Builder\CustomResponsesBuilder;
use Illuminate\Support\Collection;
use Vyuldashev\LaravelOpenApi\Builders\ComponentsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\ExtensionsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\InfoBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\CallbacksBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\SecurityBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\OperationsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\PathsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\ServersBuilder;
use Vyuldashev\LaravelOpenApi\Builders\TagsBuilder;
use Vyuldashev\LaravelOpenApi\Generator;
use Vyuldashev\LaravelOpenApi\OpenApiServiceProvider;

class CustomOpenApiServiceProvider extends OpenApiServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->app->singleton(Generator::class, static function ($app) {
            $config = config('openapi');

            return new Generator(
                $config,
                $app[InfoBuilder::class],
                $app[ServersBuilder::class],
                $app[TagsBuilder::class],

                new PathsBuilder(
                    new OperationsBuilder(
                        new CallbacksBuilder(),
                        new CustomParametersBuilder(),
                        new CustomRequestBodyBuilder(),
                        new CustomResponsesBuilder(),
                        new ExtensionsBuilder(),
                        new SecurityBuilder()
                    )
                ),
                $app[ComponentsBuilder::class]
            );
        });
    }

    private function getPathsFromConfig(string $type): array
    {
        $directories = config('openapi.locations.'.$type, []);

        foreach ($directories as &$directory) {
            $directory = glob($directory, GLOB_ONLYDIR);
        }

        return (new Collection($directories))
            ->flatten()
            ->unique()
            ->toArray();
    }
}
