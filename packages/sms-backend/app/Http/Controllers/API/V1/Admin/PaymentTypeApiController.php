<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentTypeRequest;
use App\Http\Requests\UpdatePaymentTypeRequest;
use App\Http\Resources\Admin\PaymentTypeResource;
use App\Models\PaymentType;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentTypeApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('payment_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PaymentTypeResource(PaymentType::with(['payment_category', 'company'])->get());
    }

    public function store(StorePaymentTypeRequest $request)
    {
        $paymentType = PaymentType::create($request->all());

        return (new PaymentTypeResource($paymentType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PaymentType $paymentType)
    {
        abort_if(Gate::denies('payment_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PaymentTypeResource($paymentType->load(['payment_category', 'company']));
    }

    public function update(UpdatePaymentTypeRequest $request, PaymentType $paymentType)
    {
        $paymentType->update($request->all());

        return (new PaymentTypeResource($paymentType))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PaymentType $paymentType)
    {
        abort_if(Gate::denies('payment_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentType->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
