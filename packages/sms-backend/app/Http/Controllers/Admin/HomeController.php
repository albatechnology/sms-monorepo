<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exports\GenerateExport;
use App\Models\CartDemand;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Services\HelperService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class HomeController
{
    public function index()
    {
        $orderCount = Order::query()
            ->tenanted()
            ->where('created_at', '>', now()->subWeek())
            ->count();

        $customerCount = Customer::query()
            ->where('created_at', '>', now()->subWeek())
            ->count();

        $leadCount = Lead::query()
            ->tenanted()
            ->whereIn('type', [LeadType::LEADS, LeadType::PROSPECT])
            ->where('created_at', '>', now()->subWeek())
            ->count();

        $paymentSum = Payment::query()
            ->tenanted()
            ->where('status', PaymentStatus::APPROVED)
            ->where('created_at', '>', now()->subWeek())
            ->sum('amount');

        $panels = [
            'new_order_week_count'    => [
                'value' => $orderCount,
                'label' => 'New Orders (7 days)',
                'url'   => route('admin.orders.index'),
                'icon'  => 'ion ion-bag',
                'theme' => 'bg-info'
            ],
            'new_customer_week_count' => [
                'value' => $customerCount,
                'label' => 'New Customers (7 days, all companies)',
                'url'   => route('admin.customers.index'),
                'icon'  => 'ion-person-add',
                'theme' => 'bg-success'
            ],
            'new_lead_week_count'     => [
                'value' => $leadCount,
                'label' => 'New Leads (7 days)',
                'url'   => route('admin.leads.index'),
                'icon'  => 'ion-stats-bars',
                'theme' => 'bg-warning'
            ],
            'cash_flow_week_sum'      => [
                'value' => HelperService::formatRupiah($paymentSum),
                'label' => 'Cash Flow (7 days)',
                'url'   => route('admin.payments.index'),
                'icon'  => 'ion ion-cash',
                'theme' => 'bg-danger'
            ],
        ];

        return view('home', compact('panels'));
    }

    public function getCartDemands()
    {
        $cartDemands = CartDemand::with(['order', 'user'])->whereOrdered()->whereHas('order', function ($q) {
            $q->whereNotIn('status', [OrderStatus::CANCELLED, OrderStatus::RETURNED]);
        })->select(sprintf('%s.*', (new CartDemand)->table));
        $table = Datatables::of($cartDemands);

        $table->addColumn('placeholder', '&nbsp;');
        $table->addColumn('actions', '&nbsp;');

        $table->editColumn('actions', function ($row) {
            return '<a href="' . route('admin.orders.show', $row->order?->id) . '" class="btn btn-primary btn-sm">Update</a>';
        });
        $table->addColumn('sales', function ($row) {
            return $row?->user?->name ?? '';
        });
        $table->editColumn('total_price', function ($row) {
            return helper()->formatRupiah($row->total_price) ?: "";
        });
        $table->addColumn('invoice_number', function ($row) {
            return $row?->order?->invoice_number ?? '';
        });
        $table->rawColumns(['actions', 'placeholder']);

        return $table->make(true);
    }

    /**
     * set active tenant for this session
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function setActiveTenant(Request $request): RedirectResponse
    {
        tenancy()->setActiveTenantFromRequest($request);

        return redirect()->back();
    }

    public function generate()
    {
        return Excel::download(new GenerateExport, 'leads.xlsx');
        // $startDate = Carbon::createFromFormat('Y-m-d', '2022-01-01')->startOfDay();
        // $endDate = Carbon::createFromFormat('Y-m-d', '2022-04-30')->endOfDay();
        // $activities = Activity::whereBetween('activities.follow_up_datetime', [$startDate, $endDate])
        //     // ->whereHas('lead', fn ($q) => $q->where('lead_category_id', 3)->whereIn('channel_id', [2, 5, 8]))
        //     ->get();
        // // return $activities;
        // $data = $activities->map(function ($activity) {
        //     return [
        //         'channel' => $activity->channel->name,
        //         'sales' => $activity->user->name,
        //         'customer' => $activity->customer->first_name . ' ' . $activity->customer->last_name,
        //         'email' => $activity->customer->email,
        //         'phone' => $activity->customer->phone,
        //         'invoice_number' => $activity->order?->invoice_number,
        //         'total_price' => $activity->order?->total_price,
        //     ];
        // });

        // return $data;
    }

    public function generateBackup()
    {
        return Excel::download(new GenerateExport, 'leads.xlsx');
        // $startDate = Carbon::createFromFormat('Y-m-d', '2022-01-01')->startOfDay();
        // $endDate = Carbon::createFromFormat('Y-m-d', '2022-04-30')->endOfDay();
        // return Lead::leftJoin('orders', 'leads.id', '=', 'orders.lead_id')
        //     // ->join('customers', 'orders.customer_id', '=', 'customers.id')
        //     ->join('customers', 'leads.customer_id', '=', 'customers.id')
        //     // ->join('leads', 'orders.lead_id', '=', 'leads.id')
        //     ->join('users', 'leads.user_id', '=', 'users.id')
        //     ->join('channels', 'users.channel_id', '=', 'channels.id')
        //     ->leftJoin('activities', 'leads.id', '=', 'activities.lead_id')
        //     // ->whereIn('orders.payment_status', [3, 4, 6])
        //     ->whereBetween('activities.follow_up_datetime', [$startDate, $endDate])
        //     // ->whereIn('leads.channel_id', [2, 5, 8])
        //     // ->where('leads.lead_category_id', 3)
        //     ->selectRaw('channels.name as channel_name, users.name as sales_name, IF(customers.last_name IS NULL, customers.first_name, CONCAT(customers.first_name, " ", customers.last_name)) as customer_name, customers.email, customers.phone, COUNT(orders.id) as total_order, SUM(IFNULL(orders.total_price, 0)) as total_price')
        //     ->groupBy('customers.id')
        //     ->get();
    }

    public function testEmail(Request $request)
    {
        $lead = Lead::findOrFail(69923);
        // $lead->nextStatusAndQueue();
        dd($lead);
        // $startDate = Carbon::now()->startOfMonth();
        // $endDate = Carbon::now()->endOfMonth();
        // if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
        //     $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
        //     $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        // }

        // $user = User::find(85);
        // // $company_id = $request->company_id ? [$request->company_id] : $user->company_ids ?? $user->userCompanies->pluck('id')->all();
        // $company_id = $request->company_id ? $request->company_id : ($user->company_id ?? $user->company_ids[0] ?? 1);

        // $productBrands = DB::table('product_brands')->selectRaw('name as product_brand, 0 as estimated_value, 0 as order_value')->whereNull('deleted_at')->where('show_in_moves', 1)->where('company_id', $company_id)->get()->toArray();

        // $query = DB::table('users')
        //     ->leftJoin('channels', 'channels.id', '=', 'users.channel_id')
        //     ->leftJoin('leads', 'leads.user_id', '=', 'users.id')
        //     ->join('customers', 'customers.id', '=', 'leads.customer_id')
        //     ->leftJoin('users as sl', 'users.supervisor_id', '=', 'sl.id')
        //     ->leftJoin('users as bum', 'sl.supervisor_id', '=', 'bum.id')
        //     ->leftJoin('activity_brand_values', 'activity_brand_values.lead_id', '=', 'leads.id')
        //     ->leftJoin('product_brands', 'product_brands.id', '=', 'activity_brand_values.product_brand_id')
        //     ->selectRaw('
        //     users.id as user_id,
        //     users.name as sales,
        //     sl.name as store_leader,
        //     bum.name as bum,
        //     channels.name as channel,
        //     COUNT(DISTINCT leads.id) as total_leads,
        //     product_brands.name as product_brand,
        //     SUM(activity_brand_values.estimated_value) as estimated_value,
        //     SUM(activity_brand_values.order_value) as order_value
        //     ')
        //     ->whereNull('leads.deleted_at')
        //     ->whereNull('users.deleted_at')
        //     ->where('users.type', 2)
        //     ->whereBetween('leads.created_at', [$startDate, $endDate]);

        // if ($request->sales_name) $query = $query->where('users.name', 'like', '%' . $request->sales_name . '%');
        // if ($user->is_director || $user->is_digital_marketing) {
        //     // $channel_ids = DB::table('channels')->whereIn('company_id', $company_id)->pluck('id')->all();
        //     $channel_ids = DB::table('channels')->where('company_id', $company_id)->pluck('id')->all();
        //     $query = $query->whereIn('leads.channel_id', $channel_ids ?? []);
        // } elseif ($user->is_supervisor) {
        //     $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
        //     $query = $query->whereIn('leads.user_id', $user_ids ?? []);
        // } else {
        //     $query = $query->where('leads.user_id', $user->id);
        // }

        // if ($request->supervisor_id) $query = $query->whereIn('leads.user_id', \App\Models\User::findOrFail($request->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        // if ($request->channel_id) $query = $query->where('leads.channel_id', $request->channel_id);
        // if ($request->user_id) $query = $query->where('leads.user_id', $request->user_id);
        // if ($request->product_brand_name) $query = $query->where('product_brands.product_brand_name', $request->product_brand_name);
        // if ($request->sales_name) $query = $query->where('users.name', $request->sales_name);

        // $result = $query->groupByRaw('users.id')
        //     ->orderByDesc('leads.id')
        //     ->get();

        // $datas = [];
        // foreach ($result as $r) {
        //     $datas[$r->user_id]['user_id'] = $r->user_id;
        //     $datas[$r->user_id]['sales'] = $r->sales;
        //     $datas[$r->user_id]['store_leader'] = $r->store_leader;
        //     $datas[$r->user_id]['bum'] = $r->bum;
        //     $datas[$r->user_id]['channel'] = $r->channel;
        //     $datas[$r->user_id]['total_leads'] = $r->total_leads;

        //     $key = array_search($r->product_brand, array_column($productBrands, 'product_brand'));
        //     if (!isset($datas[$r->user_id]['product_brands'])) {
        //         $datas[$r->user_id]['product_brands'] = $productBrands;
        //     }

        //     $datas[$r->user_id]['product_brands'][$key] = [
        //         'product_brand' => $r->product_brand,
        //         'estimated_value' => (int)$r->estimated_value,
        //         'order_value' => (int)$r->order_value,
        //     ];

        //     if (isset($datas[$r->user_id]['total_estimated'])) {
        //         $datas[$r->user_id]['total_estimated'] += $r->estimated_value;
        //     } else {
        //         $datas[$r->user_id]['total_estimated'] = $r->estimated_value;
        //     }

        //     if (isset($datas[$r->user_id]['total_quotation'])) {
        //         $datas[$r->user_id]['total_quotation'] += $r->order_value;
        //     } else {
        //         $datas[$r->user_id]['total_quotation'] = $r->order_value;
        //     }
        // }
        // return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportBrands(array_values($datas)), 'report-brands' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);

        // Mail::to(['albaprogrammer2@gmail.com', 'mis.support@melandas-indonesia.com'])->send(new ReportLeads());
        // return (new ReportLeads())->render();
    }
}
