<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Http\Resources\Admin\BannerResource;
use App\Models\Banner;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BannerApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('banner_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BannerResource(Banner::with(['company'])->get());
    }

    public function store(StoreBannerRequest $request)
    {
        $banner = Banner::create($request->all());

        return (new BannerResource($banner))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Banner $banner)
    {
        abort_if(Gate::denies('banner_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BannerResource($banner->load(['company']));
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $banner->update($request->all());

        return (new BannerResource($banner))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Banner $banner)
    {
        abort_if(Gate::denies('banner_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $banner->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
