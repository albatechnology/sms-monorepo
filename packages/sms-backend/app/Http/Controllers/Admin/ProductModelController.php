<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductModelRequest;
use App\Http\Requests\StoreProductModelRequest;
use App\Http\Requests\UpdateProductModelRequest;
use App\Models\ProductModel;
use App\Models\Company;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductModelController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_model_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductModel::tenanted()->select(sprintf('%s.*', (new ProductModel())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_model_show';
                $editGate      = 'product_model_edit';
                $deleteGate    = 'product_model_delete';
                $crudRoutePart = 'product-models';

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
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
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

        return view('admin.productModels.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_model_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productModels.create', compact('companies'));
    }

    public function store(StoreProductModelRequest $request)
    {
        $productModel = ProductModel::create($request->all());

        foreach ($request->input('photo', []) as $file) {
            $productModel->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $productModel->id]);
        }

        return redirect()->route('admin.product-models.index');
    }

    public function edit(ProductModel $productModel)
    {
        abort_if(Gate::denies('product_model_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productModels.edit', compact('productModel'));
    }

    public function update(UpdateProductModelRequest $request, ProductModel $productModel)
    {
        $productModel->update($request->all());

        if (count($productModel->photo) > 0) {
            foreach ($productModel->photo as $media) {
                if (!in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $productModel->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $productModel->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.product-models.index');
    }

    public function show(ProductModel $productModel)
    {
        abort_if(Gate::denies('product_model_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productModels.show', compact('productModel'));
    }

    public function destroy(ProductModel $productModel)
    {
        abort_if(Gate::denies('product_model_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productModel->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductModelRequest $request)
    {
        ProductModel::tenanted()->whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_model_create') && Gate::denies('product_model_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ProductModel();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
