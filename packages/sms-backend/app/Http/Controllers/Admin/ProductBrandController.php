<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductBrandRequest;
use App\Http\Requests\StoreProductBrandRequest;
use App\Http\Requests\UpdateProductBrandRequest;
use App\Models\ProductBrand;
use App\Models\BrandCategory;
use App\Models\Currency;
use App\Models\ProductBrandCategory;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductBrandController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_brand_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductBrand::tenanted()->with(['brandCategory'])->select(sprintf('%s.*', (new ProductBrand())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_brand_show';
                $editGate      = 'product_brand_edit';
                $deleteGate    = 'product_brand_delete';
                $crudRoutePart = 'product-brands';

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
            // $table->editColumn('company_name', function ($row) {
            //     return $row->company->name ? $row->company->name : '';
            // });
            $table->editColumn('hpp_calculation', function ($row) {
                return $row->hpp_calculation ? $row->hpp_calculation . '%' : '';
            });
            $table->editColumn('currency_id', function ($row) {
                return $row->currency ? $row->currency->main_currency . ' - ' . $row->currency->foreign_currency . ' Rp.' . number_format($row->currency->value) : '';
            });
            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->addColumn('brand_category', function ($row) {
                return $row->brandCategory?->name ?? '';
            });
            $table->editColumn('photo', function ($row) {
                if (!$row->photo) {
                    return '';
                }
                $links = [];
                foreach ($row->photo as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank"><img src="' . $media->getUrl('thumb') . '" width="50px" height="50px"></a>';
                }

                return implode(' ', $links);
            });

            $table->rawColumns(['actions', 'placeholder', 'photo']);

            return $table->make(true);
        }
        // $companies = Company::tenanted()->pluck('name', 'id');
        return view('admin.productBrands.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_brand_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $companies = Company::tenanted()->get()->pluck('name', 'id');
        $brandCategories = BrandCategory::tenanted()->get();

        return view('admin.productBrands.create', compact('brandCategories'));
    }

    public function store(StoreProductBrandRequest $request)
    {
        $productBrand = ProductBrand::create($request->all());
        // ProductBrandCategory::create([
        //     'product_brand_id' => $productBrand->id,
        //     'brand_category_id' => $request->brand_category_id,
        // ]);

        foreach ($request->input('photo', []) as $file) {
            $productBrand->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $productBrand->id]);
        }

        return redirect()->route('admin.product-brands.index');
    }

    public function edit(ProductBrand $productBrand)
    {
        abort_if(Gate::denies('product_brand_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $brandCategories = BrandCategory::tenanted()->get();

        return view('admin.productBrands.edit', compact('productBrand', 'brandCategories'));
    }

    public function update(UpdateProductBrandRequest $request, ProductBrand $productBrand)
    {
        $productBrand->update($request->validated());
        // $productBrand->productBrandCategories()->forceDelete();

        // ProductBrandCategory::create([
        //     'product_brand_id' => $productBrand->id,
        //     'brand_category_id' => $request->brand_category_id,
        // ]);

        if (count($productBrand->photo) > 0) {
            foreach ($productBrand->photo as $media) {
                if (!in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }

        $media = $productBrand->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $productBrand->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.product-brands.index');
    }

    public function show(ProductBrand $productBrand)
    {
        abort_if(Gate::denies('product_brand_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productBrands.show', compact('productBrand'));
    }

    public function destroy(ProductBrand $productBrand)
    {
        abort_if(Gate::denies('product_brand_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productBrand->productBrandCategories()->delete();
        $productBrand->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductBrandRequest $request)
    {
        ProductBrand::whereIn('id', request('ids'))->delete();
        ProductBrandCategory::whereIn('product_brand_id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_brand_create') && Gate::denies('product_brand_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ProductBrand();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function getProductBrand($brandCategoryId)
    {
        $data = ProductBrand::tenanted()->whereHas('brandCategories', fn ($q) => $q->where('brand_category_id', $brandCategoryId))->pluck('name', 'id');

        return response()->json($data);
    }

    public function ajaxActivationData(Request $request)
    {
        if ($request->ajax()) {
            if (\Illuminate\Support\Facades\DB::table('product_brands')->where('id', $request->id)->update([$request->column => $request->val])) {
                if ($request->val == 1) {
                    $success = true;
                    $message = 'Active';
                } else {
                    $success = false;
                    $message = 'Inactive';
                }
            } else {
                $success = false;
                $message = 'Inactive';
            }
            return ['success' => $success, 'message' => $message];
        }
        return ['success' => false, 'message' => 'Request Rejected'];
    }

    public function ajaxGetProductBrands(Request $request)
    {
        if ($request->ajax()) {
            $productBrands = ProductBrand::tenanted();
            // if ($request->company_id) {
            //     $company_id = explode(',', $request->company_id);
            //     $productBrands = $productBrands->whereIn('company_id', $company_id ?? []);
            // }
            return $productBrands->get(['id', 'name']);
        }
    }
}
