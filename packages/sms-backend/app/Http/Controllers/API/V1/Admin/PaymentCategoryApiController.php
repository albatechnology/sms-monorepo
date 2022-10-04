<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentCategoryRequest;
use App\Http\Requests\UpdatePaymentCategoryRequest;
use App\Http\Resources\Admin\PaymentCategoryResource;
use App\Models\PaymentCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentCategoryApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('payment_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PaymentCategoryResource(PaymentCategory::with(['company'])->get());
    }

    public function store(StorePaymentCategoryRequest $request)
    {
        $paymentCategory = PaymentCategory::create($request->all());

        return (new PaymentCategoryResource($paymentCategory))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PaymentCategory $paymentCategory)
    {
        abort_if(Gate::denies('payment_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PaymentCategoryResource($paymentCategory->load(['company']));
    }

    public function update(UpdatePaymentCategoryRequest $request, PaymentCategory $paymentCategory)
    {
        $paymentCategory->update($request->all());

        return (new PaymentCategoryResource($paymentCategory))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PaymentCategory $paymentCategory)
    {
        abort_if(Gate::denies('payment_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategory->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
