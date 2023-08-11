<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Channel;

class ApiReportService
{
    public function reportLeads(int $user_id = null, bool $is_export = false)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if ((request()->has('start_date') && request()->start_date != '') && (request()->has('end_date') && request()->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', request()->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', request()->end_date)->endOfDay();
        }

        $query = DB::table('users')
            ->leftJoin('users as sl', 'users.supervisor_id', '=', 'sl.id')
            ->leftJoin('users as bum', 'sl.supervisor_id', '=', 'bum.id')
            ->leftJoin('leads', 'leads.user_id', '=', 'users.id')
            ->join('channels', 'channels.id', '=', 'leads.channel_id')
            ->leftJoin('activities', function ($leftJoin) {
                $leftJoin->on('activities.lead_id', '=', 'leads.id')
                    ->whereRaw('activities.id IN (SELECT MAX(a2.id) FROM activities a2 JOIN leads l2 ON l2.id = a2.lead_id GROUP BY l2.id)');
            })
            ->leftJoin('activity_brand_values', 'activity_brand_values.activity_id', '=', 'activities.id')
            ->leftJoin('orders', function ($leftJoin) use ($startDate, $endDate) {
                $leftJoin->on('orders.lead_id', '=', 'leads.id')->whereRaw('orders.payment_status IN (2, 3, 4, 6)')->whereDate('orders.deal_at', '>=', date($startDate))->whereDate('orders.deal_at', '<=', date($endDate));
            })
            ->leftJoin('orders as quotation', function ($leftJoin) {
                $leftJoin->on('quotation.lead_id', '=', 'leads.id')->whereRaw('quotation.status = 1 AND quotation.payment_status = 1');
            })
            ->selectRaw('leads.user_id,
        users.name,
        channels.id as channel_id,
        channels.name as channel,
        sl.name as store_leader,
        bum.id as supervisor_id,
        bum.name as bum,
        COUNT(DISTINCT leads.id) as total_leads,
        COUNT(IF(activities.status = 4, 1, NULL)) as closed,
        COUNT(IF(activities.status = 3, 1, NULL)) as cold,
        COUNT(IF(activities.status = 2, 1, NULL)) as warm,
        COUNT(IF(activities.status = 1, 1, NULL)) as hot,
        SUM(quotation.total_price) as quotation,
        COUNT(orders.id) as total_lead_deals,
        SUM(activity_brand_values.estimated_value) as estimated_value,
        SUM(orders.total_price) as invoice_price,
        SUM(orders.amount_paid) as amount_paid')->whereNull('leads.deleted_at')
            ->whereNull('orders.deleted_at')
            ->whereDate('orders.deal_at', '>=', date($startDate))
            ->whereDate('orders.deal_at', '<=', date($endDate))
            ->where('users.type', 2);

        $user = $is_export ? \App\Models\User::find($user_id) : user();
        $userType = '';
        if ($user->is_director) {
            $userType = 'director';
            $company_id = request()->company_id ? [request()->company_id] : $user->company_ids;
            // $company_id = [1, 2];

            $channel_ids = DB::table('channels')->whereIn('company_id', $company_id)->pluck('id')->all();
            $query = $query->whereIn('leads.channel_id', $channel_ids ?? []);
        } elseif ($user->is_supervisor) {
            $userType = $user->supervisor_type_id == 1 ? 'sl' : 'bum';
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('leads.user_id', $user_ids ?? []);
        } else {
            $userType = 'sales';
            $query = $query->where('leads.user_id', $user->id);
        }

        if (request()->supervisor_id) $query = $query->whereIn('leads.user_id', \App\Models\User::findOrFail(request()->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if (request()->channel_id) $query = $query->where('leads.channel_id', request()->channel_id);

        $result = $query->groupByRaw('orders.user_id')
            ->orderByDesc('orders.id')
            ->get();

        if ($userType == 'director' || $userType == 'bum') {
            // group by bum
            $datas = [];
            foreach ($result as $r) {
                $datas[$r->bum][$r->channel][] = $r;
            }

            $dataBum = [];
            $dataChannel = [];
            $dataSales = [];
            foreach ($datas as $bumName => $channels) {
                $total_leads_bum = 0;
                $closed_bum = 0;
                $cold_bum = 0;
                $warm_bum = 0;
                $hot_bum = 0;
                $quotation_bum = 0;
                $total_lead_deals_bum = 0;
                $estimated_value_bum = 0;
                $invoice_price_bum = 0;
                $amount_paid_bum = 0;

                $dataChannel = [];
                foreach ($channels as $channelName => $sales) {
                    $supervisor_id = null;
                    $channel_id = null;
                    $dataSales = [];
                    $total_leads_channel = 0;
                    $closed_channel = 0;
                    $cold_channel = 0;
                    $warm_channel = 0;
                    $hot_channel = 0;
                    $quotation_channel = 0;
                    $total_lead_deals_channel = 0;
                    $estimated_value_channel = 0;
                    $invoice_price_channel = 0;
                    $amount_paid_channel = 0;

                    foreach ($sales as $s) {
                        $total_leads_channel += $s->total_leads ?? 0;
                        $closed_channel += $s->closed ?? 0;
                        $cold_channel += $s->cold ?? 0;
                        $warm_channel += $s->warm ?? 0;
                        $hot_channel += $s->hot ?? 0;
                        $quotation_channel += $s->quotation ?? 0;
                        $total_lead_deals_channel += $s->total_lead_deals ?? 0;
                        $estimated_value_channel += $s->estimated_value ?? 0;
                        $invoice_price_channel += $s->invoice_price ?? 0;
                        $amount_paid_channel += $s->amount_paid ?? 0;
                        $dataSales[] = $s;
                        $supervisor_id = $s->supervisor_id;
                        $channel_id = $s->channel_id;
                    }

                    $dataChannel[$channelName] = [
                        "supervisor_id" => $supervisor_id,
                        "channel_id" => $channel_id,
                        "name" => $channelName,
                        "channel" => null,
                        "bum" => $bumName,
                        "total_leads" => $total_leads_channel ?? 0,
                        "closed" => $closed_channel ?? 0,
                        "cold" => $cold_channel ?? 0,
                        "warm" => $warm_channel ?? 0,
                        "hot" => $hot_channel ?? 0,
                        "quotation" => $quotation_channel ?? 0,
                        "total_lead_deals" => $total_lead_deals_channel ?? 0,
                        "estimated_value" => $estimated_value_channel ?? 0,
                        "invoice_price" => $invoice_price_channel ?? 0,
                        "amount_paid" => $amount_paid_channel ?? 0,
                        "sales" => $dataSales
                    ];

                    $total_leads_bum += $total_leads_channel;
                    $closed_bum += $closed_channel;
                    $cold_bum += $cold_channel;
                    $warm_bum += $warm_channel;
                    $hot_bum += $hot_channel;
                    $quotation_bum += $quotation_channel;
                    $total_lead_deals_bum += $total_lead_deals_channel;
                    $estimated_value_bum += $estimated_value_channel;
                    $invoice_price_bum += $invoice_price_channel;
                    $amount_paid_bum += $amount_paid_channel;
                }

                $dataBum[$bumName] = [
                    "supervisor_id" => $supervisor_id,
                    "name" => $bumName,
                    "channel" => null,
                    "bum" => null,
                    "total_leads" => $total_leads_bum,
                    "closed" => $closed_bum,
                    "cold" => $cold_bum,
                    "warm" => $warm_bum,
                    "hot" => $hot_bum,
                    "quotation" => $quotation_bum,
                    "total_lead_deals" => $total_lead_deals_bum,
                    "estimated_value" => $estimated_value_bum,
                    "invoice_price" => $invoice_price_bum,
                    "amount_paid" => $amount_paid_bum,
                ];

                $dataBum[$bumName]['channels'] = array_values($dataChannel);
            }
            return array_values($dataBum ?? []);
        }
        // elseif ($userType == 'sl') {
        //     return response()->json($result);
        // } else {
        //     return response()->json($result);
        // }
        return $result;
    }

    public function reportLeadsNew(int $user_id = null, bool $is_export = false, $company_id = null)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if ((request()->has('start_date') && request()->start_date != '') && (request()->has('end_date') && request()->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', request()->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', request()->end_date)->endOfDay();
        }

        $query = DB::table('leads')
            ->join('users', 'users.id', '=', 'leads.user_id')
            ->leftJoin('users as sl', 'users.supervisor_id', '=', 'sl.id')
            ->leftJoin('users as bum', 'sl.supervisor_id', '=', 'bum.id')
            ->join('channels', 'channels.id', '=', 'leads.channel_id')
            ->leftJoin('activities', 'activities.lead_id', '=', 'leads.id')
            ->selectRaw('
                leads.user_id,
                users.name,
                channels.id as channel_id,
                channels.name as channel,
                sl.name as store_leader,
                bum.id as supervisor_id,
                bum.name as bum,
                (
                    SELECT COUNT(a1.lead_id) FROM activities a1 WHERE a1.user_id = leads.user_id AND DATE(a1.created_at) >= "' . $startDate . '" AND DATE(a1.created_at) <= "' . $endDate . '" AND a1.deleted_at IS NULL
                ) as total_leads,
                (
                    SELECT COUNT(a2.lead_id) FROM activities a2 WHERE a2.user_id = leads.user_id AND DATE(a2.created_at) >= "' . $startDate . '" AND DATE(a2.created_at) <= "' . $endDate . '" AND a2.status=4 AND a2.deleted_at IS NULL
                    ) as closed,
                (
                    SELECT COUNT(a3.lead_id) FROM activities a3 WHERE a3.user_id = leads.user_id AND DATE(a3.created_at) >= "' . $startDate . '" AND DATE(a3.created_at) <= "' . $endDate . '" AND a3.status=3 AND a3.deleted_at IS NULL
                    ) as cold,
                (
                    SELECT COUNT(a4.lead_id) FROM activities a4 WHERE a4.user_id = leads.user_id AND DATE(a4.created_at) >= "' . $startDate . '" AND DATE(a4.created_at) <= "' . $endDate . '" AND a4.status=2 AND a4.deleted_at IS NULL
                    ) as warm,
                (
                    SELECT SUM(activity_brand_values.estimated_value) FROM activity_brand_values WHERE activity_brand_values.user_id = leads.user_id AND DATE(activity_brand_values.created_at) >= "' . $startDate . '" AND DATE(activity_brand_values.created_at) <= "' . $endDate . '" AND leads.deleted_at IS NULL
                ) as estimated_value,
                (
                    SELECT COUNT(a5.lead_id) FROM activities a5 WHERE a5.user_id = leads.user_id AND DATE(a5.created_at) >= "' . $startDate . '" AND DATE(a5.created_at) <= "' . $endDate . '" AND a5.status=1 AND a5.deleted_at IS NULL
                    ) as hot,
                (
                    SELECT COUNT(distinct l3.id) FROM leads l3 WHERE l3.user_id = leads.user_id AND DATE(l3.created_at) >= "' . $startDate . '" AND DATE(l3.created_at) <= "' . $endDate . '" AND l3.type = 4 AND l3.deleted_at IS NULL
                ) as drop_leads,
                (
                    SELECT SUM(orders.total_price) FROM orders WHERE orders.user_id = leads.user_id AND DATE(orders.created_at) >= "' . $startDate . '" AND DATE(orders.created_at) <= "' . $endDate . '" AND orders.deleted_at IS NULL AND orders.status NOT IN (5,6)
                ) as quotation,
                (
                    SELECT count(orders.id) FROM orders WHERE orders.user_id = leads.user_id AND orders.payment_status IN (2, 3, 4, 6) AND DATE(orders.deal_at) >= "' . $startDate . '" AND DATE(orders.deal_at) <= "' . $endDate . '" AND orders.deleted_at IS NULL AND orders.status NOT IN (5,6)
                ) as total_lead_deals,
                (
                    SELECT SUM(orders.total_price) FROM orders WHERE orders.user_id = leads.user_id AND orders.payment_status IN (2, 3, 4, 6) AND DATE(orders.deal_at) >= "' . $startDate . '" AND DATE(orders.deal_at) <= "' . $endDate . '" AND orders.deleted_at IS NULL AND orders.status NOT IN (5,6)
                ) as invoice_price,
                (
                    SELECT SUM(orders.amount_paid) FROM orders WHERE orders.user_id = leads.user_id AND orders.payment_status IN (2, 3, 4, 6) AND DATE(orders.deal_at) >= "' . $startDate . '" AND DATE(orders.deal_at) <= "' . $endDate . '" AND orders.deleted_at IS NULL AND orders.status NOT IN (5,6)
                ) as amount_paid
            ')
            ->whereNull('leads.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereDate('leads.created_at', '>=', $startDate)
            ->whereDate('leads.created_at', '<=', $endDate)
            ->where('users.type', 2);

        if (request()->sales_name) $query = $query->where('users.name', 'like', '%' . request()->sales_name . '%');

        $user = $is_export ? \App\Models\User::find($user_id) : user();
        $userType = '';
        if ($user->is_director) {
            $userType = 'director';
            if ($company_id == null) {
                $company_id = request()->company_id ? request()->company_id : $user->company_id;
            }

            $channel_ids = DB::table('channels')->where('company_id', $company_id)->pluck('id')->all();
            $query = $query->whereIn('leads.channel_id', $channel_ids ?? []);

            // $company_id = request()->company_id ? [request()->company_id] : $user->company_ids;

            // $channel_ids = DB::table('channels')->whereIn('company_id', $company_id)->pluck('id')->all();
            // $query = $query->whereIn('leads.channel_id', $channel_ids ?? []);
        } elseif ($user->is_supervisor) {
            $userType = $user->supervisor_type_id == 1 ? 'sl' : 'bum';
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('leads.user_id', $user_ids ?? []);
        } else {
            $userType = 'sales';
            $query = $query->where('leads.user_id', $user->id);
        }

        if (request()->supervisor_id) $query = $query->whereIn('leads.user_id', \App\Models\User::findOrFail(request()->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if (request()->channel_id) $query = $query->where('leads.channel_id', request()->channel_id);

        $result = $query->groupByRaw('leads.user_id')
            ->orderByDesc('leads.id')
            ->get();

        // $inactiveSales = \App\Models\User::whereNotIn('id', $result->pluck('user_id')->all())->where('type', 2)->get();

        $allTotalLeads = 0;
        $allTotalClosed = 0;
        $allTotalDrop = 0;
        $allTotalCold = 0;
        $allTotalWarm = 0;
        $allTotalHot = 0;
        $allTotalQuotation = 0;
        $allTotalLeadDeals = 0;
        $allTotalEstimatedValue = 0;
        $allTotalInvoicePrice = 0;
        $allTotalAmountPaid = 0;

        if ($userType == 'director' || $userType == 'bum' || $userType == 'sl') {
            // group by bum
            $datas = [];
            foreach ($result as $r) {
                $datas[$r->bum][$r->channel][] = $r;
            }

            $dataBum = [];
            $dataChannel = [];
            $dataSales = [];
            foreach ($datas as $bumName => $channels) {
                $total_leads_bum = 0;
                $closed_bum = 0;
                $drop_bum = 0;
                $cold_bum = 0;
                $warm_bum = 0;
                $hot_bum = 0;
                $quotation_bum = 0;
                $total_lead_deals_bum = 0;
                $estimated_value_bum = 0;
                $invoice_price_bum = 0;
                $amount_paid_bum = 0;

                $dataChannel = [];
                foreach ($channels as $channelName => $sales) {
                    $supervisor_id = null;
                    $channel_id = null;
                    $dataSales = [];
                    $total_leads_channel = 0;
                    $closed_channel = 0;
                    $cold_channel = 0;
                    $drop_channel = 0;
                    $warm_channel = 0;
                    $hot_channel = 0;
                    $quotation_channel = 0;
                    $total_lead_deals_channel = 0;
                    $estimated_value_channel = 0;
                    $invoice_price_channel = 0;
                    $amount_paid_channel = 0;

                    foreach ($sales as $s) {
                        $total_leads_channel += $s->total_leads ?? 0;
                        $closed_channel += $s->closed ?? 0;
                        $drop_channel += $s->drop_leads ?? 0;
                        $cold_channel += $s->cold ?? 0;
                        $warm_channel += $s->warm ?? 0;
                        $hot_channel += $s->hot ?? 0;
                        $quotation_channel += $s->quotation ?? 0;
                        $total_lead_deals_channel += $s->total_lead_deals ?? 0;
                        $estimated_value_channel += $s->estimated_value ?? 0;
                        $invoice_price_channel += $s->invoice_price ?? 0;
                        $amount_paid_channel += $s->amount_paid ?? 0;
                        $dataSales[] = $s;
                        $supervisor_id = $s->supervisor_id;
                        $channel_id = $s->channel_id;
                    }

                    // $inactiveSalesData = array_filter($inactiveSales->toArray(), function ($element) use ($s) {
                    //     return isset($element['channel_id']) && $element['channel_id'] == $s->channel_id;
                    // });

                    // foreach ($inactiveSalesData as $sales) {
                    //     array_push($dataSales, [
                    //         "user_id" => $sales['id'],
                    //         "name" => $sales['name'],
                    //         "channel_id" => null,
                    //         "channel" => null,
                    //         "store_leader" => null,
                    //         "supervisor_id" => null,
                    //         "bum" => null,
                    //         "total_leads" => 0,
                    //         "closed" => 0,
                    //         "cold" => 0,
                    //         "warm" => 0,
                    //         "estimated_value" => 0,
                    //         "hot" => 0,
                    //         "drop_leads" => 0,
                    //         "quotation" => 0,
                    //         "total_lead_deals" => 0,
                    //         "invoice_price" => 0,
                    //         "amount_paid" => 0
                    //     ]);
                    // }

                    $dataChannel[$channelName] = [
                        "supervisor_id" => $supervisor_id,
                        "channel_id" => $channel_id,
                        "name" => $channelName,
                        "channel" => null,
                        "bum" => $bumName,
                        "total_leads" => $total_leads_channel ?? 0,
                        "closed" => $closed_channel ?? 0,
                        "drop_leads" => $drop_channel ?? 0,
                        "cold" => $cold_channel ?? 0,
                        "warm" => $warm_channel ?? 0,
                        "hot" => $hot_channel ?? 0,
                        "quotation" => $quotation_channel ?? 0,
                        "total_lead_deals" => $total_lead_deals_channel ?? 0,
                        "estimated_value" => $estimated_value_channel ?? 0,
                        "invoice_price" => $invoice_price_channel ?? 0,
                        "amount_paid" => $amount_paid_channel ?? 0,
                        "sales" => $dataSales
                    ];

                    $total_leads_bum += $total_leads_channel;
                    $closed_bum += $closed_channel;
                    $drop_bum += $drop_channel;
                    $cold_bum += $cold_channel;
                    $warm_bum += $warm_channel;
                    $hot_bum += $hot_channel;
                    $quotation_bum += $quotation_channel;
                    $total_lead_deals_bum += $total_lead_deals_channel;
                    $estimated_value_bum += $estimated_value_channel;
                    $invoice_price_bum += $invoice_price_channel;
                    $amount_paid_bum += $amount_paid_channel;
                }

                if ($userType == 'sl') {
                    $dataBum[$bumName] = [
                        "supervisor_id" => null,
                        "name" => null,
                        "channel" => null,
                        "bum" => null,
                        "total_leads" => null,
                        "closed" => null,
                        "drop_leads" => null,
                        "cold" => null,
                        "warm" => null,
                        "hot" => null,
                        "quotation" => null,
                        "total_lead_deals" => null,
                        "estimated_value" => null,
                        "invoice_price" => null,
                        "amount_paid" => null,
                    ];
                } else {
                    $dataBum[$bumName] = [
                        "supervisor_id" => $supervisor_id,
                        "name" => $bumName,
                        "channel" => null,
                        "bum" => null,
                        "total_leads" => $total_leads_bum,
                        "closed" => $closed_bum,
                        "drop_leads" => $drop_bum,
                        "cold" => $cold_bum,
                        "warm" => $warm_bum,
                        "hot" => $hot_bum,
                        "quotation" => $quotation_bum,
                        "total_lead_deals" => $total_lead_deals_bum,
                        "estimated_value" => $estimated_value_bum,
                        "invoice_price" => $invoice_price_bum,
                        "amount_paid" => $amount_paid_bum,
                    ];
                }

                $dataBum[$bumName]['channels'] = array_values($dataChannel);

                $allTotalLeads += $total_leads_bum ?? 0;
                $allTotalClosed += $closed_bum ?? 0;
                $allTotalDrop += $drop_bum ?? 0;
                $allTotalCold += $cold_bum ?? 0;
                $allTotalWarm += $warm_bum ?? 0;
                $allTotalHot += $hot_bum ?? 0;
                $allTotalQuotation += $quotation_bum ?? 0;
                $allTotalLeadDeals += $total_lead_deals_bum ?? 0;
                $allTotalEstimatedValue += $estimated_value_bum ?? 0;
                $allTotalInvoicePrice += $invoice_price_bum ?? 0;
                $allTotalAmountPaid += $amount_paid_bum ?? 0;
            }

            return ['data' => array_values($dataBum ?? []), 'total' => [
                'total_leads' => $allTotalLeads,
                'closed' => $allTotalClosed,
                'drop_leads' => $allTotalDrop,
                'cold' => $allTotalCold,
                'warm' => $allTotalWarm,
                'hot' => $allTotalHot,
                'quotation' => $allTotalQuotation,
                'total_lead_deals' => $allTotalLeadDeals,
                'estimated_value' => $allTotalEstimatedValue,
                'invoice_price' => $allTotalInvoicePrice,
                'amount_paid' => $allTotalAmountPaid
            ]];
        }
        // elseif ($userType == 'sl') {
        //     return response()->json($result);
        // } else {
        //     return response()->json($result);
        // }
        return ['data' => $result];
    }

    public function reportLeadsOptimized(int $user_id = null, bool $is_export = false, $company_id = null)
    {
        // define date range
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if ((request()->has('start_date') && request()->start_date != '') && (request()->has('end_date') && request()->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', request()->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', request()->end_date)->endOfDay();
        }

        $user = $is_export ? User::find($user_id) : user();
        $userType = null;

        if ($user->is_director) {
            $userType = 'director';
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'sl';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }
        } else if ($user->is_sales) {
            $userType = 'sales';
        }

        $query = null;

        // start query
        if (in_array($userType, ['director', 'hs', 'bum'])) {
            $query = User::with(['channels' => function ($q) use ($startDate, $endDate) {
                if (request()->channel_id) {
                    $q->where('id', request()->channel_id);
                }

                $q->with(['sales' => function ($q2) use ($startDate, $endDate) {
                    $q2->withCount(['grouped_userLeads as total_leads' => function ($q3) use ($startDate, $endDate) {
                        $q3->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q3->whereHas('activityBrandValues', function ($q4) {
                                $q4->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])->withCount(['leads as drop_leads' => function ($q3) use ($startDate, $endDate) {
                        $q3->where('type', 4);
                        $q3->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q3->whereHas('leadActivities.activityBrandValues', function ($q4) {
                                $q4->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }]);
                    $q2->withCount(['userActivities as all_activity' => function ($q3) use ($startDate, $endDate) {
                        $q3->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q3->whereHas('activityBrandValues', function ($q4) {
                                $q4->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }]);
                    $q2->withCount(['leads as hot_activity' => function ($q3) use ($startDate, $endDate) {
                        $q3->whereHas('leadActivities', function ($q4) use ($startDate, $endDate) {
                            $q4->where('status', 1);
                            $q4->whereCreatedAtRange($startDate, $endDate);
                        });

                        if (request()->product_brand_id) {
                            $q3->whereHas('activityBrandValues', function ($q4) {
                                $q4->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }]);
                    $q2->withCount(['userActivities as warm_activity' => function ($q3) use ($startDate, $endDate) {
                        $q3->where('status', 2);
                        $q3->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q3->whereHas('activityBrandValues', function ($q4) {
                                $q4->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }]);
                    $q2->withCount(['userActivities as cold_activity' => function ($q3) use ($startDate, $endDate) {
                        $q3->where('status', 3);
                        $q3->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q3->whereHas('activityBrandValues', function ($q4) {
                                $q4->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }]);
                    $q2->withCount(['userActivities as closed_activity' => function ($q3) use ($startDate, $endDate) {
                        $q3->where('status', 4);
                        $q3->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q3->whereHas('activityBrandValues', function ($q4) {
                                $q4->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }]);
                    $q2->withSum([
                        'activityBrandValues as estimated_value' => function ($q3) use ($startDate, $endDate) {
                            $q3->whereCreatedAtRange($startDate, $endDate);
                            $q3->whereHas('lead', fn ($q4) => $q4->whereNotIn('type', [4]));

                            if (request()->product_brand_id) {
                                $q3->where('product_brand_id', request()->product_brand_id);
                            }
                        }
                    ], 'estimated_value');
                    $q2->withSum([
                        'activityBrandValues as quotation' => function ($q3) use ($startDate, $endDate) {
                            $q3->whereHas('order', function ($q4) use ($startDate, $endDate) {
                                $q4->whereNotIn('status', [5, 6]);
                                $q4->whereCreatedAtRange($startDate, $endDate);
                            });

                            if (request()->product_brand_id) {
                                $q3->where('product_brand_id', request()->product_brand_id);
                            }
                        }
                    ], 'order_value');
                    $q2->withCount(['userOrders as deal_leads' => function ($q3) use ($startDate, $endDate) {
                        $q3->whereNotIn('status', [5, 6]);
                        $q3->whereIn('payment_status', [2, 3, 4, 6]);
                        $q3->whereDealAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q3->whereHas('activityBrandValues', function ($q4) {
                                $q4->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }]);
                    $q2->withSum([
                        'activityBrandValues as invoice_price' => function ($q3) use ($startDate, $endDate) {
                            $q3->whereHas('order', function ($q4) use ($startDate, $endDate) {
                                $q4->whereNotIn('status', [5, 6]);
                                $q4->whereIn('payment_status', [2, 3, 4, 6]);
                                $q4->whereDealAtRange($startDate, $endDate);
                            });

                            if (request()->product_brand_id) {
                                $q3->where('product_brand_id', request()->product_brand_id);
                            }
                        }
                    ], 'order_value');
                    $q2->withSum([
                        'userOrders as amount_paid' => function ($q3) use ($startDate, $endDate) {
                            $q3->whereNotIn('status', [5, 6]);
                            $q3->whereIn('payment_status', [2, 3, 4, 6]);
                            $q3->whereDealAtRange($startDate, $endDate);
                        }
                    ], 'amount_paid');
                }]);
            }]);

            $query = $query->whereIsSupervisor()->whereSupervisorTypeId('2');

            if ($userType == 'director') {
                if ($company_id == null) {
                    $company_id = request()->company_id ? request()->company_id : $user->company_id;
                }

                $channel_ids = Channel::where('company_id', $company_id)->pluck('id')->all();
                $query = $query->whereIn('channel_id', $channel_ids ?? []);

                if (request()->supervisor_id) $query = $query->where('id', request()->supervisor_id);
            } else if ($userType == 'bum') {
                $query = $query->where('id', $user->id);
            }

            if (request()->channel_id) {
                $query = $query->whereHas('channels', function ($q) {
                    $q->where('id', request()->channel_id);
                });
            }

            if ($userType == 'hs') {
                $query = $query->whereIn('id', $user->getAllChildrenSupervisors(2)->pluck('id')->all());
            }
        } else if ($userType == 'sl') {
            $query = Channel::with(['sales' => function ($q) use ($startDate, $endDate) {
                $q->withCount(['grouped_userActivities as total_leads' => function ($q2) use ($startDate, $endDate) {
                    $q2->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q2->whereHas('activityBrandValues', function ($q3) {
                            $q3->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])->withCount(['leads as drop_leads' => function ($q2) use ($startDate, $endDate) {
                    $q2->where('type', 4);
                    $q2->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q2->whereHas('leadActivities.activityBrandValues', function ($q3) {
                            $q3->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
                $q->withCount(['userActivities as all_activity' => function ($q2) use ($startDate, $endDate) {
                    $q2->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q2->whereHas('activityBrandValues', function ($q3) {
                            $q3->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
                $q->withCount(['leads as hot_activity' => function ($q2) use ($startDate, $endDate) {
                    $q2->whereHas('leadActivities', function ($q3) use ($startDate, $endDate) {
                        $q3->where('status', 1);
                        $q3->whereCreatedAtRange($startDate, $endDate);
                    });

                    if (request()->product_brand_id) {
                        $q2->whereHas('activityBrandValues', function ($q3) {
                            $q3->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
                $q->withCount(['userActivities as warm_activity' => function ($q2) use ($startDate, $endDate) {
                    $q2->where('status', 2);
                    $q2->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q2->whereHas('activityBrandValues', function ($q3) {
                            $q3->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
                $q->withCount(['userActivities as cold_activity' => function ($q2) use ($startDate, $endDate) {
                    $q2->where('status', 3);
                    $q2->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q2->whereHas('activityBrandValues', function ($q3) {
                            $q3->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
                $q->withCount(['userActivities as closed_activity' => function ($q2) use ($startDate, $endDate) {
                    $q2->where('status', 4);
                    $q2->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q2->whereHas('activityBrandValues', function ($q3) {
                            $q3->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
                $q->withSum([
                    'activityBrandValues as estimated_value' => function ($q2) use ($startDate, $endDate) {
                        $q2->whereCreatedAtRange($startDate, $endDate);
                        $q2->whereHas('lead', fn ($q3) => $q3->whereNotIn('type', [4]));

                        if (request()->product_brand_id) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'estimated_value');
                $q->withSum([
                    'activityBrandValues as quotation' => function ($q2) use ($startDate, $endDate) {
                        $q2->whereHas('order', function ($q3) use ($startDate, $endDate) {
                            $q3->whereNotIn('status', [5, 6]);
                            $q3->whereCreatedAtRange($startDate, $endDate);
                        });

                        if (request()->product_brand_id) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'order_value');
                $q->withCount(['userOrders as deal_leads' => function ($q2) use ($startDate, $endDate) {
                    $q2->whereNotIn('status', [5, 6]);
                    $q2->whereIn('payment_status', [2, 3, 4, 6]);
                    $q2->whereDealAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q2->whereHas('activityBrandValues', function ($q3) {
                            $q3->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
                $q->withSum([
                    'activityBrandValues as invoice_price' => function ($q2) use ($startDate, $endDate) {
                        $q2->whereHas('order', function ($q3) use ($startDate, $endDate) {
                            $q3->whereNotIn('status', [5, 6]);
                            $q3->whereIn('payment_status', [2, 3, 4, 6]);
                            $q3->whereDealAtRange($startDate, $endDate);
                        });

                        if (request()->product_brand_id) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'order_value');
                $q->withSum([
                    'userOrders as amount_paid' => function ($q2) use ($startDate, $endDate) {
                        $q2->whereNotIn('status', [5, 6]);
                        $q2->whereIn('payment_status', [2, 3, 4, 6]);
                        $q2->whereDealAtRange($startDate, $endDate);
                    }
                ], 'amount_paid');
            }])->whereIn('id', $user->channels->pluck('id')->all());

            if (request()->channel_id) {
                $query = $query->where('id', request()->channel_id);
            }
        } else if ($userType == 'sales') {
            $query = User::withCount(['grouped_userActivities as total_leads' => function ($q) use ($startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])->withCount(['leads as drop_leads' => function ($q) use ($startDate, $endDate) {
                $q->where('type', 4);
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])->withCount(['userActivities as all_activity' => function ($q2) use ($startDate, $endDate) {
                $q2->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q2->whereHas('activityBrandValues', function ($q3) {
                        $q3->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])->withCount(['leads as hot_activity' => function ($q2) use ($startDate, $endDate) {
                $q2->whereHas('leadActivities', function ($q3) use ($startDate, $endDate) {
                    $q3->where('status', 1);
                    $q3->whereCreatedAtRange($startDate, $endDate);
                });

                $q2->where('status', 1);
                $q2->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q2->whereHas('activityBrandValues', function ($q3) {
                        $q3->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])->withCount(['userActivities as warm_activity' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 2);
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])->withCount(['userActivities as cold_activity' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 3);
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])->withCount(['userActivities as closed_activity' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 4);
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])->withSum([
                'activityBrandValues as estimated_value' => function ($q) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4]));

                    if (request()->product_brand_id) {
                        $q->where('product_brand_id', request()->product_brand_id);
                    }
                }
            ], 'estimated_value')->withSum([
                'activityBrandValues as quotation' => function ($q) use ($startDate, $endDate) {
                    $q->whereHas('order', function ($q2) use ($startDate, $endDate) {
                        $q2->whereNotIn('status', [5, 6]);
                        $q2->whereCreatedAtRange($startDate, $endDate);
                    });

                    if (request()->product_brand_id) {
                        $q->where('product_brand_id', request()->product_brand_id);
                    }
                }
            ], 'order_value')->withCount(['userOrders as deal_leads' => function ($q) use ($startDate, $endDate) {
                $q->whereNotIn('status', [5, 6]);
                $q->whereIn('payment_status', [2, 3, 4, 6]);
                $q->whereDealAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])->withSum([
                'activityBrandValues as invoice_price' => function ($q) use ($startDate, $endDate) {
                    $q->whereHas('order', function ($q2) use ($startDate, $endDate) {
                        $q2->whereNotIn('status', [5, 6]);
                        $q2->whereIn('payment_status', [2, 3, 4, 6]);
                        $q2->whereDealAtRange($startDate, $endDate);
                    });

                    if (request()->product_brand_id) {
                        $q->where('product_brand_id', request()->product_brand_id);
                    }
                }
            ], 'order_value')->withSum([
                'userOrders as amount_paid' => function ($q) use ($startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                }
            ], 'amount_paid')->whereIsSales()->where('id', $user->id);
        }

        $result = $query ? $query->get() : [];

        $dailyActivityTarget = 30;
        // $workingDays = $startDate->diffInDaysFiltered(function (Carbon $date) {
        //     return !$date->isSaturday() && !$date->isSunday();
        // }, $endDate);
        // $activityTarget = ($workingDays + 1) * $dailyActivityTarget;
        $activityTarget = ($startDate->diffInDays($endDate) + 1) * $dailyActivityTarget;

        $all_total_leads = 0;
        $all_drop_leads = 0;
        $all_activity_target = 0;
        $all_all_activity = 0;
        $all_hot_activity = 0;
        $all_warm_activity = 0;
        $all_cold_activity = 0;
        $all_closed_activity = 0;
        $all_estimated_value = 0;
        $all_quotation = 0;
        $all_deal_leads = 0;
        $all_invoice_price = 0;
        $all_amount_paid = 0;

        if (in_array($userType, ['director', 'hs', 'bum', 'sl', 'sales'])) {
            // loop - 1
            foreach ($result as $obj1_key => $obj1) {
                $obj1_total_leads = 0;
                $obj1_drop_leads = 0;
                $obj1_activity_target = 0;
                $obj1_all_activity = 0;
                $obj1_hot_activity = 0;
                $obj1_warm_activity = 0;
                $obj1_cold_activity = 0;
                $obj1_closed_activity = 0;
                $obj1_estimated_value = 0;
                $obj1_quotation = 0;
                $obj1_deal_leads = 0;
                $obj1_invoice_price = 0;
                $obj1_amount_paid = 0;

                if (in_array($userType, ['director', 'hs', 'bum'])) {
                    $nextObj1 = $obj1->channels;
                } else if ($userType == 'sl') {
                    $nextObj1 = $obj1->sales;
                }

                if (isset($nextObj1)) {
                    // loop - 2
                    foreach ($nextObj1 as $obj2) {
                        $obj2_total_leads = 0;
                        $obj2_drop_leads = 0;
                        $obj2_activity_target = 0;
                        $obj2_all_activity = 0;
                        $obj2_hot_activity = 0;
                        $obj2_warm_activity = 0;
                        $obj2_cold_activity = 0;
                        $obj2_closed_activity = 0;
                        $obj2_estimated_value = 0;
                        $obj2_quotation = 0;
                        $obj2_deal_leads = 0;
                        $obj2_invoice_price = 0;
                        $obj2_amount_paid = 0;

                        if (in_array($userType, ['director', 'hs', 'bum'])) {
                            $nextObj2 = $obj2->sales;
                        }

                        if (isset($nextObj2)) {
                            // loop - 3
                            foreach ($nextObj2 as $obj3) {
                                if (in_array($userType, ['director', 'hs', 'bum'])) {
                                    $obj3->activity_target = $activityTarget;
                                    $obj3->target_percentage = ($activityTarget < 1 ? 0 : (($obj3->all_activity / $activityTarget) * 100)) > 100 ? 100 : ($activityTarget < 1 ? 0 : round(($obj3->all_activity / $activityTarget) * 100, 2));

                                    $obj2_total_leads += $obj3->total_leads;
                                    $obj2_drop_leads += $obj3->drop_leads;
                                    $obj2_activity_target += $obj3->activity_target;
                                    $obj2_all_activity += $obj3->all_activity;
                                    $obj2_hot_activity += $obj3->hot_activity;
                                    $obj2_warm_activity += $obj3->warm_activity;
                                    $obj2_cold_activity += $obj3->cold_activity;
                                    $obj2_closed_activity += $obj3->closed_activity;
                                    $obj2_estimated_value += $obj3->estimated_value;
                                    $obj2_quotation += $obj3->quotation;
                                    $obj2_deal_leads += $obj3->deal_leads;
                                    $obj2_invoice_price += $obj3->invoice_price;
                                    $obj2_amount_paid += $obj3->amount_paid;

                                    // calculate total
                                    $all_total_leads += $obj3->total_leads;
                                    $all_drop_leads += $obj3->drop_leads;
                                    $all_activity_target += $obj3->activity_target;
                                    $all_all_activity += $obj3->all_activity;
                                    $all_hot_activity += $obj3->hot_activity;
                                    $all_warm_activity += $obj3->warm_activity;
                                    $all_cold_activity += $obj3->cold_activity;
                                    $all_closed_activity += $obj3->closed_activity;
                                    $all_estimated_value += $obj3->estimated_value;
                                    $all_quotation += $obj3->quotation;
                                    $all_deal_leads += $obj3->deal_leads;
                                    $all_invoice_price += $obj3->invoice_price;
                                    $all_amount_paid += $obj3->amount_paid;
                                }
                            }
                        }

                        if (in_array($userType, ['director', 'hs', 'bum'])) {
                            $obj1_total_leads += $obj2_total_leads;
                            $obj1_drop_leads += $obj2_drop_leads;
                            $obj1_activity_target += $obj2_activity_target;
                            $obj1_all_activity += $obj2_all_activity;
                            $obj1_hot_activity += $obj2_hot_activity;
                            $obj1_warm_activity += $obj2_warm_activity;
                            $obj1_cold_activity += $obj2_cold_activity;
                            $obj1_closed_activity += $obj2_closed_activity;
                            $obj1_estimated_value += $obj2_estimated_value;
                            $obj1_quotation += $obj2_quotation;
                            $obj1_deal_leads += $obj2_deal_leads;
                            $obj1_invoice_price += $obj2_invoice_price;
                            $obj1_amount_paid += $obj2_amount_paid;
                        } else if ($userType == 'sl') {
                            $obj2->activity_target = $activityTarget;
                            $obj2->target_percentage = ($activityTarget < 1 ? 0 : (($obj2->all_activity / $activityTarget) * 100)) > 100 ? 100 : ($activityTarget < 1 ? 0 : round(($obj2->all_activity / $activityTarget) * 100, 2));

                            // calculate total
                            $obj1_total_leads += $obj2->total_leads;
                            $obj1_drop_leads += $obj2->drop_leads;
                            $obj1_activity_target += $obj2->activity_target;
                            $obj1_all_activity += $obj2->all_activity;
                            $obj1_hot_activity += $obj2->hot_activity;
                            $obj1_warm_activity += $obj2->warm_activity;
                            $obj1_cold_activity += $obj2->cold_activity;
                            $obj1_closed_activity += $obj2->closed_activity;
                            $obj1_estimated_value += $obj2->estimated_value;
                            $obj1_quotation += $obj2->quotation;
                            $obj1_deal_leads += $obj2->deal_leads;
                            $obj1_invoice_price += $obj2->invoice_price;
                            $obj1_amount_paid += $obj2->amount_paid;

                            $obj2_total_leads = $obj2->total_leads;
                            $obj2_drop_leads = $obj2->drop_leads;
                            $obj2_all_activity = $obj2->all_activity;
                            $obj2_activity_target = $obj2->activity_target;
                            $obj2_hot_activity = $obj2->hot_activity;
                            $obj2_warm_activity = $obj2->warm_activity;
                            $obj2_cold_activity = $obj2->cold_activity;
                            $obj2_closed_activity = $obj2->closed_activity;
                            $obj2_estimated_value = $obj2->estimated_value;
                            $obj2_quotation = $obj2->quotation;
                            $obj2_deal_leads = $obj2->deal_leads;
                            $obj2_invoice_price = $obj2->invoice_price;
                            $obj2_amount_paid = $obj2->amount_paid;

                            $all_total_leads += $obj2->total_leads;
                            $all_drop_leads += $obj2->drop_leads;
                            $all_activity_target += $obj2->activity_target;
                            $all_all_activity += $obj2->all_activity;
                            $all_hot_activity += $obj2->hot_activity;
                            $all_warm_activity += $obj2->warm_activity;
                            $all_cold_activity += $obj2->cold_activity;
                            $all_closed_activity += $obj2->closed_activity;
                            $all_estimated_value += $obj2->estimated_value;
                            $all_quotation += $obj2->quotation;
                            $all_deal_leads += $obj2->deal_leads;
                            $all_invoice_price += $obj2->invoice_price;
                            $all_amount_paid += $obj2->amount_paid;
                        }

                        // new obj for obj2
                        $obj2['total_leads'] = $obj2_total_leads;
                        $obj2['drop_leads'] = $obj2_drop_leads;
                        $obj2['activity_target'] = $obj2_activity_target;
                        $obj2['all_activity'] = $obj2_all_activity;
                        $obj2['target_percentage'] = ($obj2_activity_target < 1 ? 0 : (($obj2_all_activity / $obj2_activity_target) * 100)) > 100 ? 100 : ($obj2_activity_target < 1 ? 0 : round(($obj2_all_activity / $obj2_activity_target) * 100, 2));
                        $obj2['hot_activity'] = $obj2_hot_activity;
                        $obj2['warm_activity'] = $obj2_warm_activity;
                        $obj2['cold_activity'] = $obj2_cold_activity;
                        $obj2['closed_activity'] = $obj2_closed_activity;
                        $obj2['estimated_value'] = $obj2_estimated_value;
                        $obj2['quotation'] = $obj2_quotation;
                        $obj2['deal_leads'] = $obj2_deal_leads;
                        $obj2['invoice_price'] = $obj2_invoice_price;
                        $obj2['amount_paid'] = $obj2_amount_paid;
                    }
                }

                if ($userType == 'sales') {
                    $obj1->activity_target = $activityTarget;

                    $obj1_total_leads += $obj1->total_leads;
                    $obj1_drop_leads += $obj1->drop_leads;
                    $obj1_activity_target += $obj1->activity_target;
                    $obj1_all_activity += $obj1->all_activity;
                    $obj1_hot_activity += $obj1->hot_activity;
                    $obj1_warm_activity += $obj1->warm_activity;
                    $obj1_cold_activity += $obj1->cold_activity;
                    $obj1_closed_activity += $obj1->closed_activity;
                    $obj1_estimated_value += $obj1->estimated_value;
                    $obj1_quotation += $obj1->quotation;
                    $obj1_deal_leads += $obj1->deal_leads;
                    $obj1_invoice_price += $obj1->invoice_price;
                    $obj1_amount_paid += $obj1->amount_paid;

                    // calculate total
                    $all_total_leads += $obj1->total_leads;
                    $all_drop_leads += $obj1->drop_leads;
                    $all_activity_target += $obj1->activity_target;
                    $all_all_activity += $obj1->all_activity;
                    $all_hot_activity += $obj1->hot_activity;
                    $all_warm_activity += $obj1->warm_activity;
                    $all_cold_activity += $obj1->cold_activity;
                    $all_closed_activity += $obj1->closed_activity;
                    $all_estimated_value += $obj1->estimated_value;
                    $all_quotation += $obj1->quotation;
                    $all_deal_leads += $obj1->deal_leads;
                    $all_invoice_price += $obj1->invoice_price;
                    $all_amount_paid += $obj1->amount_paid;
                }

                // new obj for obj1
                $obj1['total_leads'] = $obj1_total_leads;
                $obj1['drop_leads'] = $obj1_drop_leads;
                $obj1['activity_target'] = $obj1_activity_target;
                $obj1['all_activity'] = $obj1_all_activity;
                $obj1['target_percentage'] = ($obj1_activity_target < 1 ? 0 : (($obj1_all_activity / $obj1_activity_target) * 100)) > 100 ? 100 : ($obj1_activity_target < 1 ? 0 : round(($obj1_all_activity / $obj1_activity_target) * 100, 2));
                $obj1['hot_activity'] = $obj1_hot_activity;
                $obj1['warm_activity'] = $obj1_warm_activity;
                $obj1['cold_activity'] = $obj1_cold_activity;
                $obj1['closed_activity'] = $obj1_closed_activity;
                $obj1['estimated_value'] = $obj1_estimated_value;
                $obj1['quotation'] = $obj1_quotation;
                $obj1['deal_leads'] = $obj1_deal_leads;
                $obj1['invoice_price'] = $obj1_invoice_price;
                $obj1['amount_paid'] = $obj1_amount_paid;
            }
        }

        return response()->json([
            'data' => $result,
            'total' => [
                'total_leads' => $all_total_leads,
                'drop_leads' => $all_drop_leads,
                'activity_target' => $all_activity_target,
                'all_activity' => $all_all_activity,
                'hot_activity' => $all_hot_activity,
                'warm_activity' => $all_warm_activity,
                'cold_activity' => $all_cold_activity,
                'closed_activity' => $all_closed_activity,
                'estimated_value' => $all_estimated_value,
                'quotation' => $all_quotation,
                'deal_leads' => $all_deal_leads,
                'invoice_price' => $all_invoice_price,
                'amount_paid' => $all_amount_paid,
            ]
        ]);
    }
}
