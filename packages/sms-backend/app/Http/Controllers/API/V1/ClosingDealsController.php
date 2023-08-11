<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClosingDealsController extends BaseApiController
{
    public function noOfLeads(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $query = DB::table('leads')
            ->join('users', 'users.id', '=', 'leads.user_id')
            ->leftJoin('users as sl', 'users.supervisor_id', '=', 'sl.id')
            ->leftJoin('users as bum', 'sl.supervisor_id', '=', 'bum.id')
            ->join('channels', 'channels.id', '=', 'leads.channel_id')
            ->join('orders', 'orders.lead_id', '=', 'leads.id')
            ->join('activities', 'orders.id', '=', 'activities.order_id')
            ->selectRaw('
            leads.id as lead_id,
            leads.label,
            leads.customer_id,
            orders.invoice_number,
            leads.user_id,
            users.name,
            channels.name as channel,
            sl.name as store_leader,
            bum.name as bum,
            activities.id as activity_id,
            orders.id as order_id,
            orders.total_price as invoice_price,
            orders.amount_paid
        ')
            ->whereNull('leads.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereNull('orders.deleted_at')
            ->whereRaw('orders.status NOT IN (5,6)')
            ->whereRaw('orders.payment_status IN (2, 3, 4, 6)')
            ->whereDate('orders.deal_at', '>=', date($startDate))
            ->whereDate('orders.deal_at', '<=', date($endDate))
            ->where('users.type', 2);

        $user = user();
        if ($user->is_director) {
            // $company_id = request()->company_id ? [request()->company_id] : $user->company_ids;

            // $channel_ids = DB::table('channels')->whereIn('company_id', $company_id)->pluck('id')->all();
            $channel_ids = DB::table('channels')->pluck('id')->all();
            $query = $query->whereIn('leads.channel_id', $channel_ids ?? []);
        } elseif ($user->is_supervisor) {
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('leads.user_id', $user_ids ?? []);
        } else {
            $query = $query->where('leads.user_id', $user->id);
        }

        if (request()->supervisor_id) $query = $query->whereIn('leads.user_id', \App\Models\User::findOrFail(request()->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if (request()->channel_id) $query = $query->where('leads.channel_id', request()->channel_id);
        if (request()->sales_id) $query = $query->where('leads.user_id', request()->sales_id);
        if (request()->product_brand_id) $query = $query->whereIn('leads.id', \App\Models\ActivityBrandValue::where('product_brand_id', request()->product_brand_id)->pluck('lead_id')->all() ?? []);

        $result = $query->orderByDesc('leads.id')->get();
        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportLeadsClosingDeals($result), 'report-leads-closing-deals' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        foreach ($result as $r) {
            $r->user = \App\Models\User::find($r->user_id);
            $r->lead = \App\Models\Lead::find($r->lead_id);
            $r->customer = \App\Models\Customer::find($r->customer_id);
            $r->order = \App\Models\Order::find($r->order_id);
            $r->activity = \App\Models\Activity::find($r->activity_id);
        }

        return response()->json($result);
    }
}
