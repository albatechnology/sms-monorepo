<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyInteriorDesignRequest;
use App\Http\Requests\StoreInteriorDesignRequest;
use App\Http\Requests\UpdateInteriorDesignRequest;
use App\Enums\UserType;
use App\Models\InteriorDesign;
use App\Models\User;
use App\Models\SupervisorType;
use App\Models\Company;
use App\Models\Religion;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Traits\CsvImportTrait;
use Gate;

class InteriorDesignController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('interior_design_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax() || false) {
            $query = InteriorDesign::with(['bum', 'sales'])->get();

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('placeholder', '&nbsp;')
                ->addColumn('bum', function ($row) {
                    return $row->bum->name ?? null;
                })
                ->addColumn('sales', function ($row) {
                    return $row->sales->name ?? null;
                })
                ->addColumn('actions', function ($row) {
                    $viewGate      = null;
                    $editGate      = 'interior_design_edit';
                    $deleteGate    = 'interior_design_delete';
                    $crudRoutePart = 'interior-designs';

                    return view('partials.datatablesActions', compact(
                        'viewGate',
                        'editGate',
                        'deleteGate',
                        'crudRoutePart',
                        'row'
                    ));
                })
                ->rawColumns(['placeholder', 'actions'])
                ->make(true);
        }

        return view('admin.interiorDesigns.index');
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('interior_design_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->prepare();

        return view('admin.interiorDesigns.create', [
            'bums' => $this->bums,
            'saleses' => $this->saleses,
            'companies' => $this->companies,
            'religions' => $this->religions,
        ]);
    }

    public function store(StoreInteriorDesignRequest $request)
    {
        InteriorDesign::create($request->validated());

        return redirect()->route('admin.interior-designs.index');
    }

    public function show()
    {
        //
    }

    public function edit(InteriorDesign $interiorDesign)
    {
        abort_if(Gate::denies('interior_design_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->prepare();

        return view('admin.interiorDesigns.edit', [
            'interiorDesign' => $interiorDesign,
            'bums' => $this->bums,
            'saleses' => $this->saleses,
            'companies' => $this->companies,
            'religions' => $this->religions,
        ]);
    }

    public function update(UpdateInteriorDesignRequest $request, InteriorDesign $interiorDesign)
    {
        $interiorDesign->update($request->validated());

        return redirect()->route('admin.interior-designs.index');
    }

    public function destroy(InteriorDesign $interiorDesign)
    {
        abort_if(Gate::denies('interior_design_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $interiorDesign->delete();
        } catch (BaseException $e) {
            return back()->withErrors($e->getMessageBag());
        }

        return redirect()->route('admin.interior-designs.index');
    }

    public function massDestroy(MassDestroyInteriorDesignRequest $request)
    {
        InteriorDesign::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    private function prepare()
    {
        $this->bums = User::where('type', UserType::SUPERVISOR)->where('supervisor_type_id', SupervisorType::where('code', 'manager-area')->first()->id)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $this->saleses = User::where('type', UserType::SALES)->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $this->companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $this->religions = Religion::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
    }

    public function getInteriorDesigns($sales_id)
    {
        return InteriorDesign::where('sales_id', $sales_id)->orderBy('id', 'desc')->pluck('name', 'id');
    }
}
