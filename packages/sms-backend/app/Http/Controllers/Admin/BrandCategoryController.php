<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBrandCategoryRequest;
use App\Http\Requests\StoreBrandCategoryRequest;
use App\Http\Requests\UpdateBrandCategoryRequest;
use App\Models\BrandCategory;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class BrandCategoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('brand_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $brandCategories = BrandCategory::tenanted()->get();

        return view('admin.brandCategories.index', compact('brandCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('brand_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.brandCategories.create');
    }

    public function store(StoreBrandCategoryRequest $request)
    {
        $brandCategory = BrandCategory::create($request->validated());

        return redirect()->route('admin.brand-categories.index');
    }

    public function edit(BrandCategory $brandCategory)
    {
        abort_if(Gate::denies('brand_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.brandCategories.edit', compact('brandCategory'));
    }

    public function update(UpdateBrandCategoryRequest $request, BrandCategory $brandCategory)
    {
        $brandCategory->update($request->validated());

        return redirect()->route('admin.brand-categories.index');
    }

    public function show(BrandCategory $brandCategory)
    {
        abort_if(Gate::denies('brand_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $brandCategory->load('brandCategoryBrands');

        return view('admin.brandCategories.show', compact('brandCategory'));
    }

    public function destroy(BrandCategory $brandCategory)
    {
        abort_if(Gate::denies('brand_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $brandCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyBrandCategoryRequest $request)
    {
        BrandCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
