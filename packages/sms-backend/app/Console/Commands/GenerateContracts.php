<?php

namespace App\Console\Commands;

use App\Contracts\Enums;
use App\Contracts\Errors;
use App\Services\CoreService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate json contract for enum and errors.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $newJsonString = json_encode(Errors::body(), JSON_PRETTY_PRINT);
            Storage::disk('contracts')->put('errors.json', $newJsonString);
            $this->info('Errors contract generated.');

            $enums = json_encode(app(CoreService::class)->getEnumContracts(), JSON_PRETTY_PRINT);
            //dd($newJsonString);
            Storage::disk('contracts')->put('enums.json', $enums);
            $this->info('Enums contract generated.');
        } catch (Exception $e) {
            $this->error('Failed to generate contracts.');
            $this->error($e->getMessage());
        }
    }
}
