<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AddressType;
use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyAddressRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use App\Models\Customer;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AddressController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('address_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Address::with(['customer'])->select(sprintf('%s.*', (new Address)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'address_show';
                $editGate      = 'address_edit';
                $deleteGate    = 'address_delete';
                $crudRoutePart = 'addresses';

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
            $table->editColumn('address_line_1', function ($row) {
                return $row->address_line_1 ? $row->address_line_1 : "";
            });
            $table->editColumn('address_line_2', function ($row) {
                return $row->address_line_2 ? $row->address_line_2 : "";
            });
            $table->editColumn('address_line_3', function ($row) {
                return $row->address_line_3 ? $row->address_line_3 : "";
            });
            $table->editColumn('city', function ($row) {
                return $row->city ? $row->city : "";
            });
            $table->editColumn('country', function ($row) {
                return $row->country ? $row->country : "";
            });
            $table->editColumn('province', function ($row) {
                return $row->province ? $row->province : "";
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? AddressType::fromValue($row->type)->description : '';
            });
            $table->editColumn('phone', function ($row) {
                return $row->phone ? $row->phone : "";
            });
            $table->addColumn('customer_first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'customer']);

            return $table->make(true);
        }

        $customers = Customer::get();

        return view('admin.addresses.index', compact('customers'));
    }

    public function create()
    {
        abort_if(Gate::denies('address_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.addresses.create');
    }

    public function store(StoreAddressRequest $request)
    {
        Address::create($request->validated());

        return redirect()->route('admin.addresses.index');
    }

    public function edit(Address $address)
    {
        abort_if(Gate::denies('address_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $address->load('customer');

        return view('admin.addresses.edit', compact('address'));
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $address->update($request->validated());

        return redirect()->route('admin.addresses.index');
    }

    public function show(Address $address)
    {
        abort_if(Gate::denies('address_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $address->load('customer', 'addressOrders', 'addressTaxInvoices');

        return view('admin.addresses.show', compact('address'));
    }

    public function destroy(Address $address)
    {
        abort_if(Gate::denies('address_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $address->delete();
        } catch (BaseException $e) {
            return back()->withErrors($e->getMessageBag());
        }

        return back();
    }

    public function massDestroy(MassDestroyAddressRequest $request)
    {
        Address::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
