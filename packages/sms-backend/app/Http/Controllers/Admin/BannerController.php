<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBannerRequest;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Models\Banner;
use App\Models\Company;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('banner_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Banner::with(['company'])->select(sprintf('%s.*', (new Banner)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'banner_show';
                $editGate      = 'banner_edit';
                $deleteGate    = 'banner_delete';
                $crudRoutePart = 'banners';

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
            $table->editColumn('bannerable_type', function ($row) {
                return $row->bannerable_type ? $row->bannerable_type : "";
            });
            $table->editColumn('bannerable', function ($row) {
                return $row->bannerable ? $row->bannerable : "";
            });
            $table->editColumn('is_active', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_active ? 'checked' : null) . '>';
            });

            $table->editColumn('end_time', function ($row) {
                return $row->end_time ? $row->end_time : "";
            });
            $table->addColumn('company_name', function ($row) {
                return $row->company ? $row->company->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'is_active', 'company']);

            return $table->make(true);
        }

        $companies = Company::get();

        return view('admin.banners.index', compact('companies'));
    }

    public function create()
    {
        abort_if(Gate::denies('banner_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.banners.create', compact('companies'));
    }

    public function store(StoreBannerRequest $request)
    {
        $banner = Banner::create($request->validated());

        return redirect()->route('admin.banners.index');
    }

    public function edit(Banner $banner)
    {
        abort_if(Gate::denies('banner_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $banner->load('company');

        return view('admin.banners.edit', compact('companies', 'banner'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $banner->update($request->validated());

        return redirect()->route('admin.banners.index');
    }

    public function show(Banner $banner)
    {
        abort_if(Gate::denies('banner_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $banner->load('company');

        return view('admin.banners.show', compact('banner'));
    }

    public function destroy(Banner $banner)
    {
        abort_if(Gate::denies('banner_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $banner->delete();

        return back();
    }

    public function massDestroy(MassDestroyBannerRequest $request)
    {
        Banner::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
