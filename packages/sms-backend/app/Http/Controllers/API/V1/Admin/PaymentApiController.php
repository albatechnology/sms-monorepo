<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\Admin\PaymentResource;
use App\Models\Payment;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PaymentResource(Payment::with(['payment_type', 'added_by', 'approved_by', 'order'])->get());
    }

    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create($request->all());

        if ($request->input('proof', false)) {
            $payment->addMedia(storage_path('tmp/uploads/' . basename($request->input('proof'))))->toMediaCollection('proof');
        }

        return (new PaymentResource($payment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Payment $payment)
    {
        abort_if(Gate::denies('payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PaymentResource($payment->load(['payment_type', 'added_by', 'approved_by', 'order']));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->all());

        if ($request->input('proof', false)) {
            if (!$payment->proof || $request->input('proof') !== $payment->proof->file_name) {
                if ($payment->proof) {
                    $payment->proof->delete();
                }

                $payment->addMedia(storage_path('tmp/uploads/' . basename($request->input('proof'))))->toMediaCollection('proof');
            }
        } elseif ($payment->proof) {
            $payment->proof->delete();
        }

        return (new PaymentResource($payment))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Payment $payment)
    {
        abort_if(Gate::denies('payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
