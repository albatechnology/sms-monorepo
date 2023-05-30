<?php

namespace App\Http\Livewire;

use App\Models\PaymentCategory;
use App\Models\PaymentType;
use Livewire\Component;

class PaymentPage extends Component
{
    public $payment_categories;
    public $payment_types;

    public $payment_category;
    public $payment_type;

    public function mount()
    {
        // $this->payment_categories = PaymentCategory::tenanted()->get();
        $this->payment_categories = PaymentCategory::all();
        $this->payment_types      = collect();

        // if default payment type is set
        if ($this->payment_type) {
            $type                   = PaymentType::find($this->payment_type);
            $this->payment_category = $type->payment_category_id;
            // $this->payment_types    = PaymentType::tenanted()
            $this->payment_types    = PaymentType::where('payment_category_id', $type->payment_category_id)
                ->get();
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('livewire.payment-page');
    }

    public function updatedPaymentCategory($value)
    {
        // $this->payment_types = PaymentType::tenanted()->where('payment_category_id', $value)->get();
        $this->payment_types = PaymentType::where('payment_category_id', $value)->get();
        $this->payment_type  = $this->payment_types->first()->id ?? null;
    }
}
