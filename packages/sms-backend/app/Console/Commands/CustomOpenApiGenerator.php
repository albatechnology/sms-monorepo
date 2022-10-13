<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Vyuldashev\LaravelOpenApi\Generator;

class CustomOpenApiGenerator extends Command
{
    protected $signature = 'openapi:save';
    protected $description = 'Save OpenAPI specification to a json file';

    public function handle(Generator $generator): void
    {
        $filename = config('core.open_api.filename');
        $collectionExists = collect(config('openapi.collections'))->has('default');

        if (! $collectionExists) {
            $this->error('Collection "default" does not exist.');

            return;
        }

        Storage::disk('open-api')->put($filename, $generator
            ->generate('default')
            ->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $folder = config('filesystems.disks.open-api.root');

        $this->info(sprintf("%s generated at %s", $filename, $folder));
    }
}
