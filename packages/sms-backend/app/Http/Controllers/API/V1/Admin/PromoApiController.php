<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Http\Resources\Admin\PromoResource;
use App\Models\Promo;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class PromoApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('promo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PromoResource(Promo::with(['company'])->get());
    }

    public function store(StorePromoRequest $request)
    {
        $promo = Promo::create($request->all());

        if ($request->input('image', false)) {
            $promo->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new PromoResource($promo))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Promo $promo)
    {
        abort_if(Gate::denies('promo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PromoResource($promo->load(['company']));
    }

    public function update(UpdatePromoRequest $request, Promo $promo)
    {
        $promo->update($request->all());

        if ($request->input('image', false)) {
            if (!$promo->image || $request->input('image') !== $promo->image->file_name) {
                if ($promo->image) {
                    $promo->image->delete();
                }

                $promo->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($promo->image) {
            $promo->image->delete();
        }

        return (new PromoResource($promo))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Promo $promo)
    {
        abort_if(Gate::denies('promo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promo->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
