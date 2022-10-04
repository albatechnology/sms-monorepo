<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Imports\NewLeadsImport;
use App\Models\Address;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\SubLeadCategory;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class LeadsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Lead::tenanted()->with(['sales', 'customer', 'channel'])->select(sprintf('%s.*', (new Lead)->table));
            if(isset($request->company_id) && $request->company_id != ''){
                $query = $query->whereCompanyId($request->company_id);
            }
            if(isset($request->supervisor_id) && $request->supervisor_id != ''){
                $query = $query->whereHas('user', fn($q) => $q->where('supervisor_id', $request->supervisor_id));
            }
            if(isset($request->lead_category_id) && $request->lead_category_id != ''){
                $query = $query->where('lead_category_id', $request->lead_category_id);
            }
            if(isset($request->sub_lead_category_id) && $request->sub_lead_category_id != ''){
                $query = $query->where('sub_lead_category_id', $request->sub_lead_category_id);
            }
            if(isset($request->start_date) && $request->start_date != ''){
                $query = $query->whereDate('created_at', '>=',date('Y-m-d', strtotime($request->start_date)));
            }
            if(isset($request->end_date) && $request->end_date != ''){
                $query = $query->whereDate('created_at', '<=',date('Y-m-d', strtotime($request->end_date)));
            }
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'lead_show';
                $editGate      = 'lead_edit';
                $deleteGate    = 'lead_delete';
                $crudRoutePart = 'leads';

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
            $table->addColumn('sales', function ($row) {
                return $row->user ? $row->user->name . ' - ' . $row->user->type->description : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type->description ?? '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status->description ?? '';
            });
            $table->editColumn('is_new_customer', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_new_customer ? 'checked' : null) . '>';
            });
            $table->editColumn('label', function ($row) {
                return $row->label ? $row->label : "";
            });
            $table->addColumn('customer_first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });

            $table->addColumn('channel_name', function ($row) {
                return $row->channel ? $row->channel->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'is_new_customer', 'customer', 'channel', 'sales']);

            return $table->make(true);
        }

        $customers = Customer::get();
        $channels  = Channel::get();
        $sales = User::where('type', UserType::SALES)->get();
        $companies = Company::tenanted()->pluck('name','id');
        $supervisors = User::where('type', UserType::SUPERVISOR)->where('supervisor_type_id', 2)->pluck('name','id');
        $leadCategories = LeadCategory::pluck('name','id');
        return view('admin.leads.index', compact('customers', 'channels', 'sales', 'companies', 'supervisors','leadCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('lead_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $leadCategories = LeadCategory::all()->pluck('name', 'id');

        return view('admin.leads.create', compact('leadCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            //lead
            'type' => 'required',
            'label' => 'string|nullable',
            'lead_category_id' => 'required|integer',
            'sub_lead_category_id' => 'nullable',
            'is_new_customer' => 'nullable',
            'interest' => 'nullable',
            'customer_id' => 'required_if:is_new_customer,null',
            //customer
            'title' => 'required_if:is_new_customer,1',
            'first_name' => 'required_if:is_new_customer,1',
            'email' => 'nullable|unique:customers,email',
            'phone' => 'nullable|unique:customers,phone',
            //address
            'address_line_1' => 'required_if:is_new_customer,1',
            'address_type' => 'required_if:is_new_customer,1',
        ]);

        DB::transaction(function () use ($request) {
            $is_new_customer = 0;
            $customer_id = $request->customer_id;
            if (isset($request->is_new_customer) && $request->is_new_customer == 1) {
                $customer = Customer::create([
                    'title' => $request->title ?? 1,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name ?? null,
                    'date_of_birth' => $request->date_of_birth ? date('Y-m-d', strtotime($request->date_of_birth)) : null,
                    'email' => $request->email ?? null,
                    'phone' => $request->phone ?? null,
                    'description' => $request->description ?? null,
                ]);
                $is_new_customer = 1;
                $customer_id = $customer->id;

                $address = Address::create([
                    'address_line_1' => $request->address_line_1 ?? null,
                    'address_line_2' => $request->address_line_2 ?? null,
                    'address_line_3' => $request->address_line_3 ?? null,
                    'country' => $request->country ?? null,
                    'province' => $request->province ?? null,
                    'city' => $request->city ?? null,
                    'type' => $request->address_type ?? 1,
                    'phone' => $request->address_phone ?? null,
                    'customer_id' => $customer_id,
                ]);

                $customer->default_address_id = $address->id;
            }

            Lead::create([
                'type' => $request->type,
                'status' => \App\Enums\LeadStatus::GREEN,
                'label' => $request->label,
                'interest' => $request->interest,
                'is_unhandled' => 1,
                'channel_id' => auth()->user()->getDefaultChannel(),
                'lead_category_id' => $request->lead_category_id,
                'sub_lead_category_id' => $request->sub_lead_category_id ?? null,
                'is_new_customer' => $is_new_customer,
                'customer_id' => $customer_id,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.leads.index')->with('message', 'Lead Created Successfully');
    }

    public function edit(Lead $lead)
    {
        abort_if(Gate::denies('lead_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::where('type', UserType::SALES)->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $channels = Channel::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $lead->load('customer', 'channel');

        return view('admin.leads.edit', compact('channels', 'lead', 'users'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $request['user_id'] = $request->sales;
        $lead->update($request->validated());

        return redirect()->route('admin.leads.index');
    }

    public function show(Lead $lead)
    {
        abort_if(Gate::denies('lead_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead->load('customer', 'channel', 'leadActivities');

        return view('admin.leads.show', compact('lead'));
    }

    public function destroy(Lead $lead)
    {
        abort_if(Gate::denies('lead_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead->delete();

        return back();
    }

    public function massDestroy(MassDestroyLeadRequest $request)
    {
        Lead::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getSubLeadCategories($leadCategoryId)
    {
        $subLeadCategories = SubLeadCategory::where('lead_category_id', $leadCategoryId)->pluck('name', 'id')->all();
        $html = '<option value="">- Sub Category is Empty -</option>';
        if ($subLeadCategories) {
            $html = '<option value="">- Select Sub Category -</option>';
            foreach ($subLeadCategories as $id => $name) {
                $html .= '<option value="' . $id . '">' . $name . '</option>';
            }
        }
        return $html;
    }

    public function import()
    {
        $import = new NewLeadsImport;
        Excel::import($import, request()->file('csv_file'));
        // $this->exportFailedImport($import->getDataFailed());
        return redirect('admin/leads')->with('message', $import->getRowCount() . ' Data Berhasil di Import. ' . $import->getFailedCount() . ' Data Gagal di Import');
    }

    public function exportFailedImport(array $data)
    {
        return Excel::download(new \App\Exports\LeadFailedExport($data), 'lead_failed_import.csv')->withHeadings();
    }
}
