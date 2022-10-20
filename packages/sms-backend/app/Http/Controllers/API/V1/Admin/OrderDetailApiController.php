<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Resources\Admin\OrderDetailResource;
use App\Models\OrderDetail;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderDetailApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('order_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new OrderDetailResource(OrderDetail::with(['product', 'order'])->get());
    }

    public function show(OrderDetail $orderDetail)
    {
        abort_if(Gate::denies('order_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new OrderDetailResource($orderDetail->load(['product', 'order']));
    }
}
