<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChannelCategoryRequest;
use App\Http\Requests\UpdateChannelCategoryRequest;
use App\Http\Resources\Admin\ChannelCategoryResource;
use App\Models\ChannelCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChannelCategoryApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('channel_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ChannelCategoryResource(ChannelCategory::all());
    }

    public function store(StoreChannelCategoryRequest $request)
    {
        $channelCategory = ChannelCategory::create($request->all());

        return (new ChannelCategoryResource($channelCategory))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ChannelCategory $channelCategory)
    {
        abort_if(Gate::denies('channel_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ChannelCategoryResource($channelCategory);
    }

    public function update(UpdateChannelCategoryRequest $request, ChannelCategory $channelCategory)
    {
        $channelCategory->update($request->all());

        return (new ChannelCategoryResource($channelCategory))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ChannelCategory $channelCategory)
    {
        abort_if(Gate::denies('channel_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channelCategory->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
