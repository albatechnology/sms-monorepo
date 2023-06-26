<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Jobs\GenerateProductBarcode;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductModel;
use App\Models\ProductVersion;
use App\Models\ProductCategoryCode;
use App\Models\ProductCategory;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;
    public function index(Request $request)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Product::tenanted()
                ->with(['productCategory', 'brand'])
                ->select(sprintf('%s.*', (new Product)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_show';
                $editGate      = 'product_edit';
                $deleteGate    = 'product_delete';
                $crudRoutePart = 'products';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            // $table->addColumn('barcode', function ($row) {
            //     return '<img src="data:image/png;base64,' . \DNS2D::getBarcodePNG(env('MOVES_PRODUCT_URL') . $row->id, 'QRCODE', 3, 3) . '" alt="barcode" /> <center><a href="' . url('admin/products/generateBarcode/' . $row->id) . '" class="btn btn-primary btn-xs">Print</a></center>';
            // });
            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('category', function ($row) {
                return $row->productCategory?->name ?? "";
            });
            $table->editColumn('brand', function ($row) {
                return $row->brand?->name ?? "";
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : "";
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
            $table->editColumn('is_active', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_active ? 'checked' : null) . '>';
            });
            // $table->addColumn('company_name', function ($row) {
            //     return $row->company ? $row->company->name : '';
            // });

            $table->rawColumns([
                'actions', 'placeholder', 'category',
                'brand', 'model', 'version', 'categoryCode',
                'tag', 'photo', 'is_active', 'barcode'
            ]);

            return $table->make(true);
        }

        $productCategories = ProductCategory::tenanted()->get();
        $productBrands = ProductBrand::tenanted()->get();
        // $companies          = Company::tenanted()->get();

        return view('admin.products.index', compact('productCategories', 'productBrands'));
    }

    public function create()
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productBrands = ProductBrand::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $categories = ProductCategory::tenanted()->pluck('name', 'id');

        // $companies = Company::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.products.create', compact('productBrands', 'categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        foreach ($request->input('photo', []) as $file) {
            $product->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $product->id]);
        }

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productBrands = ProductBrand::where('subscribtion_user_id', $product->subscribtion_user_id)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $categories = ProductCategory::where('subscribtion_user_id', $product->subscribtion_user_id)->pluck('name', 'id');

        // $companies = Company::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.products.edit', compact('productBrands', 'categories', 'product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        if (count($product->photo) > 0) {
            foreach ($product->photo as $media) {
                if (!in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }

        $media = $product->photo->pluck('file_name')->toArray();

        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $product->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.products.index');
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->load('productCategory', 'brand');

        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductRequest $request)
    {
        Product::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_create') && Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Product();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function getProductSuggestion(Request $request)
    {
        $products = Product::tenanted()->where('name', 'like', '%' . $request->name . '%');
        // if ($request->has('company_id') && $request->company_id != '') {
        //     $products->where('company_id', $request->company_id);
        // }
        $products = $products->get()->pluck('name', 'id');
        return response()->json($products);
    }

    // public function getModels(Request $request)
    // {
    //     $data = ProductModel::where('name', 'LIKE', "%" . $request->q . "%")->orWhere('description', 'LIKE', "%" . $request->q . "%")->get();

    //     return response()->json($data);
    // }

    // public function getVersions(Request $request)
    // {
    //     $data = ProductVersion::where('name', 'LIKE', "%" . $request->q . "%")->get();

    //     return response()->json($data);
    // }

    // public function getCategoryCodes(Request $request)
    // {
    //     $data = ProductCategoryCode::where('name', 'LIKE', "%" . $request->q . "%")->get();

    //     return response()->json($data);
    // }

    // public function generateBarcode(Request $request, $id = null)
    // {
    //     if ($request->post()) {
    //         $fileName = 'product-barcode-' . date('dmyhis');
    //         $export = \App\Models\Export::create([
    //             'user_id' => auth()->id(),
    //             'title' => $fileName,
    //             'file_name' => $fileName . '.pdf',
    //         ]);

    //         GenerateProductBarcode::dispatch($export, $request->except('_token'));
    //         return redirect()->back()->with('message', 'Generating product barcodes. Download in File Export menu');
    //     }

    //     $products = \Illuminate\Support\Facades\DB::table('products')->select('id', 'name')->where('id', $id)->get();

    //     return \PDF::loadView('admin.products.generateBarcode', ['products' => $products])->download();
    // }
}
