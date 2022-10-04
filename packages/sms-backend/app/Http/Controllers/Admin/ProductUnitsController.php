<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductUnitRequest;
use App\Http\Requests\StoreProductUnitRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Colour;
use App\Models\Covering;
use App\Models\Company;
use App\Models\ProductUnit;
use App\Services\HelperService;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductUnitsController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {

            $query = ProductUnit::tenanted()->with(['product']);
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_unit_show';
                $editGate      = 'product_unit_edit';
                $deleteGate    = 'product_unit_delete';
                $crudRoutePart = 'product-units';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?? "";
            });

            $table->addColumn('product_name', function ($row) {
                return $row->product?->name ?? '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ?? "";
            });
            $table->editColumn('sku', function ($row) {
                return $row->sku ?? "";
            });
            $table->editColumn('price', function ($row) {
                return HelperService::formatRupiah($row->price) ?? "";
            });
            $table->editColumn('is_active', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_active ? 'checked' : null) . '>';
            });
            $table->editColumn('product_unit_category', function ($row) {
                return $row->product_unit_category?->description ?? '';
            });

            $table->rawColumns(['actions', 'placeholder', 'product', 'is_active', 'product_unit_category']);

            return $table->make(true);
        }

        return view('admin.productUnits.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_unit_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $products = Product::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.productUnits.create', compact('companies'));
    }

    public function getColour($product_id)
    {
        $colours = Colour::tenanted()
            ->where('product_id', $product_id)
            ->get()
            ->pluck('name', 'id');

        return response()->json($colours);
    }

    public function getCovering($product_id)
    {
        $coverings = Covering::tenanted()
            ->where('product_id', $product_id)
            ->get()
            ->pluck('name', 'id');

        return response()->json($coverings);
    }

    public function store(StoreProductUnitRequest $request)
    {
        $productUnit = ProductUnit::create($request->validated());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $productUnit->id]);
        }

        return redirect()->route('admin.product-units.index');
    }

    public function edit(ProductUnit $productUnit)
    {
        abort_if(Gate::denies('product_unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productUnit->load('product');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $colours = Colour::tenanted()
            ->where('product_id', $productUnit->product_id)
            ->get()
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $coverings = Covering::tenanted()
            ->where('product_id', $productUnit->product_id)
            ->get()
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');


        return view('admin.productUnits.edit', compact('companies', 'colours', 'coverings', 'productUnit'));
    }

    public function update(UpdateProductUnitRequest $request, ProductUnit $productUnit)
    {
        $productUnit->update($request->validated());

        return redirect()->route('admin.product-units.index');
    }

    public function show(ProductUnit $productUnit)
    {
        abort_if(Gate::denies('product_unit_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productUnit->load('product', 'productUnitItemProductUnits', 'productUnitOrderDetails', 'productunitsPromos');

        return view('admin.productUnits.show', compact('productUnit'));
    }

    public function destroy(ProductUnit $productUnit)
    {
        abort_if(Gate::denies('product_unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productUnit->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductUnitRequest $request)
    {
        ProductUnit::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_unit_create') && Gate::denies('product_unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ProductUnit();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function export(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer',
            'product_brand_id' => 'required|integer',
            'start_id' => 'required|integer|lt:end_id',
            'end_id' => 'required|integer|gt:start_id',
        ]);
        $productBrandName = \App\Models\ProductBrand::where('id', $request->product_brand_id)->select('name')->first()->name;
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProductUnitExport($request->except('_token')), 'product-units-' . $productBrandName . '-' . $request->start_id . '-' . $request->end_id . '.csv');
    }

    public function getProductUnitSuggestion(Request $request, $company_id = null)
    {
        $products = ProductUnit::select('id', 'name');
        if ($company_id) $products->where('company_id', $company_id);
        $products = $products->where('name', 'like', '%' . $request->q . '%')->get();
        return response()->json($products);
    }

    public function updateBrandCategoryIds($skip, $take)
    {
        echo "skip = " . $skip;
        echo "<br/>";
        echo "take = " . $take;
        echo "<br/><br/>";

        $productUnits = ProductUnit::skip($skip)->take($take)->get();
        if(count($productUnits) <= 0) {
            die('SELESAI SUDAH');
        }
        $success = 0;
        $error = 0;
        foreach ($productUnits as $pu) {
            // $brandCategoryIds = \App\Models\Product::findOrFail($pu->product_id)->brand->productBrandCategories->map(function($p){
            //     return $p->brand_category_id;
            // })->toArray();
            // if($pu->update(['brand_category_ids' => $brandCategoryIds])){
            //     $success++;
            // } else {
            //     $error++;
            // }
            if($pu->save()){
                $success++;
            } else {
                $error++;
            }
        }

        echo "<br/>";
        echo '<h1>JANGAN SENTUH KOMPUTER COKKK !!!</h1>';
        echo "<br/>";
        echo 'Success : ' . $success. '. Error : ' . $error;
        $new_skip = $take;
        $new_take = $take + ($take - $skip);


        echo "<br/><br/>";
        $url = url('admin/product-units/updateBrandCategoryIds') .'/'. $new_skip . "/" . $new_take;
        echo "next url= " . $url;

		// echo '<audio autoplay><source src="'.asset('doraemon.mp3').'" type="audio/mpeg"></audio>';
        echo "<script>";
        echo "setTimeout(function(){
			window.location.href = '" . $url . "';
		}, 4000)";
        echo "</script>";

        // return redirect()->back()->withStatus('Success ' . $success . '. ' . $skip . '-' . $take);
    }
}
