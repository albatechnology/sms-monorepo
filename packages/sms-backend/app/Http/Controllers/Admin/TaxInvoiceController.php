<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTaxInvoiceRequest;
use App\Http\Requests\StoreTaxInvoiceRequest;
use App\Http\Requests\UpdateTaxInvoiceRequest;
use App\Models\Address;
use App\Models\Customer;
use App\Models\TaxInvoice;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class TaxInvoiceController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('tax_invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = TaxInvoice::with(['customer', 'address'])->select(sprintf('%s.*', (new TaxInvoice)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'tax_invoice_show';
                $editGate      = 'tax_invoice_edit';
                $deleteGate    = 'tax_invoice_delete';
                $crudRoutePart = 'tax-invoices';

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
            $table->addColumn('customer_first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });

            $table->editColumn('customer.last_name', function ($row) {
                return $row->customer ? (is_string($row->customer) ? $row->customer : $row->customer->last_name) : '';
            });
            $table->editColumn('company_name', function ($row) {
                return $row->company_name ? $row->company_name : "";
            });
            $table->editColumn('npwp', function ($row) {
                return $row->npwp ? $row->npwp : "";
            });
            $table->addColumn('address_address_line_1', function ($row) {
                return $row->address ? $row->address->address_line_1 : '';
            });

            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : "";
            });

            $table->rawColumns(['actions', 'placeholder', 'customer', 'address']);

            return $table->make(true);
        }

        $customers = Customer::get();
        $addresses = Address::get();

        return view('admin.taxInvoices.index', compact('customers', 'addresses'));
    }

    public function create()
    {
        abort_if(Gate::denies('tax_invoice_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customers = Customer::all()->pluck('first_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $addresses = Address::all()->pluck('address_line_1', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.taxInvoices.create', compact('customers', 'addresses'));
    }

    public function store(StoreTaxInvoiceRequest $request)
    {
        $taxInvoice = TaxInvoice::create($request->validated());

        return redirect()->route('admin.tax-invoices.index');
    }

    public function edit(TaxInvoice $taxInvoice)
    {
        abort_if(Gate::denies('tax_invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customers = Customer::all()->pluck('first_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $addresses = Address::all()->pluck('address_line_1', 'id')->prepend(trans('global.pleaseSelect'), '');

        $taxInvoice->load('customer', 'address');

        return view('admin.taxInvoices.edit', compact('customers', 'addresses', 'taxInvoice'));
    }

    public function update(UpdateTaxInvoiceRequest $request, TaxInvoice $taxInvoice)
    {
        $taxInvoice->update($request->validated());

        return redirect()->route('admin.tax-invoices.index');
    }

    public function show(TaxInvoice $taxInvoice)
    {
        abort_if(Gate::denies('tax_invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $taxInvoice->load('customer', 'address', 'taxInvoiceOrders');

        return view('admin.taxInvoices.show', compact('taxInvoice'));
    }

    public function destroy(TaxInvoice $taxInvoice)
    {
        abort_if(Gate::denies('tax_invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $taxInvoice->delete();

        return back();
    }

    public function massDestroy(MassDestroyTaxInvoiceRequest $request)
    {
        TaxInvoice::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
