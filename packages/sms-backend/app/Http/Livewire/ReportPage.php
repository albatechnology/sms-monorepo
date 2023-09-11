<?php

namespace App\Http\Livewire;

use App\Enums\ReportableType;
use App\Models\Channel;
use App\Models\Company;
use App\Models\SubscribtionUser;
use App\Models\User;
use Livewire\Component;

class ReportPage extends Component
{
    public $reportable_id = null;
    public $reportable_type;

    public $reportable_models;

    public function mount()
    {
        $this->reportable_models = collect();
        // if default reportable type is set
        if ($this->reportable_type) {
            $this->updatedReportableType($this->reportable_type);
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('livewire.report-page');
    }

    public function updatedReportableType($value)
    {
        $type = ReportableType::fromValue($value);

        // if ($type->is(ReportableType::COMPANY)) {
        //     $this->reportable_models = Company::tenanted()->get();
        // }

        if ($type->is(ReportableType::SUBSCRIBTION_USER)) {
            $this->reportable_models = SubscribtionUser::get();
        }

        if ($type->is(ReportableType::CHANNEL)) {
            $this->reportable_models = Channel::tenanted()->get();
        }

        if ($type->is(ReportableType::USER)) {
            $this->reportable_models = User::tenanted()->get();
        }
    }
}
