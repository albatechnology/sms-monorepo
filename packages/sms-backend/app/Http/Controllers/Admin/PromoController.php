<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPromoRequest;
use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Models\Promo;
// use App\Models\PromoCategory;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('promo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Promo::tenanted()->select(sprintf('%s.*', (new Promo)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'promo_show';
                $editGate      = 'promo_edit';
                $deleteGate    = 'promo_delete';
                $crudRoutePart = 'promos';

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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('image', function ($row) {
                if (!$row->image) {
                    return '';
                }

                $links = [];

                foreach ($row->image as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank"><img src="' . $media->getUrl('thumb') . '" width="50px" height="50px"></a>';
                }

                return implode(' ', $links);
            });

            // $table->addColumn('company_name', function ($row) {
            //     return $row->company ? $row->company->name : '';
            // });

            // $table->addColumn('promo_category_name', function ($row) {
            //     return $row->promoCategory ? $row->promoCategory->name : "";
            // });

            $table->rawColumns(['actions', 'placeholder', 'image']);

            return $table->make(true);
        }

        // $companies = Company::get();
        return view('admin.promos.index');
    }

    public function create()
    {
        abort_if(Gate::denies('promo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $promoCategories = PromoCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.promos.create');
    }

    public function store(StorePromoRequest $request)
    {
        $promo = Promo::create($request->validated());

        foreach ($request->input('image', []) as $file) {
            $promo->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $promo->id]);
        }

        return redirect()->route('admin.promos.index');
    }

    public function edit(Promo $promo)
    {
        abort_if(Gate::denies('promo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $promoCategories = PromoCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.promos.edit', compact('promo'));
    }

    public function update(UpdatePromoRequest $request, Promo $promo)
    {
        $promo->update($request->validated());

        if (count($promo->image) > 0) {
            foreach ($promo->image as $media) {
                if (!in_array($media->file_name, $request->input('image', []))) {
                    $media->delete();
                }
            }
        }

        $media = $promo->image->pluck('file_name')->toArray();

        foreach ($request->input('image', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $promo->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('image');
            }
        }

        return redirect()->route('admin.promos.index');
    }

    public function show(Promo $promo)
    {
        abort_if(Gate::denies('promo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $promo->load('company');

        return view('admin.promos.show', compact('promo'));
    }

    public function destroy(Promo $promo)
    {
        abort_if(Gate::denies('promo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promo->delete();

        return back();
    }

    public function massDestroy(MassDestroyPromoRequest $request)
    {
        Promo::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('promo_create') && Gate::denies('promo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Promo();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
