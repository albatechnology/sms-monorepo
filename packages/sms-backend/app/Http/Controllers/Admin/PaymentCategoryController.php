<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPaymentCategoryRequest;
use App\Http\Requests\StorePaymentCategoryRequest;
use App\Http\Requests\UpdatePaymentCategoryRequest;
// use App\Models\Company;
use App\Models\PaymentCategory;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class PaymentCategoryController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('payment_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategories = PaymentCategory::tenanted()->get();
        // dd($paymentCategories);
        return view('admin.paymentCategories.index', compact('paymentCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('payment_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $companies = Company::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.paymentCategories.create');
    }

    public function store(StorePaymentCategoryRequest $request)
    {
        $paymentCategory = PaymentCategory::create($request->validated());

        foreach ($request->input('photo', []) as $file) {
            $paymentCategory->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $paymentCategory->id]);
        }

        return redirect()->route('admin.payment-categories.index');
    }

    public function edit(PaymentCategory $paymentCategory)
    {
        abort_if(Gate::denies('payment_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $companies = Company::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $paymentCategory->load('company');

        return view('admin.paymentCategories.edit', compact('paymentCategory'));
    }

    public function update(UpdatePaymentCategoryRequest $request, PaymentCategory $paymentCategory)
    {
        $paymentCategory->update($request->validated());

        if (count($paymentCategory->photo) > 0) {
            foreach ($paymentCategory->photo as $media) {
                if (!in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $paymentCategory->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $paymentCategory->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.payment-categories.index');
    }

    public function show(PaymentCategory $paymentCategory)
    {
        abort_if(Gate::denies('payment_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategory->load('paymentCategoryPaymentTypes');

        return view('admin.paymentCategories.show', compact('paymentCategory'));
    }

    public function destroy(PaymentCategory $paymentCategory)
    {
        abort_if(Gate::denies('payment_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paymentCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyPaymentCategoryRequest $request)
    {
        PaymentCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('payment_category_create') && Gate::denies('payment_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new PaymentCategory();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
