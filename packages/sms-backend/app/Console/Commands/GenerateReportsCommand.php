<?php

namespace App\Console\Commands;

use App\Enums\UserType;
use App\Models\Channel;
use App\Models\SubscribtionUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate {--month= : The month for the reports. Uses current month when not provided.}
    {--year= : The year for the reports. Uses current year when not provided.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate reports';

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
     * @return int
     */
    public function handle()
    {
        $reportMonth = $this->option('month') ??  now()->month;
        $reportYear = $this->option('year') ??  now()->year;

        $startDate = Carbon::create($reportYear, $reportMonth);
        $endDate   = Carbon::create($reportYear, $reportMonth)->addMonth()->subSecond();

        $monthName = $startDate->format('F');
        $yearName = $startDate->format('Y');

        $sales = User::where('type', UserType::SALES)->get();
        foreach ($sales as $sales) {
            $sales->reports()->create([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }

        $supervisors = User::where('type', UserType::SUPERVISOR)->get();
        foreach ($supervisors as $supervisor) {
            $supervisor->reports()->create([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }

        $channels = Channel::all();
        foreach ($channels as $channel) {
            $channel->reports()->create([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }

        $subscribtionUsers = SubscribtionUser::all();
        foreach ($subscribtionUsers as $subscribtionUser) {
            $subscribtionUser->reports()->create([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }

        $this->info('New monthly new report generating queued!');
    }
}
