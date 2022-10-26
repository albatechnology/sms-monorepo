<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyVoucherRequest;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Models\Company;
use App\Models\voucher;
use App\Models\Product;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('voucher_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Voucher::with(['company'])->select(sprintf('%s.*', (new Voucher)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'voucher_show';
                $editGate      = 'voucher_edit';
                $deleteGate    = 'voucher_delete';
                $crudRoutePart = 'vouchers';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });
            $table->editColumn('start_time', function ($row) {
                return date('d-m-Y H:i', strtotime($row->start_time));
            });
            $table->editColumn('end_time', function ($row) {
                return date('d-m-Y H:i', strtotime($row->end_time));
            });
            $table->editColumn('is_active', function ($row) {
                return $row->is_active == 1 ? '<i class="fa fa-check text-green"></i>' : '<i class="fa fa-ban text-danger"></i>';
            });
            $table->editColumn('value', function ($row) {
                return rupiah($row->value);
            });
            $table->addColumn('company', function ($row) {
                return $row->company?->name ?? '-';
            });
            $table->rawColumns(['actions', 'placeholder', 'is_active']);

            return $table->make(true);
        }

        $vouchers = Voucher::tenanted()->with(['company'])->get();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        abort_if(Gate::denies('voucher_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $companies = Company::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.vouchers.create', ['companies' => $companies]);
    }

    public function store(StoreVoucherRequest $request)
    {
        Voucher::create($request->validated());

        return redirect()->route('admin.vouchers.index')->with('message', 'Voucher created successfully');
    }

    public function edit(Voucher $voucher)
    {
        abort_if(Gate::denies('voucher_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.vouchers.edit', compact('companies', 'voucher'));
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher)
    {
        $voucher->update($request->validated());

        return redirect()->route('admin.vouchers.index')->with('message', 'Voucher updated successfully');
    }

    public function show(Voucher $voucher)
    {
        abort_if(Gate::denies('voucher_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $voucher->load('company');

        return view('admin.vouchers.show', compact('voucher'));
    }

    public function destroy(Voucher $voucher)
    {
        abort_if(Gate::denies('voucher_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $voucher->delete();

        return back();
    }

    public function massDestroy(MassDestroyVoucherRequest $request)
    {
        Voucher::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getvouchers($company_id = null)
    {
        $vouchers = Voucher::select('id', 'name', 'description');
        if ($company_id != null && $company_id > 0) $vouchers->where('company_id', $company_id);
        $vouchers = $vouchers->get();

        return response()->json($vouchers);
    }
}
