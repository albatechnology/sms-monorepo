<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Admin\OrderResource;
use App\Models\Order;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class OrderApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new OrderResource(Order::with(['user', 'customer', 'address', 'channel', 'tax_invoice'])->get());
    }

    public function show(Order $order)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new OrderResource($order->load(['user', 'customer', 'address', 'channel', 'tax_invoice']));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->all());

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Order $order)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
