<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCatalogueRequest;
use App\Http\Requests\StoreCatalogueRequest;
use App\Http\Requests\UpdateCatalogueRequest;
use App\Models\Catalogue;
use App\Models\Channel;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CatalogueController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('catalogue_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Catalogue::with(['channel'])->select(sprintf('%s.*', (new Catalogue)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'catalogue_show';
                $editGate      = 'catalogue_edit';
                $deleteGate    = 'catalogue_delete';
                $crudRoutePart = 'catalogues';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('catalogue', function ($row) {
                if (!$row->catalogue) {
                    return '';
                }

                $links = [];

                foreach ($row->catalogue as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });
            $table->addColumn('channel_name', function ($row) {
                return $row->channel ? $row->channel->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'catalogue', 'channel']);

            return $table->make(true);
        }

        $channels = Channel::get();

        return view('admin.catalogues.index', compact('channels'));
    }

    public function create()
    {
        abort_if(Gate::denies('catalogue_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channels = Channel::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.catalogues.create', compact('channels'));
    }

    public function store(StoreCatalogueRequest $request)
    {
        $catalogue = Catalogue::create($request->validated());

        foreach ($request->input('catalogue', []) as $file) {
            $catalogue->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('catalogue');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $catalogue->id]);
        }

        return redirect()->route('admin.catalogues.index');
    }

    public function edit(Catalogue $catalogue)
    {
        abort_if(Gate::denies('catalogue_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channels = Channel::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $catalogue->load('channel');

        return view('admin.catalogues.edit', compact('channels', 'catalogue'));
    }

    public function update(UpdateCatalogueRequest $request, Catalogue $catalogue)
    {
        $catalogue->update($request->validated());

        if (count($catalogue->catalogue) > 0) {
            foreach ($catalogue->catalogue as $media) {
                if (!in_array($media->file_name, $request->input('catalogue', []))) {
                    $media->delete();
                }
            }
        }

        $media = $catalogue->catalogue->pluck('file_name')->toArray();

        foreach ($request->input('catalogue', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $catalogue->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('catalogue');
            }
        }

        return redirect()->route('admin.catalogues.index');
    }

    public function show(Catalogue $catalogue)
    {
        abort_if(Gate::denies('catalogue_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $catalogue->load('channel');

        return view('admin.catalogues.show', compact('catalogue'));
    }

    public function destroy(Catalogue $catalogue)
    {
        abort_if(Gate::denies('catalogue_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $catalogue->delete();

        return back();
    }

    public function massDestroy(MassDestroyCatalogueRequest $request)
    {
        Catalogue::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('catalogue_create') && Gate::denies('catalogue_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Catalogue();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
