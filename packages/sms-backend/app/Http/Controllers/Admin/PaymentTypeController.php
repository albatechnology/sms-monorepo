<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPaymentTypeRequest;
use App\Http\Requests\StorePaymentTypeRequest;
use App\Http\Requests\UpdatePaymentTypeRequest;
// use App\Models\Company;
use App\Models\PaymentCategory;
use App\Models\PaymentType;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class PaymentTypeController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('payment_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentTypes = PaymentType::tenanted()->with(['payment_category'])->get();

        $payment_categories = PaymentCategory::tenanted()->get();

        // $companies = Company::get();

        return view('admin.paymentTypes.index', compact('paymentTypes', 'payment_categories'));
    }

    public function create()
    {
        abort_if(Gate::denies('payment_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategories = PaymentCategory::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.paymentTypes.create', compact('paymentCategories'));
    }

    public function store(StorePaymentTypeRequest $request)
    {
        $paymentType = PaymentType::create($request->validated());

        foreach ($request->input('photo', []) as $file) {
            $paymentType->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $paymentType->id]);
        }

        return redirect()->route('admin.payment-types.index');
    }

    public function edit(PaymentType $paymentType)
    {
        abort_if(Gate::denies('payment_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategories = PaymentCategory::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $paymentType->load('payment_category');

        return view('admin.paymentTypes.edit', compact('paymentCategories', 'paymentType'));
    }

    public function update(UpdatePaymentTypeRequest $request, PaymentType $paymentType)
    {
        $paymentType->update($request->validated());

        if (count($paymentType->photo) > 0) {
            foreach ($paymentType->photo as $media) {
                if (!in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $paymentType->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $paymentType->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.payment-types.index');
    }

    public function show(PaymentType $paymentType)
    {
        abort_if(Gate::denies('payment_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentType->load('payment_category', 'paymentTypePayments');

        return view('admin.paymentTypes.show', compact('paymentType'));
    }

    public function destroy(PaymentType $paymentType)
    {
        abort_if(Gate::denies('payment_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentType->delete();

        return back();
    }

    public function massDestroy(MassDestroyPaymentTypeRequest $request)
    {
        PaymentType::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('payment_type_create') && Gate::denies('payment_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new PaymentType();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
