<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPromoRequest;
use App\Http\Requests\StorePromoCategoryRequest;
use App\Http\Requests\UpdatePromoCategoryRequest;
use App\Models\Company;
use App\Models\PromoCategory;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PromoCategoryController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('promo_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PromoCategory::with(['company'])->select(sprintf('%s.*', (new PromoCategory)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'promo_category_show';
                $editGate      = 'promo_category_edit';
                $deleteGate    = 'promo_category_delete';
                $crudRoutePart = 'promo-categories';

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

            $table->addColumn('company_name', function ($row) {
                return $row->company ? $row->company->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'image', 'company']);

            return $table->make(true);
        }

        $companies = Company::get();

        return view('admin.promoCategories.index', compact('companies'));
    }

    public function create()
    {
        abort_if(Gate::denies('promo_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.promoCategories.create', compact('companies'));
    }

    public function store(StorePromoCategoryRequest $request)
    {
        $promoCategory = PromoCategory::create($request->validated());

        foreach ($request->input('image', []) as $file) {
            $promoCategory->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $promoCategory->id]);
        }

        return redirect()->route('admin.promo-categories.index');
    }

    public function edit(PromoCategory $promoCategory)
    {
        abort_if(Gate::denies('promo_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $promoCategory->load('company');

        return view('admin.promoCategories.edit', compact('companies', 'promoCategory'));
    }

    public function update(UpdatePromoCategoryRequest $request, PromoCategory $promoCategory)
    {
        $promoCategory->update($request->validated());

        if (count($promoCategory->image) > 0) {
            foreach ($promoCategory->image as $media) {
                if (!in_array($media->file_name, $request->input('image', []))) {
                    $media->delete();
                }
            }
        }

        $media = $promoCategory->image->pluck('file_name')->toArray();

        foreach ($request->input('image', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $promoCategory->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('image');
            }
        }

        return redirect()->route('admin.promo-categories.index');
    }

    public function show(PromoCategory $promoCategory)
    {
        abort_if(Gate::denies('promo_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promoCategory->load('company');

        return view('admin.promoCategories.show', compact('promoCategory'));
    }

    public function destroy(PromoCategory $promoCategory)
    {
        abort_if(Gate::denies('promo_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $promoCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyPromoRequest $request)
    {
        PromoCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('promo_category_create') && Gate::denies('promo_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new PromoCategory();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
