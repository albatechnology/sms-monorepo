<?php

namespace App\Console\Commands;

use App\Enums\NewTargetType;
use App\Enums\UserType;
use App\Models\Channel;
// use App\Models\Company;
use App\Models\ProductBrand;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateNewReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new-reports:generate {--month= : The month for the new reports. Uses current month when not provided.}
    {--year= : The year for the new reports. Uses current year when not provided.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new reports';

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
        $target = 50;
        $reportMonth = $this->option('month') ??  now()->month;
        $reportYear = $this->option('year') ??  now()->year;

        $startDate = Carbon::create($reportYear, $reportMonth);
        $endDate   = Carbon::create($reportYear, $reportMonth)->addMonth()->subSecond();

        $monthName = $startDate->format('F');
        $yearName = $startDate->format('Y');

        $sales = User::where('type', UserType::SALES)->get();
        foreach ($sales as $sales) {
            $sales->newTargets()->firstOrCreate(
                [
                    'type' => NewTargetType::LEAD,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'name' => $sales->name . ' - ' . $monthName . ' ' . $yearName,
                    'target' => $target,
                ]
            );

            foreach (ProductBrand::where('subscribtion_user_id', $sales->subscribtion_user_id)->pluck('name', 'id') as $id => $name) {
                $sales->newTargets()->firstOrCreate(
                    [
                        'target_id' => $id,
                        'type' => NewTargetType::PRODUCT_BRAND,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    [
                        'name' => $sales->name . ' - ' . $monthName . ' ' . $yearName,
                        'target_name' => $name,
                        'target' => 0,
                    ]
                );
            }
        }

        $supervisors = User::where('type', UserType::SUPERVISOR)->whereIn('supervisor_type_id', [1, 2])->get();
        foreach ($supervisors as $supervisor) {
            $supervisor->newTargets()->firstOrCreate(
                [
                    'type' => NewTargetType::LEAD,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'name' => $supervisor->name . ' - ' . $monthName . ' ' . $yearName,
                    'target' => $target * (count($supervisor->getAllChildrenSales()) ?? 1),
                ]
            );

            foreach (ProductBrand::where('subscribtion_user_id', $supervisor->subscribtion_user_id)->pluck('name', 'id') as $id => $name) {
                $supervisor->newTargets()->firstOrCreate(
                    [
                        'target_id' => $id,
                        'type' => NewTargetType::PRODUCT_BRAND,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    [
                        'name' => $supervisor->name . ' - ' . $monthName . ' ' . $yearName,
                        'target_name' => $name,
                        'target' => 0,
                    ]
                );
            }
        }

        $channels = Channel::all();
        foreach ($channels as $channel) {
            $channel->newTargets()->firstOrCreate(
                [
                    'type' => NewTargetType::LEAD,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'name' => $channel->name . ' - ' . $monthName . ' ' . $yearName,
                    'target' => $target * (User::where('type', UserType::SALES)->where('channel_id', $channel->id)->count() ?? 1),
                ]
            );

            foreach (ProductBrand::where('subscribtion_user_id', $channel->subscribtion_user_id)->pluck('name', 'id') as $id => $name) {
                $channel->newTargets()->firstOrCreate(
                    [
                        'target_id' => $id,
                        'type' => NewTargetType::PRODUCT_BRAND,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    [
                        'name' => $channel->name . ' - ' . $monthName . ' ' . $yearName,
                        'target_name' => $name,
                        'target' => 0,
                    ]
                );
            }
        }

        // $companies = Company::all();
        // foreach ($companies as $company) {
        //     $company->newTargets()->firstOrCreate(
        //         [
        //             'type' => NewTargetType::LEAD,
        //             'start_date' => $startDate,
        //             'end_date' => $endDate,
        //         ],
        //         [
        //             'name' => $company->name . ' - ' . $monthName . ' ' . $yearName,
        //             'target' => $target * (User::where('type', UserType::SALES)->where('company_id', $company->id)->count() ?? 1),
        //         ]
        //     );

        //     foreach (ProductBrand::where('company_id', $company->id)->pluck('name', 'id') as $id => $name) {
        //         $company->newTargets()->firstOrCreate(
        //             [
        //                 'target_id' => $id,
        //                 'type' => NewTargetType::PRODUCT_BRAND,
        //                 'start_date' => $startDate,
        //                 'end_date' => $endDate,
        //             ],
        //             [
        //                 'name' => $company->name . ' - ' . $monthName . ' ' . $yearName,
        //                 'target_name' => $name,
        //                 'target' => 0,
        //             ]
        //         );
        //     }
        // }

        $this->info('New monthly new report generating queued!');
    }
}
