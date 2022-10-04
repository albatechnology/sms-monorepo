<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductVersionRequest;
use App\Http\Requests\StoreProductVersionRequest;
use App\Http\Requests\UpdateProductVersionRequest;
use App\Models\Company;
use App\Models\ProductVersion;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductVersionController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_version_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductVersion::tenanted()->select(sprintf('%s.*', (new ProductVersion())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_version_show';
                $editGate      = 'product_version_edit';
                $deleteGate    = 'product_version_delete';
                $crudRoutePart = 'product-versions';

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
            $table->editColumn('height', function ($row) {
                return $row->height ? $row->height : '';
            });
            $table->editColumn('length', function ($row) {
                return $row->length ? $row->length : '';
            });
            $table->editColumn('width', function ($row) {
                return $row->width ? $row->width : '';
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

        return view('admin.productVersions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_version_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.productVersions.create', compact('companies'));
    }

    public function store(StoreProductVersionRequest $request)
    {
        $productVersion = ProductVersion::create($request->all());

        foreach ($request->input('photo', []) as $file) {
            $productVersion->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $productVersion->id]);
        }

        return redirect()->route('admin.product-versions.index');
    }

    public function edit(ProductVersion $productVersion)
    {
        abort_if(Gate::denies('product_version_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productVersions.edit', compact('productVersion'));
    }

    public function update(UpdateProductVersionRequest $request, ProductVersion $productVersion)
    {
        $productVersion->update($request->all());

        if (count($productVersion->photo) > 0) {
            foreach ($productVersion->photo as $media) {
                if (!in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $productVersion->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $productVersion->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.product-versions.index');
    }

    public function show(ProductVersion $productVersion)
    {
        abort_if(Gate::denies('product_version_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productVersions.show', compact('productVersion'));
    }

    public function destroy(ProductVersion $productVersion)
    {
        abort_if(Gate::denies('product_version_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productVersion->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductVersionRequest $request)
    {
        ProductVersion::tenanted()->whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_version_create') && Gate::denies('product_version_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ProductVersion();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
