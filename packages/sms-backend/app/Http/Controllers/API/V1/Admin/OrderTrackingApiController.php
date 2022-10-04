<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderTrackingResource;
use App\Models\OrderTracking;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderTrackingApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('order_tracking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new OrderTrackingResource(OrderTracking::with(['order'])->get());
    }

    public function show(OrderTracking $orderTracking)
    {
        abort_if(Gate::denies('order_tracking_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new OrderTrackingResource($orderTracking->load(['order']));
    }
}
