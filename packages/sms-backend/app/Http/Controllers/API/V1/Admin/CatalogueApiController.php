<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreCatalogueRequest;
use App\Http\Requests\UpdateCatalogueRequest;
use App\Http\Resources\Admin\CatalogueResource;
use App\Models\Catalogue;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CatalogueApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('catalogue_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CatalogueResource(Catalogue::with(['channel'])->get());
    }

    public function store(StoreCatalogueRequest $request)
    {
        $catalogue = Catalogue::create($request->all());

        if ($request->input('catalogue', false)) {
            $catalogue->addMedia(storage_path('tmp/uploads/' . $request->input('catalogue')))->toMediaCollection('catalogue');
        }

        return (new CatalogueResource($catalogue))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Catalogue $catalogue)
    {
        abort_if(Gate::denies('catalogue_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CatalogueResource($catalogue->load(['channel']));
    }

    public function update(UpdateCatalogueRequest $request, Catalogue $catalogue)
    {
        $catalogue->update($request->all());

        if ($request->input('catalogue', false)) {
            if (!$catalogue->catalogue || $request->input('catalogue') !== $catalogue->catalogue->file_name) {
                if ($catalogue->catalogue) {
                    $catalogue->catalogue->delete();
                }

                $catalogue->addMedia(storage_path('tmp/uploads/' . $request->input('catalogue')))->toMediaCollection('catalogue');
            }
        } elseif ($catalogue->catalogue) {
            $catalogue->catalogue->delete();
        }

        return (new CatalogueResource($catalogue))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Catalogue $catalogue)
    {
        abort_if(Gate::denies('catalogue_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $catalogue->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
