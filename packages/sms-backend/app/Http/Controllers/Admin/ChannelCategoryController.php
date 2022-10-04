<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyChannelCategoryRequest;
use App\Http\Requests\StoreChannelCategoryRequest;
use App\Http\Requests\UpdateChannelCategoryRequest;
use App\Models\ChannelCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChannelCategoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('channel_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channelCategories = ChannelCategory::all();

        return view('admin.channelCategories.index', compact('channelCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('channel_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.channelCategories.create');
    }

    public function store(StoreChannelCategoryRequest $request)
    {
        $channelCategory = ChannelCategory::create($request->validated());

        return redirect()->route('admin.channel-categories.index');
    }

    public function edit(ChannelCategory $channelCategory)
    {
        abort_if(Gate::denies('channel_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.channelCategories.edit', compact('channelCategory'));
    }

    public function update(UpdateChannelCategoryRequest $request, ChannelCategory $channelCategory)
    {
        $channelCategory->update($request->validated());

        return redirect()->route('admin.channel-categories.index');
    }

    public function show(ChannelCategory $channelCategory)
    {
        abort_if(Gate::denies('channel_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channelCategory->load('channelCategoryChannels');

        return view('admin.channelCategories.show', compact('channelCategory'));
    }

    public function destroy(ChannelCategory $channelCategory)
    {
        abort_if(Gate::denies('channel_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channelCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyChannelCategoryRequest $request)
    {
        ChannelCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
