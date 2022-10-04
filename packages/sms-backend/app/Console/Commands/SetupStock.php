<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Services\CoreService;
use Exception;
use Illuminate\Console\Command;

/**
 * Class SetupStock
 * @package App\Console\Commands
 */
class SetupStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:stock {--company=} {--channel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup stock for existing product unit system wide. Channel option have higher priority over company.';

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
        $channelId = $this->option('channel');
        $companyID = $this->option('company');

        if ($channelId) {
            try {
                $channel = Channel::firstOrFail($channelId);
            } catch (Exception) {
                $this->error('Invalid channel id');
            }

            // CoreService::createStocksForChannel($channel);
            $this->info('Stock created.');
            return;
        }

        if ($companyID) {
            $channels = Channel::query()->whereIn('company_id', $companyID)->get();

            if ($channels->isEmpty()) {
                $this->error('Invalid company or company does not have any channel');
                return;
            }

        } else {

            $channels = Channel::all();
        }


        $channels->each(function (Channel $channel) {
            // CoreService::createStocksForChannel($channel);
        });

        $this->info(sprintf('Stock created for %s channels.', $channels->count()));
    }
}
