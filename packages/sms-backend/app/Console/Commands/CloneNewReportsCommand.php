<?php

namespace App\Console\Commands;

use App\Enums\NewTargetType;
use App\Enums\UserType;
use App\Models\Channel;
use App\Models\Company;
use App\Models\ProductBrand;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloneNewReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new-reports:clone {--month= : The month for the new reports. Uses current month when not provided.}
    {--year= : The year for the new reports. Uses current year when not provided.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone new reports';

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

        // TODO: command option to choose the month to copy from
        $copyMonth = null;
        $copyYear  = null;

        if ($copyMonth && $copyYear) {
            // use the month as provided
            $copyStartDate = Carbon::create($copyYear, $copyMonth);
            $copyEndDate   = Carbon::create($copyYear, $copyMonth)->addMonth()->subSecond();
        } else {
            // use the month before the new report month
            $copyStartDate = Carbon::create($reportYear, $reportMonth)->subMonth();
            $copyEndDate   = Carbon::create($reportYear, $reportMonth)->subSecond();
        }

        $sales = User::where('type', UserType::SALES)->get();
        foreach ($sales as $sales) {
            $lastTargetLead = $sales->newTargets()->where('type', NewTargetType::LEAD)->where('start_date', $copyStartDate)->where('end_date', $copyEndDate)->first();

            $sales->newTargets()->firstOrCreate(
                [
                    'type' => NewTargetType::LEAD,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'target' => $lastTargetLead?->target ?? $target,
                ]
            );

            $lastTargetProductBrands = $sales->newTargets()->where('type', NewTargetType::PRODUCT_BRAND)->where('start_date', $copyStartDate)->where('end_date', $copyEndDate)->get()->keyBy('id');

            foreach (ProductBrand::where('company_id', $sales->company_id)->pluck('name', 'id') as $id => $name) {
                $sales->newTargets()->firstOrCreate(
                    [
                        'target_id' => $id,
                        'type' => NewTargetType::PRODUCT_BRAND,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    [
                        'target_name' => $name,
                        'target' => $lastTargetProductBrands[$id]?->target ?? 0,
                    ]
                );
            }
        }

        $supervisors = User::where('type', UserType::SUPERVISOR)->whereIn('supervisor_type_id', [1, 2])->get();
        foreach ($supervisors as $supervisor) {
            $lastTargetLead = $supervisor->newTargets()->where('type', NewTargetType::LEAD)->where('start_date', $copyStartDate)->where('end_date', $copyEndDate)->first();

            $supervisor->newTargets()->firstOrCreate(
                [
                    'type' => NewTargetType::LEAD,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'target' => $lastTargetLead?->target ?? $target * (count($supervisor->getAllChildrenSales()) ?? 1),
                ]
            );

            $lastTargetProductBrands = $supervisor->newTargets()->where('type', NewTargetType::PRODUCT_BRAND)->where('start_date', $copyStartDate)->where('end_date', $copyEndDate)->get()->keyBy('id');

            foreach (ProductBrand::where('company_id', $supervisor->company_id)->pluck('name', 'id') as $id => $name) {
                $supervisor->newTargets()->firstOrCreate(
                    [
                        'target_id' => $id,
                        'type' => NewTargetType::PRODUCT_BRAND,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    [
                        'target_name' => $name,
                        'target' => $lastTargetProductBrands[$id]?->target ?? 0,
                    ]
                );
            }
        }

        $channels = Channel::all();
        foreach ($channels as $channel) {
            $lastTargetLead = $channel->newTargets()->where('type', NewTargetType::LEAD)->where('start_date', $copyStartDate)->where('end_date', $copyEndDate)->first();

            $channel->newTargets()->firstOrCreate(
                [
                    'type' => NewTargetType::LEAD,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'target' => $lastTargetLead?->target ?? $target * (User::where('type', UserType::SALES)->where('channel_id', $channel->id)->count() ?? 1),
                ]
            );

            $lastTargetProductBrands = $channel->newTargets()->where('type', NewTargetType::PRODUCT_BRAND)->where('start_date', $copyStartDate)->where('end_date', $copyEndDate)->get()->keyBy('id');

            foreach (ProductBrand::where('company_id', $channel->company_id)->pluck('name', 'id') as $id => $name) {
                $channel->newTargets()->firstOrCreate(
                    [
                        'target_id' => $id,
                        'type' => NewTargetType::PRODUCT_BRAND,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    [
                        'target_name' => $name,
                        'target' => $lastTargetProductBrands[$id]?->target ?? 0,
                    ]
                );
            }
        }

        $companies = Company::all();
        foreach ($companies as $company) {
            $lastTargetLead = $company->newTargets()->where('type', NewTargetType::LEAD)->where('start_date', $copyStartDate)->where('end_date', $copyEndDate)->first();

            $company->newTargets()->firstOrCreate(
                [
                    'type' => NewTargetType::LEAD,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'target' => $lastTargetLead?->target ?? $target * (User::where('type', UserType::SALES)->where('company_id', $company->id)->count() ?? 1),
                ]
            );

            $lastTargetProductBrands = $channel->newTargets()->where('type', NewTargetType::PRODUCT_BRAND)->where('start_date', $copyStartDate)->where('end_date', $copyEndDate)->get()->keyBy('id');

            foreach (ProductBrand::where('company_id', $company->id)->pluck('name', 'id') as $id => $name) {
                $company->newTargets()->firstOrCreate(
                    [
                        'target_id' => $id,
                        'type' => NewTargetType::PRODUCT_BRAND,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    [
                        'target_name' => $name,
                        'target' => $lastTargetProductBrands[$id]?->target ?? 0,
                    ]
                );
            }
        }

        $this->info('New monthly new report cloning queued!');
    }
}
