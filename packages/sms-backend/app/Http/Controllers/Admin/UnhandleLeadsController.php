<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyLeadRequest;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Enums\NotificationType;
use App\Classes\ExpoMessage;
use App\Models\LeadCategory;
use App\Services\PushNotificationService;

class UnhandleLeadsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('unhandle_lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            if (auth()->user()->type->in([UserType::DEFAULT, UserType::DIRECTOR])) {
                $query = Lead::unhandled()->with(['sales', 'customer', 'channel'])->select(sprintf('%s.*', (new Lead)->table));
            } else {
                $query = Lead::tenanted()->unhandled()->with(['sales', 'customer', 'channel'])->select(sprintf('%s.*', (new Lead)->table));
            }

            // if(isset($request->company_id) && $request->company_id != ''){
            //     $query = $query->whereCompanyId($request->company_id);
            // }
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
                $html = '';
                $html .= '<a class="btn btn-xs btn-info" href="' . route('admin.unhandle-leads.edit', $row->id) . '">Assign To</a>';
                $html .= '<form action="' . route('admin.unhandle-leads.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\' ' . trans('global.delete') . ' \');" style="display: inline-block;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <input type="submit" class="btn btn-xs btn-danger" value="' . trans('global.delete') . '"></form>';
                return $html;
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('user_id', function ($row) {
                return $row->user ? $row->user->name . ' - ' . $row->user->type->description : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type->description ?? '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status->description ?? '';
            });
            $table->editColumn('label', function ($row) {
                return $row->label ? $row->label : "";
            });
            $table->addColumn('customer_first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });

            $table->editColumn('channel_id', function ($row) {
                return $row->channel ? $row->channel->name : '';
            });

            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'is_new_customer', 'customer', 'channel', 'sales']);

            return $table->make(true);
        }

        $customers = Customer::get();
        $channels  = Channel::pluck('name', 'id')->all();
        $supervisors = User::where('type', UserType::SUPERVISOR)->pluck('name', 'id')->all();

        // $companies = Company::tenanted()->pluck('name','id');
        // $supervisors = User::where('type', UserType::SUPERVISOR)->where('supervisor_type_id', 2)->pluck('name','id');
        $leadCategories = LeadCategory::pluck('name','id');

        return view('admin.unhandleLeads.index', compact('customers', 'channels', 'supervisors','leadCategories'));
    }

    public function getUsers($channelId, $userType)
    {
        $users = User::whereChannelId($channelId);
        switch ($userType) {
            case '2':
                $users->where('type', UserType::SUPERVISOR)->where('supervisor_type_id', 1);
                break;
            case '3':
                $users->where('type', UserType::SALES);
                break;
            default:
                $users->where('type', UserType::SUPERVISOR)->where('supervisor_type_id', 2);
                break;
        }
        $users = $users->pluck('name', 'id')->all();
        $html = '<option value="">- User is empty -</option>';
        if ($users) {
            $html = '<option value="">- Select User -</option>';
            foreach ($users as $id => $name) {
                $html .= '<option value="' . $id . '">' . $name . '</option>';
            }
        }
        return $html;
    }

    public function edit($id)
    {
        abort_if(Gate::denies('unhandle_lead_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $lead = Lead::findOrFail($id);
        return view('admin.unhandleLeads.edit', compact('lead'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_type' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        $channel_id = User::findOrFail($request->user_id)->getDefaultChannel();
        if (!$channel_id || $channel_id == null) {
            return redirect()->back()->with('message', 'Sales/Supervisor tidak mempunyai default channel');
        }

        $lead = Lead::findOrFail($id);
        if ($request->user_type == 3) {
            $lead->is_unhandled = 0;
        }
        $lead->user_id = $request->user_id;
        $lead->channel_id = $channel_id;
        $lead->save();

        $user = User::find($request->user_id);

        // notify user (new lead assigned)
        if (isset($user->notificationDevices) && count($user->notificationDevices) > 0) {
            $type = NotificationType::NewLeadAssigned();
            $link = config("notification-link.{$type->key}") ?? 'no-link';

            $message = ExpoMessage::create()
                ->addRecipients($user)
                ->setBadgeFor($user)
                ->title("New Assigned Lead")
                ->body($lead->label . ' has been assigned to your lead list.')
                ->code($type->key)
                ->link($link);

            app(PushNotificationService::class)->notify($message);
        }

        return redirect()->route('admin.unhandle-leads.index')->with('message', 'Lead assigned successfully');
    }

    public function destroy(Lead $lead)
    {
        abort_if(Gate::denies('unhandle_lead_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead->delete();

        return back();
    }

    public function massDestroy(MassDestroyLeadRequest $request)
    {
        Lead::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getCustomers()
    {
        return Customer::select("id", "first_name as name")->has('customerAddresses')->whereSearch($_GET['q'])->get();
    }
}
