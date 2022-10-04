<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyColourRequest;
use App\Http\Requests\StoreColourRequest;
use App\Http\Requests\UpdateColourRequest;
use App\Models\Company;
use App\Models\Colour;
use App\Models\ProductBrand;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ColourController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('colour_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Colour::tenanted()->with(['product'])->select(sprintf('%s.*', (new Colour())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'colour_show';
                $editGate      = 'colour_edit';
                $deleteGate    = 'colour_delete';
                $crudRoutePart = 'colours';

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
            $table->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : '';
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

            $table->rawColumns(['actions', 'placeholder', 'photo', 'product_name']);

            return $table->make(true);
        }

        return view('admin.colours.index');
    }

    public function create()
    {
        abort_if(Gate::denies('colour_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.colours.create', compact('companies'));
    }

    public function store(StoreColourRequest $request)
    {
        $colour = Colour::create($request->all());

        foreach ($request->input('photo', []) as $file) {
            $colour->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $colour->id]);
        }

        return redirect()->route('admin.colours.index');
    }

    public function edit(Colour $colour)
    {
        abort_if(Gate::denies('colour_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $product_brands = ProductBrand::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $colour->load('product_brand');

        return view('admin.colours.edit', compact('colour'));
    }

    public function update(UpdateColourRequest $request, Colour $colour)
    {
        $colour->update($request->all());

        if (count($colour->photo) > 0) {
            foreach ($colour->photo as $media) {
                if (!in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $colour->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $colour->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.colours.index');
    }

    public function show(Colour $colour)
    {
        abort_if(Gate::denies('colour_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $colour->load('product_brand');

        return view('admin.colours.show', compact('colour'));
    }

    public function destroy(Colour $colour)
    {
        abort_if(Gate::denies('colour_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $colour->delete();

        return back();
    }

    public function massDestroy(MassDestroyColourRequest $request)
    {
        Colour::tenanted()->whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('colour_create') && Gate::denies('colour_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Colour();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
