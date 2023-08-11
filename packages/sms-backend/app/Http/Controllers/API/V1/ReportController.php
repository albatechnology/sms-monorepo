<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Exports\SalesRevenueExport;
use App\Http\Resources\V1\Report\ReportResource;
use App\Models\Report;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ReportController extends BaseApiController
{
    const load_relation = ['reportable'];

    /**
     * Get report
     *
     * Returns report by id
     *
     * @param Report $report
     * @return  ReportResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[CustomOpenApi\Operation(id: 'ReportShow', tags: [Tags::Report, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ReportResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(Report $report)
    {
        $this->authorize('show', $report);
        return new ReportResource($report->loadMissing(self::load_relation)->checkTenantAccess());
    }

    /**
     * Show all report.
     *
     * Show all report
     *
     */
    #[CustomOpenApi\Operation(id: 'ReportIndex', tags: [Tags::Report, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Report::class)]
    #[CustomOpenApi\Response(resource: ReportResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(
            Report::class,
            ReportResource::class,
            fn ($query) => $query->with(self::load_relation)->tenanted()
        );
    }

    public function salesRevenue(Request $request)
    {
        $user = user();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $query = DB::table('reports')
            ->join('targets', 'reports.id', '=', 'targets.report_id')
            ->join('users', 'reports.reportable_id', '=', 'users.id')
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->join('channels', 'users.channel_id', '=', 'channels.id')
            ->selectRaw(
                'users.name as sales_name,
                companies.name as company_name,
                channels.name as channel_name,
                CONCAT(MONTH(reports.start_date), " - ", YEAR(reports.start_date)) as periode,
                IFNULL(targets.value, 0) as sales_order,
                IFNULL(targets.target, 0) as target,
                IFNULL(((targets.value / targets.target) * 100), 0) as achievement'
            )
            ->where('reports.reportable_type', 'user')
            ->where('reports.start_date', '>=', $startDate)
            ->where('reports.end_date', '<=', $endDate)
            ->where('targets.model_type', 'user')
            ->where('targets.type', 0);

        if ($user->is_director) {
            $company_id = $request->company_id ? [$request->company_id] : $user->company_ids;
            $query = $query->whereIn('companies.id', $company_id ?? []);
        } elseif ($user->is_supervisor) {
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('reports.reportable_id', $user_ids ?? []);
        } else {
            $query = $query->where('reports.reportable_id', $user->id);
        }

        if ($request->supervisor_id) $query = $query->whereIn('reports.reportable_id', \App\Models\User::findOrFail($request->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if ($request->channel_id) $query = $query->where('channels.id', $request->channel_id);

        if ($request->paginate == 1) {
            $query = $query->groupByRaw('users.name, periode, companies.name, channel_name, targets.target, sales_order')->paginate($request->per_page ?? 15);
        } else {
            $query = $query->groupByRaw('users.name, periode, companies.name, channel_name, targets.target, sales_order')->get();
        }
        return response()->json($query);
        // $export = new SalesRevenueExport($query);
        // return Excel::download($export, 'sales-revenue.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function reportByLeads(Request $request)
    {
        $user = user();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $query = DB::table('leads')
            ->leftJoin('activities', 'leads.id', '=', 'activities.lead_id')
            ->leftJoin('orders', 'leads.id', '=', 'orders.lead_id')
            ->join('users as sales', 'leads.user_id', '=', 'sales.id')
            ->leftJoin('users as sl', 'sales.supervisor_id', '=', 'sl.id')
            ->leftJoin('users as bum', 'sl.supervisor_id', '=', 'bum.id')
            ->selectRaw(
                'sales.name as sales,
                sl.name as store_leader,
                bum.name as bum,
                (
                    SELECT
                            COUNT(leads.id)
                    FROM
                        leads
                        JOIN activities ON activities.lead_id = leads.id
                    where
                        leads.user_id = sales.id
                        AND leads.created_at >= "' . $startDate . '"
                        AND leads.created_at <= "' . $endDate . '"
                        AND leads.deleted_at IS NULL
                        AND activities.deleted_at IS NULL
                ) as total_leads,
                (
                    SELECT
                        COUNT(leads.id)
                    FROM
                        leads
                        JOIN activities ON activities.lead_id = leads.id
                    where
                        leads.user_id = sales.id
                        AND activities.status = 4
                        AND leads.created_at >= "' . $startDate . '"
                        AND leads.created_at <= "' . $endDate . '"
                        AND leads.deleted_at IS NULL
                        AND activities.deleted_at IS NULL
                ) as CLOSED,
                (
                    SELECT
                        COUNT(leads.id)
                    FROM
                        leads
                        JOIN activities ON activities.lead_id = leads.id
                    where
                        leads.user_id = sales.id
                        AND activities.status = 3
                        AND leads.created_at >= "' . $startDate . '"
                        AND leads.created_at <= "' . $endDate . '"
                        AND leads.deleted_at IS NULL
                        AND activities.deleted_at IS NULL
                ) as COLD,
                (
                    SELECT
                        COUNT(leads.id)
                    FROM
                        leads
                        JOIN activities ON activities.lead_id = leads.id
                    where
                        leads.user_id = sales.id
                        AND activities.status = 2
                        AND leads.created_at >= "' . $startDate . '"
                        AND leads.created_at <= "' . $endDate . '"
                        AND leads.deleted_at IS NULL
                        AND activities.deleted_at IS NULL
                ) as WARM,
                (
                    SELECT
                        COUNT(leads.id)
                    FROM
                        leads
                        JOIN activities ON activities.lead_id = leads.id
                    where
                        leads.user_id = sales.id
                        AND activities.status = 1
                        AND leads.created_at >= "' . $startDate . '"
                        AND leads.created_at <= "' . $endDate . '"
                        AND leads.deleted_at IS NULL
                        AND activities.deleted_at IS NULL
                ) as HOT,
                (
                    SELECT
                        count(orders.id)
                    FROM
                        orders
                        JOIN leads ON leads.id = orders.lead_id
                    where
                        orders.payment_status IN (2, 3, 4, 6)
                        AND orders.user_id = sales.id
                        AND leads.created_at >= "' . $startDate . '"
                        AND leads.created_at <= "' . $endDate . '"
                        AND orders.deleted_at IS NULL
                        AND leads.deleted_at IS NULL
                ) as DEALS,
                (
                    SELECT
                        IFNULL(SUM(orders.total_price), 0)
                    FROM
                        orders
                        JOIN leads ON leads.id = orders.lead_id
                    where
                        orders.payment_status IN (2, 3, 4, 6)
                        AND orders.user_id = sales.id
                        AND orders.created_at >= "' . $startDate . '"
                        AND orders.created_at <= "' . $endDate . '"
                        AND orders.deleted_at IS NULL
                        AND leads.deleted_at IS NULL
                ) as invoice_price'
            )
            ->where('sales.type', 2)
            ->whereDate('leads.created_at', '>=', $startDate)
            ->whereDate('leads.created_at', '<=', $endDate)
            ->whereNull('leads.deleted_at');

        if ($user->is_director) {
            $company_id = $request->company_id ? [$request->company_id] : $user->company_ids;

            $channel_ids = DB::table('channels')->whereIn('company_id', $company_id)->pluck('id')->all();
            $query = $query->whereIn('leads.channel_id', $channel_ids ?? []);
        } elseif ($user->is_supervisor) {
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('leads.user_id', $user_ids ?? []);
        } else {
            $query = $query->where('leads.user_id', $user->id);
        }

        if ($request->supervisor_id) $query = $query->whereIn('leads.user_id', \App\Models\User::findOrFail($request->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if ($request->channel_id) $query = $query->where('leads.channel_id', $request->channel_id);

        if ($request->paginate == 1) {
            $query = $query->groupBy('sales.id')->paginate($request->per_page ?? 15);
        } else {
            $query = $query->groupBy('sales.id')->get();
        }
        return response()->json($query);
    }
}
