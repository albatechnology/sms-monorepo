<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyReportRequest;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Activity;
use App\Models\Report;
use App\Models\Channel;
use App\Services\ReportService;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FollowupPerChannelController extends Controller
{

    function date_compare($element1, $element2)
    {
        $datetime1 = strtotime($element1['datetime']);
        $datetime2 = strtotime($element2['datetime']);
        return $datetime1 - $datetime2;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        if ($request->ajax()) {
            $table = Datatables::of($query);

            // $table->addColumn('placeholder', '&nbsp;');
            // $table->addColumn('actions', '&nbsp;');

            // $table->editColumn('actions', function ($row) {
            //     $crudRoutePart = 'followup-per-channels';

            //     return view('partials.datatablesActions', compact(
            //         'crudRoutePart',
            //         'row'
            //     ));
            // });

            $table->addColumn('leads', function ($row) {
                return $row->channelLeads->count();
            });
            $table->addColumn('activities', function ($row) {
                return $row->channelActivities->count();
            });

            // $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.followupPerChannel.index');
    }

    public function getData()
    {
        $activities = Channel::selectRaw("channels.id as channel_id, channels.name, DATE_FORMAT(activities.created_at, '%m-%Y') as date, count('activities.id') as total_activities")
            ->join('activities', 'channels.id', 'activities.channel_id')
            ->groupBy('channels.id', 'channels.name', DB::raw("DATE_FORMAT(activities.created_at, '%m-%Y')"))
            ->orderBy('activities.created_at')
            ->orderBy('channels.id')
            ->get();

        $leads = Channel::selectRaw("channels.id as channel_id, channels.name, DATE_FORMAT(leads.created_at, '%m-%Y') as date, count('leads.id') as total_leads")
            ->join('leads', 'channels.id', 'leads.channel_id')
            ->groupBy('channels.id', 'channels.name', DB::raw("DATE_FORMAT(leads.created_at, '%m-%Y')"))
            ->orderBy('leads.created_at')
            ->orderBy('channels.id')
            ->get();

        $array = array_merge($activities->toArray(), $leads->toArray());
        $data = [];
        foreach ($array as $a) {
            $data[$a['channel_id']][$a['date']]['channel_id'] = $a['channel_id'];
            $data[$a['channel_id']][$a['date']]['name'] = $a['name'];
            $data[$a['channel_id']][$a['date']]['date'] = $a['date'];

            if (isset($a['total_leads']) && !isset($a['total_activities'])) {
                $data[$a['channel_id']][$a['date']]['total_leads'] = $a['total_leads'];
            } else {
                $data[$a['channel_id']][$a['date']]['total_activities'] = $a['total_activities'];
            }
        }

        $data2 = [];
        foreach ($data as $data) {
            foreach ($data as $a) {
                $data2[] = $a;
            }
        }

        $data3 = [];
        foreach ($data2 as $a) {
            if (isset($a['total_leads']) || isset($a['total_activities'])) {
                $leads = $a['total_leads'] ?? 0;
                $activities = $a['total_activities'] ?? 0;

                array_push($data3, [
                    'name' => $a['name'],
                    'date' => Carbon::createFromFormat('m-Y', $a['date'])->format('F Y'),
                    'total_leads' => $leads,
                    'total_activities' => $activities
                ]);
            }
        }

        $table = Datatables::of(collect($data3));
        $table->addColumn('placeholder', '&nbsp;');
        $table->rawColumns(['placeholder']);

        return $table->toJson();
    }

    public function create()
    {
        abort_if(Gate::denies('report_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.followupPerChannel.create');
    }

    public function store(StoreReportRequest $request)
    {
        $report = Report::create($request->validated());

        return redirect()->route('admin.followupPerChannel.index');
    }

    public function edit(Report $report)
    {
        abort_if(Gate::denies('report_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.followupPerChannel.edit', compact('report'));
    }

    public function update(UpdateReportRequest $request, Report $report)
    {
        $report->update($request->validated());

        return redirect()->route('admin.followupPerChannel.index');
    }

    public function show(Report $report)
    {
        abort_if(Gate::denies('report_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.followupPerChannel.show', compact('report'));
    }

    public function destroy(Report $report)
    {
        abort_if(Gate::denies('report_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $report->delete();

        return back();
    }

    public function massDestroy(MassDestroyReportRequest $request)
    {
        Report::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function reevaluate(Report $report)
    {
        app(ReportService::class)->reevaluateReport($report);

        return back()->with('message', 'Report reevaluated!');
    }

    // region sub report
    public function activity(Request $request)
    {
        abort_if(Gate::denies('report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {

            $validator = Validator::make($request->all(), [
                'start_date' => 'date',
                'end_date'   => 'date',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!empty($errors->first('start_date'))) {
                    $request->request->remove('start_date');
                    $request->request->remove('end_date');
                }
                if (!empty($errors->first('end_date'))) {
                    $request->request->remove('end_date');
                }
            }
            /*
            #$query = Activity::with(['channel','lead', 'customer'])->select(sprintf('%s.*', (new Activity)->table));
            */
            $query = Activity::join('channels', 'channels.id', '=', 'activities.channel_id')
                ->join('users', 'users.id', '=', 'activities.user_id')
                ->join('customers', 'customers.id', '=', 'activities.customer_id')
                ->leftJoin('addresses',  function ($join) {
                    $join->on('addresses.id', '=', 'customers.default_address_id');
                })
                ->select(DB::Raw('DATE(follow_up_datetime) as follow_up_date'), DB::Raw("CONCAT(customers.first_name,' ',customers.last_name) AS name"), 'customers.last_name', 'customers.phone', 'channels.name AS channel', 'follow_up_method', 'activities.status AS status', 'users.name AS lead', 'feedback', 'addresses.city');
            if ($request->has('start_date')) {
                if ($request->has('end_date')) {
                    $query->whereDate('follow_up_datetime', '>=', $request->input('start_date'));
                    $query->whereDate('follow_up_datetime', '<=', $request->input('end_date'));
                } else {
                    $query->whereDate('follow_up_datetime', '=', $request->input('start_date'));
                }
            }
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');

            $table->addColumn('follow_up_method', function ($row) {
                return \App\Enums\ActivityFollowUpMethod::fromValue($row->follow_up_method)->description ?? '';
            });

            $table->addColumn('status', function ($row) {
                return \App\Enums\ActivityStatus::fromValue($row->status)->description ?? '';
            });


            $table->filterColumn('name', function ($query, $keyword) {
                $query->where('first_name', 'LIKE', "%{$keyword}%");
                $query->orWhere('last_name', 'LIKE', "%{$keyword}%");
            });
            $table->filterColumn('phone', function ($query, $keyword) {
                $query->where('customers.phone', 'LIKE', "%{$keyword}%");
            });
            $table->filterColumn('follow_up_date', function ($query, $keyword) {
            });
            $table->filterColumn('channel', function ($query, $keyword) {
                $query->where('channels.name', 'LIKE', "%{$keyword}%");
            });
            $table->filterColumn('city', function ($query, $keyword) {
                $query->where('addresses.city', 'LIKE', "%{$keyword}%");
            });
            $table->filterColumn('lead', function ($query, $keyword) {
                $query->where('users.name', 'LIKE', "%{$keyword}%");
            });
            $table->filterColumn('follow_up_method', function ($query, $keyword) {
                $query->where('follow_up_method', 'REGEXP', $keyword);
            });
            $table->filterColumn('status', function ($query, $keyword) {
                $query->where('activities.status', 'REGEXP', $keyword);
            });


            $table->rawColumns(['placeholder', 'lead', 'channel', 'follow_up_method', 'status']);

            return $table->make(true);
        }

        return view('admin.reports.activity');
    }

    public function activityFollowUp(Request $request)
    {
        abort_if(Gate::denies('report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Activity::groupBy('username', 'user_email', 'channel_name', 'follow_up_date', 'follow_up_datetime', 'activities.channel_id', 'activities.user_id')
                ->leftJoin('users', 'users.id', '=', 'activities.user_id')
                ->leftJoin('users AS spv', 'spv.id', '=', 'users.supervisor_id')
                ->leftJoin('users AS area', 'area.id', '=', 'spv.supervisor_id')
                ->leftJoin('channels', 'channels.id', '=', 'activities.channel_id')
                ->where('users.type', '=', UserType::fromKey("SALES"))
                ->select('users.name as username', 'spv.name AS supervisor', 'area.name AS area', 'users.email as user_email', 'channels.name as channel_name', 'follow_up_datetime', DB::Raw('DATE(follow_up_datetime) as follow_up_date'), DB::Raw('COUNT(*) as total'));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');

            $table->editColumn('follow_up_date', function ($row) {
                return with(new Carbon($row->follow_up_date))->format('Y/m/d') ?? '';
            });
            $table->editColumn('username', function ($row) {
                return $row->username ?? '';
            });
            $table->editColumn('supervisor', function ($row) {
                return $row->supervisor ?? '';
            });
            $table->editColumn('user_email', function ($row) {
                return $row->user_email ?? '';
            });
            $table->editColumn('channel_name', function ($row) {
                return $row->channel_name ?? '';
            });
            $table->editColumn('total', function ($row) {
                return number_format($row->total, 0) ?? '';
            });

            $table->rawColumns(['placeholder', 'follow_up_date', 'channel']);

            return $table->make(true);
        }

        return view('admin.reports.activity_follow_up');
    }

    //endregion
}
