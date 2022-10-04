<?php

namespace App\Exports;

use App\Models\Activity;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenerateExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // $startDate = Carbon::createFromFormat('Y-m-d', '2022-02-01')->startOfDay();
        // $endDate = Carbon::createFromFormat('Y-m-d', '2022-04-30')->endOfDay();
        // return DB::table('orders')
        //     ->join('customers', 'orders.customer_id', '=', 'customers.id')
        //     ->join('leads', 'orders.lead_id', '=', 'leads.id')
        //     ->join('users', 'leads.user_id', '=', 'users.id')
        //     ->join('channels', 'users.channel_id', '=', 'channels.id')
        //     ->join('activities', 'orders.id', '=', 'activities.order_id')
        //     ->whereIn('orders.payment_status', [3, 4, 6])
        //     ->whereBetween('deal_at', [$startDate, $endDate])
        //     ->whereIn('leads.channel_id', [2, 5, 8])
        //     ->where('leads.lead_category_id', 3)
        //     ->selectRaw('channels.name as channel_name, users.name as sales_name, IF(customers.last_name IS NULL, customers.first_name, CONCAT(customers.first_name, " ", customers.last_name)) as customer_name, customers.email, customers.phone, CASE WHEN activities.status = 1 THEN "HOT" WHEN activities.status = 2 THEN "WARM" WHEN activities.status = 3 THEN "COLD" WHEN activities.status = 4 THEN "CLOSED" END as status, COUNT(orders.id) as total_order, SUM(orders.total_price) as total_price')
        //     ->groupBy('orders.customer_id')
        //     ->get();

        // $startDate = Carbon::createFromFormat('Y-m-d', '2022-02-01')->startOfDay();
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
        //     ->whereIn('leads.channel_id', [2, 5, 8])
        //     ->where('leads.lead_category_id', 3)
        //     ->selectRaw('channels.name as channel_name, users.name as sales_name, IF(customers.last_name IS NULL, customers.first_name, CONCAT(customers.first_name, " ", customers.last_name)) as customer_name, customers.email, customers.phone, COUNT(orders.id) as total_order, SUM(IFNULL(orders.total_price, 0)) as total_price')
        //     ->groupBy('customers.id')
        //     ->get();

        $startDate = Carbon::createFromFormat('Y-m-d', '2022-02-01')->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', '2022-04-30')->endOfDay();
        $activities = Activity::whereBetween('activities.follow_up_datetime', [$startDate, $endDate])
            ->whereHas('lead', fn ($q) => $q->where('lead_category_id', 3)->whereIn('channel_id', [2, 5, 8]))
            ->get();

        $data = $activities->map(function ($activity) {
            return [
                'follow_up_datetime' => $activity->follow_up_datetime,
                'follow_up_method' => $activity->follow_up_method?->description,
                'status' => $activity->status?->description,
                'channel' => $activity->channel->name,
                'sales' => $activity->user->name,
                'customer' => $activity->customer->first_name . ' ' . $activity->customer->last_name,
                'email' => $activity->customer->email,
                'phone' => $activity->customer->phone,
                'invoice_number' => $activity->order?->invoice_number,
                'created_at' => $activity->order?->created_at,
                'order_status' => $activity->order?->status?->description,
                'payment_status' => $activity->order?->payment_status?->description,
                'total_price' => $activity->order?->total_price,
            ];
        });

        return $data;
    }

    public function headings(): array
    {
        // return [
        //     'Channel',
        //     'Sales',
        //     'Customer Name',
        //     'Customer Email',
        //     'Customer Phone',
        //     // 'Last Activity Status',
        //     'COUNT Total Order',
        //     'SUM Total Order',
        // ];

        return [
            'Follow Up Date',
            'Follow Up Method',
            'Activity Status',
            'Channel',
            'Sales',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Invoice Number',
            'Order Created At',
            'Order Status',
            'Payment Status',
            'Total Price',
        ];
    }
}
