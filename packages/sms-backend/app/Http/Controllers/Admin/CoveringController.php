<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCoveringRequest;
use App\Http\Requests\StoreCoveringRequest;
use App\Http\Requests\UpdateCoveringRequest;
use App\Models\Company;
use App\Models\Covering;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CoveringController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('covering_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Covering::tenanted()->with(['product'])->select(sprintf('%s.*', (new Covering())->getTable()));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'covering_show';
                $editGate      = 'covering_edit';
                $deleteGate    = 'covering_delete';
                $crudRoutePart = 'coverings';

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
            $table->editColumn('type', function ($row) {
                return $row->type ? $row->type : '';
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

        return view('admin.coverings.index');
    }

    public function create()
    {
        abort_if(Gate::denies('covering_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.coverings.create', compact('companies'));
    }

    public function store(StoreCoveringRequest $request)
    {
        $covering = Covering::create($request->all());

        foreach ($request->input('photo', []) as $file) {
            $covering->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $covering->id]);
        }

        return redirect()->route('admin.coverings.index');
    }

    public function edit(Covering $covering)
    {
        abort_if(Gate::denies('covering_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.coverings.edit', compact('covering'));
    }

    public function update(UpdateCoveringRequest $request, Covering $covering)
    {
        $covering->update($request->all());

        if (count($covering->photo) > 0) {
            foreach ($covering->photo as $media) {
                if (!in_array($media->file_name, $request->input('photo', []))) {
                    $media->delete();
                }
            }
        }
        $media = $covering->photo->pluck('file_name')->toArray();
        foreach ($request->input('photo', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $covering->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('photo');
            }
        }

        return redirect()->route('admin.coverings.index');
    }

    public function show(Covering $covering)
    {
        abort_if(Gate::denies('covering_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.coverings.show', compact('covering'));
    }

    public function destroy(Covering $covering)
    {
        abort_if(Gate::denies('covering_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $covering->delete();

        return back();
    }

    public function massDestroy(MassDestroyCoveringRequest $request)
    {
        Covering::tenanted()->whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('covering_create') && Gate::denies('covering_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Covering();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
