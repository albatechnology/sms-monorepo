<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Address;
use App\Models\Customer;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Customer::tenanted()->with('subscribtionUser')->select(sprintf('%s.*', (new Customer)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'customer_show';
                $editGate      = 'customer_edit';
                $deleteGate    = 'customer_delete';
                $crudRoutePart = 'customers';

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
            $table->addColumn('subscriber', function ($row) {
                return $row->subscribtionUser?->name ?? '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title?->key ?? '';
            });
            $table->editColumn('first_name', function ($row) {
                return $row->first_name ? $row->first_name : "";
            });
            $table->editColumn('last_name', function ($row) {
                return $row->last_name ? $row->last_name : "";
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : "";
            });
            $table->editColumn('phone', function ($row) {
                return $row->phone ? $row->phone : "";
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.customers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('customer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());
        if (!$customer) return redirect()->back()->withInput()->withStatus('Customer failed to create!');

        $address = Address::create([
            'customer_id' => $customer->id,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'address_line_3' => $request->address_line_3,
            'city' => $request->city,
            'country' => $request->country,
            'province' => $request->province,
            'type' => $request->type,
            'phone' => $request->address_phone,
        ]);
        if (!$address) {
            $customer->delete();
            return redirect()->back()->withInput()->withStatus('Address failed to create!');
        }
        $customer->update(['default_address_id' => $address->id]);

        return redirect()->route('admin.customers.index')->withStatus('Customer created successfully');
    }

    public function edit(Customer $customer)
    {
        abort_if(Gate::denies('customer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $addresses = Address::where('customer_id', $customer->id)->get()->pluck('address_line_1', 'id');

        return view('admin.customers.edit', compact('customer', 'addresses'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        if (count($customer->customerAddresses) < 1 && !isset($request->address_line_1)) return redirect()->back()->withInput()->withStatus('Address required!');
        if (count($customer->customerAddresses) < 1 && isset($request->address_line_1)) {
            $address = Address::create([
                'customer_id' => $customer->id,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'address_line_3' => $request->address_line_3,
                'city' => $request->city,
                'country' => $request->country,
                'province' => $request->province,
                'type' => $request->type,
                'phone' => $request->address_phone,
            ]);
            if (!$address) {
                $customer->delete();
                return redirect()->back()->withInput()->withStatus('Address failed to create!');
            }
        }

        return redirect()->route('admin.customers.index')->withStatus('Customer updated successfully');
    }

    public function show(Customer $customer)
    {
        abort_if(Gate::denies('customer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customer->load('customerLeads', 'customerAddresses', 'customerTaxInvoices');

        return view('admin.customers.show', compact('customer'));
    }

    public function destroy(Customer $customer)
    {
        abort_if(Gate::denies('customer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customer->delete();

        return back();
    }

    public function massDestroy(MassDestroyCustomerRequest $request)
    {
        Customer::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function hapus(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'csv_file' => 'required|mimes:csv,txt'
            ]);

            $csv = array_map('str_getcsv', file($request->file('csv_file')));
            $success = 0;
            $failed = 0;
            foreach ($csv as $nohp) {
                $hp = '0' . $nohp[0];
                $customer = Customer::where('phone', $hp)->first();
                if ($customer) {
                    $customer->customerLeads()->delete();
                    $customer->customerActivity()->delete();
                    $customer->customerAddresses()->delete();
                    $customer->defaultCustomerAddress()->delete();
                    $customer->customerTaxInvoices()->delete();
                    $customer->delete();
                    $success++;
                } else {
                    $failed++;
                }
            }
            die('success : ' . $success . ' - Failed : ' . $failed);
        }
        return view('admin.customers.hapus');
    }

    public function getCustomers()
    {
        return Customer::selectRaw("id, CONCAT(first_name, ' ', last_name) as name")->where('first_name', 'LIKE', "%" . $_GET['q'] . "%")->orWhere('last_name', 'LIKE', "%" . $_GET['q'] . "%")->get();
    }

    public function ajaxGetCustomers(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::selectRaw("id, IF(
                customers.last_name IS NOT NULL,
                CONCAT(customers.first_name, ' ', customers.last_name),
                customers.first_name
            ) as name");

            if ($q = $request->q) {
                $customers->whereSearch($q);
            }

            if ($request->has_address == 1) {
                $customers->whereHas('customerAddresses');
            }

            return $customers->get();
        }
    }
}
