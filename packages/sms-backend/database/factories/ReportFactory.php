<?php

namespace Database\Factories;

use App\Enums\ReportableType;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company   = Company::first() ?? Company::factory()->create();
        $startDate = now()->subDays(15);
        $endDate   = now()->addDays(15);

        return [
            'name'             => "{$company->name} ({$startDate} - {$endDate})",
            'start_date'       => now()->subDays(15),
            'end_date'         => now()->addDays(15),
            'reportable_type'  => ReportableType::fromModel($company)->value,
            'reportable_id'    => $company->id,
            'reportable_label' => $company->name
        ];
    }

    public function forCompany(Company $model = null)
    {
        if (!$model) {
            $model = Company::first() ?? Company::factory()->create();
        }

        return $this->state([
            'reportable_type'  => ReportableType::fromModel($model)->value,
            'reportable_id'    => $model->id,
            'reportable_label' => $model->name,
        ]);
    }

    public function forChannel(Channel $model = null)
    {
        if (!$model) {
            $model = Channel::first() ?? Channel::factory()->create();
        }

        return $this->state([
            'reportable_type'  => ReportableType::fromModel($model)->value,
            'reportable_id'    => $model->id,
            'reportable_label' => $model->name,
        ]);
    }

    public function forUser(User $model = null)
    {
        if (!$model) {
            $model = User::first() ?? User::factory()->create();
        }

        return $this->state([
            'reportable_type'  => ReportableType::fromModel($model)->value,
            'reportable_id'    => $model->id,
            'reportable_label' => $model->name,
        ]);
    }
}
