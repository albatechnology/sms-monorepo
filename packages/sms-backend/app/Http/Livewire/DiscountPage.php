<?php

namespace App\Http\Livewire;

use App\Models\Promo;
use Livewire\Component;

class DiscountPage extends Component
{
    public $promos;
    public $company_id;

    public function mount()
    {
        $this->promos = collect();
        $this->company_id = "";
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('livewire.discount-page');
    }

    public function updatedCompanyId($value)
    {
        $this->promos = Promo::query()
            ->where('company_id', $value)
            ->doesntHave('discount')
            ->get();
    }
}