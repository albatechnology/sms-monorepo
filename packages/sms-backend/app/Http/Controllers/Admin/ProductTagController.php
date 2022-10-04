<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyProductTagRequest;
use App\Http\Requests\StoreProductTagRequest;
use App\Http\Requests\UpdateProductTagRequest;
use App\Models\Company;
use App\Models\ProductTag;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductTagController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('product_tag_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productTags = ProductTag::with(['company'])->get();

        return view('admin.productTags.index', compact('productTags'));
    }

    public function create()
    {
        abort_if(Gate::denies('product_tag_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productTags.create', compact('companies'));
    }

    public function store(StoreProductTagRequest $request)
    {
        $productTag = ProductTag::create($request->validated());

        return redirect()->route('admin.product-tags.index');
    }

    public function edit(ProductTag $productTag)
    {
        abort_if(Gate::denies('product_tag_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productTag->load('company');

        return view('admin.productTags.edit', compact('companies', 'productTag'));
    }

    public function update(UpdateProductTagRequest $request, ProductTag $productTag)
    {
        $productTag->update($request->validated());

        return redirect()->route('admin.product-tags.index');
    }

    public function show(ProductTag $productTag)
    {
        abort_if(Gate::denies('product_tag_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productTag->load('company');

        return view('admin.productTags.show', compact('productTag'));
    }

    public function destroy(ProductTag $productTag)
    {
        abort_if(Gate::denies('product_tag_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productTag->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductTagRequest $request)
    {
        ProductTag::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
