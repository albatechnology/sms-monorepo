<?php

namespace App\Http\Livewire;

use App\Models\Order;
use App\Models\OrderDetail;
use Livewire\Component;

class ShipmentPage extends Component
{
    // order dropdown
    public $orders = [];

    // selected order dropdown
    public $order = null;

    // order details of the selected order
    public $orderDetails = [];

    protected $listeners = ['setOrder' => 'setOrder'];

    public function mount()
    {
        $this->orders = Order::query()
            ->tenanted()
            ->with(['customer', 'channel'])
            //->whereWaitingDelivery()
            ->orderBy('expected_shipping_datetime')
            ->get()
            ->groupBy(function ($item) {
                return $item->expected_shipping_datetime?->format('Y-m-d') ?? 'no date';
            })
            ->all();
    }

    public function setOrder($id)
    {
        $this->order        = Order::find($id);
        $this->orderDetails = OrderDetail::where('order_id', $id)->with(['shipments'])->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('livewire.shipment-page');
    }
}
