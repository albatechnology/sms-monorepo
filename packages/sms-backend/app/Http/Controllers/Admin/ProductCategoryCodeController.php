<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyProductCategoryCodeRequest;
use App\Http\Requests\StoreProductCategoryCodeRequest;
use App\Http\Requests\UpdateProductCategoryCodeRequest;
use App\Models\Company;
use App\Models\ProductCategoryCode;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductCategoryCodeController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_category_code_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductCategoryCode::tenanted()->select(sprintf('%s.*', (new ProductCategoryCode())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_category_code_show';
                $editGate      = 'product_category_code_edit';
                $deleteGate    = 'product_category_code_delete';
                $crudRoutePart = 'product-category-codes';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.productCategoryCodes.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_category_code_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productCategoryCodes.create', compact('companies'));
    }

    public function store(StoreProductCategoryCodeRequest $request)
    {
        $productCategoryCode = ProductCategoryCode::create($request->all());

        return redirect()->route('admin.product-category-codes.index');
    }

    public function edit(ProductCategoryCode $productCategoryCode)
    {
        abort_if(Gate::denies('product_category_code_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productCategoryCodes.edit', compact('productCategoryCode'));
    }

    public function update(UpdateProductCategoryCodeRequest $request, ProductCategoryCode $productCategoryCode)
    {
        $productCategoryCode->update($request->all());

        return redirect()->route('admin.product-category-codes.index');
    }

    public function show(ProductCategoryCode $productCategoryCode)
    {
        abort_if(Gate::denies('product_category_code_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productCategoryCodes.show', compact('productCategoryCode'));
    }

    public function destroy(ProductCategoryCode $productCategoryCode)
    {
        abort_if(Gate::denies('product_category_code_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productCategoryCode->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductCategoryCodeRequest $request)
    {
        ProductCategoryCode::tenanted()->whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
