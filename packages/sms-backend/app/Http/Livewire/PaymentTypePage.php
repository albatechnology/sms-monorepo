<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\PaymentCategory;
use Livewire\Component;

class PaymentTypePage extends Component
{
    public $companies;
    public $payment_categories;

    public $name;
    public $company;
    public $payment_category;

    public function mount()
    {
        $this->companies          = Company::tenanted()->get();
        $this->payment_categories = collect();
    }

    public function render()
    {
        return view('livewire.payment-type-page');
    }

    public function updatedCompany($value)
    {
        $this->payment_categories = PaymentCategory::tenanted()->where('company_id', $value)->get();
        $this->payment_category   = $this->payment_categories->first()->id ?? null;
    }
}