<?php

namespace App\Services;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderStockStatus;
use App\Enums\StockHistoryType;
use App\Enums\StockTransferStatus;
use App\Models\Channel;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\StockTransfer;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;


class StockService
{
    /**
     * @param Channel $channelFrom
     * @param Channel $channelTo
     * @param Product $product
     * @param int $amount
     * @return MessageBag|StockTransfer
     */
    public static function createStockTransfer(
        Channel $channelFrom,
        Channel $channelTo,
        Product $product,
        int $amount
    ) {
        $message = new MessageBag();

        // Can only transfer within company
        // if ($channelFrom->company_id !== $channelTo->company_id) {
        //     $message->add('channel_to', 'Stock can only move between channel of the same company.');
        //     return $message;
        // }

        // // product product stock must exist on both channels
        // $stockFrom = Stock::where('product_id', $product)
        //     ->where('channel_id', $channelFrom)
        //     ->first();

        // $stockTo = Stock::where('product_id', $product)
        //     ->where('channel_id', $channelTo)
        //     ->first();

        // if (!$stockFrom) {
        //     $message->add('channel_from', 'Product product doesn\'t exist on the selected channel.');
        // }

        // if (!$stockTo) {
        //     $message->add('channel_to', 'Product product doesn\'t exist on the selected channel.');
        // }

        // // check the sender channel has the stock amount
        // if ($stockFrom && ($stockFrom->stock < $amount)) {

        //     $errMessage = sprintf(
        //         'Insufficient stock. Channel %s only have %s stock remaining.',
        //         $channelFrom->name,
        //         $stockFrom->stock
        //     );

        //     $message->add('amount', $errMessage);
        // }

        // if ($message->isNotEmpty()) {
        //     return $message;
        // }

        // return StockTransfer::create(
        //     [
        //         'stock_from_id'   => $stockFrom->id,
        //         'stock_to_id'     => $stockTo->id,
        //         'requested_by_id' => user()->id,
        //         'amount'          => $amount,
        //         'company_id'      => $channelFrom->company_id,
        //         'status'          => StockTransferStatus::PENDING(),
        //     ]
        // );
    }

    /**
     * Attempt to fulfil order.
     * @param Order $order
     */
    public static function fulfillOrder(Order $order): void
    {
        if ($order->stock_status->is(OrderStockStatus::FULFILLED)) {
            return;
        }

        $order->order_details->each(function (OrderDetail $detail) use ($order) {
            self::fulfillOrderDetail($detail, $order, false);
        });

        $order->refreshStockStatus();
        $order->save();
    }

    /**
     * @param Stock $stock
     * @param int $amount
     * @return Stock
     */
    public static function manualEditStock(Stock $stock, int $amount): Stock
    {
        return DB::transaction(function () use ($stock, $amount) {

            $stock->addStock($amount);

            StockHistory::create(
                [
                    'stock_id'   => $stock->id,
                    'quantity'   => $amount,
                    'type'       => StockHistoryType::MANUAL(),
                    'user_id'    => user()->id,
                    'company_id' => $stock->company_id,
                ]
            );

            return $stock;
        });
    }

    /**
     * Attempt to fulfil order detail. If there is insufficient
     * stock, allow for indent.
     * @param OrderDetail $detail
     * @param Order|null $order
     * @param bool $updateOrder
     */
    public static function fulfillOrderDetail(
        OrderDetail $detail,
        Order $order = null,
        bool $updateOrder = true,
    ): void {
        $order = $order ?? $detail->order;

        /** @var Stock|null $stock */
        $stock = Stock::query()
            // ->where('channel_id', $order->channel_id)
            ->where('location_id', $detail->location_id)
            ->where('product_id', $detail->product_id)
            ->first();

        if (!$stock) {
            return;
        }

        DB::transaction(function () use ($detail, $stock, $order) {
            $quantityRequired = $detail->quantity - $detail->quantity_fulfilled;
            $stockAvailable   = $stock->stock;
            $fulfilAmount     = $quantityRequired > $stockAvailable ? $stockAvailable : $quantityRequired;

            $stock->deductStock($fulfilAmount);

            // start sync stock transfer
            // $stockTransfers = $order->stockTransfers()->where('product_id', $detail->product_id)->where('status', StockTransferStatus::PENDING)->get();
            // $amount = 0;

            // foreach ($stockTransfers as $stockTransfer) {
            //     $sourceStock = $stockTransfer->fromChannel->channelStocks()->where('product_id', $stockTransfer->product_id)->first();
            //     $sourceStock->deductStock($stockTransfer->amount);

            //     $stockTransfer->update([
            //         'status' => StockTransferStatus::COMPLETE
            //     ]);

            //     $amount += $stockTransfer->amount;
            // }

            // $stock->addStock($amount);
            // end sync stock transfer

            // $stock = Stock::query()
            //     // ->where('channel_id', $order->channel_id)
            //     ->where('location_id', $detail->location_id)
            //     ->where('product_id', $detail->product_id)
            //     ->first();

            // $stock->deductStock($quantityRequired);

            // StockHistory::create(
            //     [
            //         'stock_id'        => $stock->id,
            //         'quantity'        => $quantityRequired * -1,
            //         'type'            => StockHistoryType::ORDER(),
            //         'order_detail_id' => $detail->id,
            //         'user_id'         => $order->user_id,
            //         'company_id'      => $order->company_id,
            //     ]
            // );

            $detail->quantity_fulfilled += $quantityRequired;
            $detail->refreshStatus();
            $detail->save();
        });

        if ($updateOrder) {
            $order->refreshStockStatus();
            $order->save();
        }
    }

    /**
     * @param StockTransfer $transfer
     * @return StockTransfer
     * @throws Exception
     */
    public static function processStockTransfer(StockTransfer $transfer): StockTransfer
    {
        // dont process if this stock transfer has been processed before
        if ($transfer->stockHistories()->exists()) {
            return $transfer;
        }

        try {
            $transfer = DB::transaction(function () use ($transfer) {

                $stockFrom = $transfer->stock_from;
                $stockTo   = $transfer->stock_to;
                $quantity  = $transfer->amount;

                // move the stock
                $stockFrom->deductStock($quantity);
                $stockTo->addStock($quantity);

                // add to stock history
                StockHistory::create(
                    [
                        'stock_id'          => $stockFrom->id,
                        'quantity'          => $quantity * -1,
                        'type'              => StockHistoryType::TRANSFER(),
                        'stock_transfer_id' => $transfer->id,
                        'user_id'           => user()->id,
                        'company_id'        => $stockFrom->company_id,
                    ]
                );

                StockHistory::create(
                    [
                        'stock_id'          => $stockTo->id,
                        'quantity'          => $quantity,
                        'type'              => StockHistoryType::TRANSFER(),
                        'stock_transfer_id' => $transfer->id,
                        'user_id'           => user()->id,
                        'company_id'        => $stockTo->company_id,
                    ]
                );

                // update the stock transfer
                $transfer->update(['status' => StockTransferStatus::COMPLETE]);
                return $transfer;
            });
        } catch (Exception $e) {

            $transfer->update(['status' => StockTransferStatus::FAILED]);
            throw new Exception();
        }

        return $transfer;
    }

    public static function outstandingOrder($companyId, $channelId, $productId)
    {
        $total = DB::table('orders')->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->whereIn('orders.payment_status', [OrderPaymentStatus::NONE(), OrderPaymentStatus::PARTIAL(), OrderPaymentStatus::DOWN_PAYMENT()])->where('orders.company_id', $companyId)->where('orders.channel_id', $channelId)->where('order_details.product_id', $productId)->select(DB::raw('sum((order_details.quantity - order_details.quantity_fulfilled)) as outstanding_order'))->groupBy('orders.id')->get()->sum('outstanding_order');
        return $total;
    }

    public static function outstandingShipment($companyId, $channelId, $productId)
    {
        $total = DB::table('orders')->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.payment_status', OrderPaymentStatus::SETTLEMENT())->where('orders.status', OrderStatus::SHIPMENT())->where('orders.company_id', $companyId)->where('orders.channel_id', $channelId)->where('order_details.product_id', $productId)->select(DB::raw('sum(order_details.quantity) as outstanding_shipment'))->groupBy('orders.id')->get()->sum('outstanding_shipment');
        return $total;
    }
}
