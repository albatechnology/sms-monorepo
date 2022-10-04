<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Role;
use App\Models\SupervisorType;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Lead;
use App\Enums\UserType;
use App\Http\Requests\StoreSmsUserRequest;
use App\Http\Requests\UpdateSmsUserRequest;
use App\Models\PermissionUser;
use App\Models\SmsChannel;
use App\Models\UserCompany;

class UsersController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = User::tenanted()->with(['roles', 'supervisor_type', 'companies', 'channels'])
                ->leftJoin('users AS spv', 'spv.id', '=', 'users.supervisor_id')
                ->select(sprintf('%s.*', (new User)->table));

            if (isset($request->user_app) && $request->user_app != '') {
                if ($request->user_app == 'sms') {
                    // $query = $query->type->in([UserType::SALES_SMS, UserType::SUPERVISOR_SMS]);
                    $query = $query->whereIn('users.type', [UserType::SALES_SMS, UserType::SUPERVISOR_SMS]);
                } else {
                    $query = $query->whereNotIn('users.type', [UserType::SALES_SMS, UserType::SUPERVISOR_SMS]);
                    // $query = $query->type->notIn([UserType::SALES_SMS, UserType::SUPERVISOR_SMS]);
                }
            }

            if (!empty($request->input('columns.9.search.value'))) {
                $query->whereHas('channels', function ($q) use ($request) {
                    $q->where('name', '=', $request->input('columns.9.search.value'));
                });
            }
            if (!empty($request->input('columns.8.search.value'))) {
                $query->where('spv.name', 'REGEXP', $request->input('columns.8.search.value'));
            }
            $table = Datatables::eloquent($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'user_show';
                $editGate      = 'user_edit';
                $deleteGate    = 'user_delete';
                $crudRoutePart = 'users';

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
            $table->editColumn('orlan_user_id', function ($row) {
                return $row->orlan_user_id ? $row->orlan_user_id : "";
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : "";
            });

            $table->editColumn('roles', function ($row) {
                $labels = [];

                foreach ($row->roles as $role) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $role->title);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('type', function ($row) {
                return $row->type?->key ?? '';
            });
            $table->addColumn('supervisor_type_name', function ($row) {
                return $row->supervisor_type ? $row->supervisor_type->name : '';
            });

            $table->addColumn('supervisor_name', function ($row) {
                return $row->supervisor ? $row->supervisor->name : '';
            });

            $table->editColumn('companies', function ($row) {
                $labels = [];

                foreach ($row->companies as $company) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $company->name);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('channels', function ($row) {
                $labels = [];

                foreach ($row->channels as $channel) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $channel->name);
                }

                return implode(' ', $labels);
            });

            $table->editColumn('channels', function ($row) {
                $labels = [];

                foreach ($row->channels as $channel) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $channel->name);
                }

                return implode(' ', $labels);
            });

            $table->filterColumn('channels', function ($query) {
            });
            $table->filterColumn('supervisor.name', function ($query) {
            });

            $table->rawColumns(['actions', 'placeholder', 'roles', 'supervisor_type', 'supervisor', 'companies', 'channels']);

            return $table->make(true);
        }

        $roles            = Role::get();
        $supervisor_types = SupervisorType::get();
        $users            = User::get();
        $companies        = Company::get();
        $channels         = Channel::get();

        return view('admin.users.index', compact('roles', 'supervisor_types', 'users', 'companies', 'channels'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all()->pluck('title', 'id');

        $supervisor_types = SupervisorType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $supervisors = User::tenanted()->whereIsSupervisor()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $companies = Company::tenanted()->get()->pluck('name', 'id');
        return view('admin.users.create', compact('roles', 'supervisor_types', 'supervisors', 'companies'));
    }

    public function createSms(Request $request)
    {
        abort_if(Gate::denies('user_create_sms'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervisors = User::where('type', UserType::SUPERVISOR_SMS)->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $channels = SmsChannel::whereNull('user_id')->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $types = UserType::getSMSUserType();
        return view('admin.users.create-sms', compact('channels', 'supervisors', 'types'));
    }

    public function storeSms(StoreSmsUserRequest $request)
    {
        $data = $request->validated();
        if ($request->type == UserType::SALES_SMS) {
            $channel_id = User::findOrFail($request->supervisor_id)->channel_id;
            $data = array_merge($data, ['channel_id' => $channel_id]);
        }
        $user = User::create($data);
        if ($request->type == UserType::SUPERVISOR_SMS) SmsChannel::findOrFail($request->channel_id)->update(['user_id' => $user->id]);
        return redirect()->route('admin.users.index');
    }

    public function store(StoreUserRequest $request)
    {
        /** @var User $user */
        $user = User::create($request->validated());
        if (isset($request->company_ids) && count($request->company_ids) > 0) {
            $user->userCompanies()->delete();
            $user->update(['company_id' => $request->company_ids[0]]);
            foreach ($request->company_ids as $cid) {
                UserCompany::create(['user_id' => $user->id, 'company_id' => $cid]);
            }
        } else {
            $user->update(['company_ids' => [$user->company_id]]);
            UserCompany::create(['user_id' => $user->id, 'company_id' => $user->company_id]);
        }

        $user->roles()->sync($request->input('roles', []));

        $userId = $user->id;
        $user->roles->each(function ($role) use ($userId) {
            $role->permissions->each(function ($permission) use ($userId) {
                PermissionUser::insert([
                    'user_id' => $userId,
                    'permission_id' => $permission->id,
                ]);
            });
        });
        //$user->companies()->sync($request->input('companies', []));
        if (isset($request->channels) && count($request->channels) > 0) $user->channels()->sync($request->input('channels', []));

        return redirect()->route('admin.users.index');
    }

    public function editSms($id)
    {
        $user = User::findOrFail($id);
        abort_if(Gate::denies('user_edit_sms'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (!$user->type->in([UserType::SALES_SMS, UserType::SUPERVISOR_SMS])) return redirect()->route('admin.users.edit', $user->id);
        $supervisors = User::where('type', UserType::SUPERVISOR_SMS)->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $channels = SmsChannel::whereNull('user_id')->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $types = UserType::getSMSUserType();
        return view('admin.users.edit-sms', compact('supervisors', 'channels', 'types', 'user'));
    }

    public function updateSms(UpdateSmsUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->type == UserType::SALES_SMS) {
            $channel_id = User::findOrFail($request->supervisor_id)->channel_id;
            $user->channel_id = $channel_id;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->type = $request->type;
        $user->supervisor_id = $request->supervisor_id;
        if (isset($request->password) && $request->password != '') $user->password = $request->password;
        $user->save();
        if ($request->type == UserType::SUPERVISOR_SMS) SmsChannel::findOrFail($request->channel_id)->update(['user_id' => $user->id]);
        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($user->type->in([UserType::SALES_SMS, UserType::SUPERVISOR_SMS])) return redirect()->route('admin.users.editSms', $user->id);
        $roles = Role::all()->pluck('title', 'id');

        $supervisor_types = SupervisorType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $supervisors = User::tenanted()->whereIsSupervisor()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $companies = Company::tenanted()->get()->pluck('name', 'id');

        $user->load('roles', 'supervisor_type', 'supervisor', 'companies', 'channels');
        $user_channels = $user->channels->pluck('id')->all();
        return view('admin.users.edit', compact('roles', 'supervisor_types', 'supervisors', 'companies', 'user_channels', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        $user->roles()->sync($request->input('roles', []));

        $user->userCompanies()->delete();
        if (isset($request->company_ids) && count($request->company_ids) > 0) {
            $user->update(['company_id' => $request->company_ids[0]]);
            foreach ($request->company_ids as $cid) {
                UserCompany::create(['user_id' => $user->id, 'company_id' => $cid]);
            }
        } else {
            $user->update(['company_ids' => [$user->company_id]]);
            UserCompany::create(['user_id' => $user->id, 'company_id' => $user->company_id]);
        }

        $userId = $user->id;
        PermissionUser::where('user_id', $userId)->delete();
        $user->roles->each(function ($role) use ($userId) {
            $role->permissions->each(function ($permission) use ($userId) {
                PermissionUser::insert([
                    'user_id' => $userId,
                    'permission_id' => $permission->id,
                ]);
            });
        });

        //$user->companies()->sync($request->input('companies', []));
        $user->channels()->sync($request->input('channels', []));
        if (intval($request->type) == UserType::SALES()->value) {
            $user->update(['channel_id' => intval($request->validated()['channels'][0])]);
            Lead::where('user_id', $user->id)->update(['channel_id' => intval($request->validated()['channels'][0])]);
        }

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles', 'supervisor_type', 'supervisor', 'companies', 'channels', 'userActivities', 'userActivityComments', 'userOrders', 'approvedByPayments', 'fulfilledByShipments', 'fulfilledByInvoices', 'supervisorUsers', 'requestedByStockTransfers', 'approvedByStockTransfers', 'userUserAlerts');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getChannels($companyId)
    {
        $channel_ids = isset($_POST['channel_ids']) && count($_POST['channel_ids']) > 0 ? $_POST['channel_ids'] : [];
        $channels = Channel::tenanted()->whereCompanyId($companyId)->pluck('name', 'id')->all();
        $html = '<option value="">- Channels is empty -</option>';
        if ($channels) {
            $html = '';
            foreach ($channels as $id => $name) {
                $selected = in_array($id, $channel_ids) ? 'selected' : '';
                $html .= '<option value="' . $id . '" ' . $selected . '>' . $name . '</option>';
            }
        }
        return $html;
    }

    public function getUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
}
