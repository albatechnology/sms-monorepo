<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Channel;
// use App\Models\Company;
use App\Models\Role;
use App\Models\SupervisorType;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Enums\UserType;
use App\Models\PermissionUser;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = User::tenanted()->with(['roles', 'supervisor_type', 'channels'])
                // ->leftJoin('users AS spv', 'spv.id', '=', 'users.supervisor_id')
                ->select(sprintf('%s.*', (new User)->table));

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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : "";
            });

            $table->editColumn('roles', function ($row) {
                $labels = [];

                foreach ($row->roles as $role) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $role->name);
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

            // $table->editColumn('companies', function ($row) {
            //     $labels = [];

            //     foreach ($row->companies as $company) {
            //         $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $company->name);
            //     }

            //     return implode(', ', $labels);
            // });
            $table->editColumn('channels', function ($row) {
                $labels = [];

                foreach ($row->channels as $channel) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $channel->name);
                }

                return implode(', ', $labels);
            });

            $table->filterColumn('channels', function ($query) {
            });
            $table->filterColumn('supervisor.name', function ($query) {
            });

            $table->rawColumns(['actions', 'placeholder', 'roles', 'supervisor_type', 'supervisor', 'channels']);

            return $table->make(true);
        }

        $roles            = Role::tenanted()->get();
        $supervisor_types = SupervisorType::get();
        $users            = User::tenanted()->get();
        // $companies        = Company::get();
        $channels         = Channel::tenanted()->get();

        return view('admin.users.index', compact('roles', 'supervisor_types', 'users', 'channels'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $supervisor_types = SupervisorType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $supervisors = User::tenanted()->whereIsSupervisor()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $companies = Company::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.users.create', compact('roles', 'supervisor_types', 'supervisors'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        // if (isset($request->company_ids) && count($request->company_ids) > 0) {
        //     $user->update(['company_id' => $request->company_ids[0]]);
        //     $user->companies()->sync($request->input('company_ids', []));
        //     // foreach ($request->company_ids as $cid) {
        //     //     UserCompany::create(['user_id' => $user->id, 'company_id' => $cid]);
        //     // }
        // } else {
        //     $user->update(['company_ids' => [$user->company_id]]);
        //     $user->companies()->sync([$user->company_id]);
        //     // UserCompany::create(['user_id' => $user->id, 'company_id' => $user->company_id]);
        // }

        $user->roles()->sync($request->input('role', []));

        $user->productBrands()->sync($request->input('product_brand_ids', []));

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
        if (isset($request->channel_ids) && count($request->channel_ids) > 0) {
            $user->channels()->sync($request->input('channel_ids', []));
        } elseif (isset($request->channel_id)) {
            $user->channels()->sync([$request->channel_id]);
        }

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::tenanted()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $supervisor_types = SupervisorType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $supervisors = User::tenanted()->whereIsSupervisor()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // $companies = Company::tenanted()->get()->pluck('name', 'id');
        $user->load('roles', 'supervisor_type', 'supervisor', 'channels');
        $user_channels = $user->channels->pluck('id')->all();
        return view('admin.users.edit', compact('roles', 'supervisor_types', 'supervisors', 'user_channels', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // dd($request->all());
        $user->update($request->validated());
        $user->roles()->sync($request->input('role', []));

        // $user->productBrands()->sync($request->input('product_brand_ids', []));

        // $user->userCompanies()->delete();
        // if (isset($request->company_ids) && count($request->company_ids) > 0) {
        //     $user->update(['company_id' => $request->company_ids[0]]);
        //     $user->companies()->sync($request->input('company_ids', []));
        //     // foreach ($request->company_ids as $cid) {
        //     //     UserCompany::create(['user_id' => $user->id, 'company_id' => $cid]);
        //     // }
        // } else {
        //     $user->update(['company_ids' => [$user->company_id]]);
        //     $user->companies()->sync([$user->company_id]);
        //     // UserCompany::create(['user_id' => $user->id, 'company_id' => $user->company_id]);
        // }

        // $userId = $user->id;
        // PermissionUser::where('user_id', $userId)->delete();
        // $user->roles->each(function ($role) use ($userId) {
        //     $role->permissions->each(function ($permission) use ($userId) {
        //         PermissionUser::insert([
        //             'user_id' => $userId,
        //             'permission_id' => $permission->id,
        //         ]);
        //     });
        // });

        if (isset($request->channel_ids) && count($request->channel_ids) > 0) {
            $user->channels()->sync($request->input('channel_ids', []));
        } elseif (isset($request->channel_id)) {
            $user->channels()->sync([$request->channel_id]);
        }

        // skg sales bisa multiple channels
        // if (intval($request->type) == UserType::SALES()->value) {
        //     $user->update(['channel_id' => intval($request->validated()['channel_id'])]);
        //     Lead::where('user_id', $user->id)->update(['channel_id' => intval($request->validated()['channel_id'])]);
        // }

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles', 'supervisor_type', 'supervisor', 'channels', 'userActivities', 'userActivityComments', 'userOrders', 'approvedByPayments', 'fulfilledByShipments', 'fulfilledByInvoices', 'supervisorUsers', 'requestedByStockTransfers', 'approvedByStockTransfers', 'userUserAlerts');

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

    public function ajaxGetUsers(Request $request)
    {
        if ($request->ajax()) {
            $users = User::tenanted()->select('id', 'name');
            // if ($companyId = $request->company_id) {
            //     $users->where(function ($q) use ($companyId) {
            //         $q->where('company_id', $companyId)->orWhereHas('companies', fn ($q) => $q->where('channel_id', $companyId));
            //     });
            // }
            if ($channelId = $request->channel_id) {
                $users->whereHas('channels', fn ($q) => $q->where('channel_id', $channelId));
            }
            if ($type = $request->type) {
                $users->where('type', $type);
            }
            if ($supervisorTypeId = $request->supervisor_type_id) {
                if ($request->is_create_user == 1) {
                    $supervisorTypeId = (int)$supervisorTypeId + 1;
                    $users->where('supervisor_type_id', $supervisorTypeId);
                } else {
                    $users->where('supervisor_type_id', $supervisorTypeId);
                }
            }
            if ($supervisorId = $request->supervisor_id) {
                $users->where('supervisor_id', $supervisorId);
            }
            if ($name = $request->name) {
                $users->where('name', 'like', '%' . $name . '%');
            }
            if ($email = $request->email) {
                $users->where('email', 'like', '%' . $email . '%');
            }

            return $users->get();
        }
    }

    public function getChannels()
    {
        $channel_ids = isset($_POST['channel_ids']) && count($_POST['channel_ids']) > 0 ? $_POST['channel_ids'] : [];
        $channels = Channel::tenanted()->pluck('name', 'id')->all();
        // $channels = Channel::tenanted()->whereCompanyId($companyId)->pluck('name', 'id')->all();
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

    public function includeFormDefault($userId = null)
    {
        $user = $userId ? User::findOrFail($userId) : null;
        $selectedChannels = $user?->channels?->pluck('name', 'id')->all() ?? null;
        $channels = user()->channels?->pluck('name', 'id')->all() ?? null;

        // $companies = Company::tenanted()->get()->pluck('name', 'id');

        return view('admin.users.includes.default', ['user' => $user, 'channels' => $channels, 'selectedChannels' => $selectedChannels]);
    }

    public function includeFormDirector($userId = null)
    {
        $user = $userId ? User::findOrFail($userId) : null;
        // $selectedCompanies = $user?->companies?->pluck('id')->all() ?? null;
        // $companies = Company::tenanted()->get()->pluck('name', 'id');

        return view('admin.users.includes.director', ['user' => $user]);
    }

    public function includeFormSupervisor($userId = null)
    {
        $user = $userId ? User::findOrFail($userId) : null;
        $selectedChannels = $user?->channels?->pluck('id')->all() ?? null;

        // $companies = Company::tenanted()->get()->pluck('name', 'id');
        $supervisor_types = SupervisorType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $maxSupervisorTypeId = DB::table('supervisor_types')->whereNull('deleted_at')->max('id');

        return view('admin.users.includes.supervisor', ['user' => $user, 'supervisor_types' => $supervisor_types, 'maxSupervisorTypeId' => $maxSupervisorTypeId, 'selectedChannels' => $selectedChannels]);
    }

    public function includeFormSales($userId = null)
    {
        $user = $userId ? User::findOrFail($userId) : null;
        // $companies = Company::tenanted()->get()->pluck('name', 'id');
        $selectedProductBrands = $user?->productBrands?->pluck('id')->all() ?? null;
        $selectedChannels = $user?->channels?->pluck('id')->all() ?? null;

        return view('admin.users.includes.sales', ['user' => $user, 'selectedProductBrands' => $selectedProductBrands, 'selectedChannels' => $selectedChannels]);
    }
}
