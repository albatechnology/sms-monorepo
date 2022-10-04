<?php


namespace Tests\Helpers;


use App\Enums\ShipmentStatus;
use App\Events\ShipmentUpdated;
use App\Models\Channel;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderDetailShipment;
use App\Models\Shipment;
use App\Models\User;
use App\Services\OrderService;
use Exception;
use Illuminate\Support\Collection;

/**
 * Not an actual cart, but cart to hold all required data to create
 * an order. Used when there is a need for a more refined control
 * over order data compared to using order factory.
 *
 * Class TestCart
 * @package Tests\Helpers\
 */
class TestCart
{
    public ?Lead $lead = null;
    public Collection $cartLines;

    // for consistency check
    public ?int $companyId = null;
    public ?int $channelId = null;

    public function __construct()
    {
        $this->cartLines = collect([]);
    }

    /**
     * Add cart line to the cart
     * @param TestCartLine $line
     * @return $this
     * @throws Exception
     */
    public function addCartLine(TestCartLine $line): self
    {
        $this->checkCompanyId($line->productUnit->company_id);

        $this->cartLines->push($line);
        return $this;
    }

    /**
     * Set the lead for the cart
     * @param Lead $lead
     * @return $this
     * @throws Exception
     */
    public function setLead(Lead $lead): self
    {
        $this->checkChannelId($lead->channel_id);
        $this->lead = $lead;
        return $this;
    }

    /**
     * Check that company is valid for this cart.
     * Sets the company id when it is null
     * @param int $companyId
     * @throws Exception
     */
    protected function checkCompanyId(int $companyId): void
    {
        if (!$this->companyId) {
            $this->companyId = $companyId;
        }

        if ($this->companyId !== $companyId) {
            throw new Exception('Non matching company id added to test cart');
        }
    }

    /**
     * Check that channel is valid for this cart.
     * Sets the channel id when it is null
     * @param int $channelId
     * @throws Exception
     */
    protected function checkChannelId(int $channelId): void
    {
        if (!$this->channelId) {
            $this->checkCompanyId(Channel::findOrFail($channelId)->company_id);

            $this->channelId = $channelId;
        }

        if ($this->channelId !== $channelId) {
            throw new Exception('Non matching channel id added to test cart');
        }
    }

    /**
     * Create new order from this cart
     * @param array $attributes
     * @return Order
     */
    public function createOrder(array $attributes = []): Order
    {
        $this->prepareForOrder();

        $raw_source = [
            'items' => $this->cartLines
                ->map(function (TestCartLine $line) {
                    return [
                        'id'       => $line->productUnit->id,
                        'quantity' => $line->quantity
                    ];
                })
                ->all(),

            'shipping_address_id' => $this->lead->customer->default_address_id,
            'billing_address_id'  => $this->lead->customer->default_address_id,
            'lead_id'             => $this->lead->id,
        ];

        $raw_source = array_merge($raw_source, $attributes);

        return app(OrderService::class)
            ->processOrder(Order::make(['raw_source' => $raw_source]))
            ->refresh();
    }

    /**
     * Fill in empty data in preparation to create a new order
     */
    protected function prepareForOrder(): void
    {
        if (!$this->lead) {
            $this->setLead(Lead::factory()->create(
                [
                    'channel_id' => $this->channelId ?? Channel::factory()->create(['company_id' => $this->companyId])->id,
                    //user_id
                ]
            ));
        }
    }

    /**
     * Take this cart as shipment against a given order
     * @param Order $order
     * @param ShipmentStatus $status
     * @return Shipment
     */
    public function createShipment(Order $order, ShipmentStatus $status): Shipment
    {
        $shipment                  = new Shipment();
        $shipment->order_id        = $order->id;
        $shipment->status          = $status;
        $shipment->fulfilled_by_id = auth()?->user()?->id ?? User::factory()->first();
        $shipment->save();

        $details = $order->order_details->keyBy('product_unit_id');

        collect($this->cartLines)->each(function (TestCartLine $line) use ($shipment, $details) {
            OrderDetailShipment::create([
                'shipment_id'     => $shipment->id,
                'order_detail_id' => $details[$line->productUnit->id]->id,
                'quantity'        => $line->quantity,
            ]);
        });

        ShipmentUpdated::dispatch($shipment);
        return $shipment;
    }
}