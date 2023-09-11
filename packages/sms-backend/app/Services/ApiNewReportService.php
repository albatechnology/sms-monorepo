<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\TargetType;
use App\Enums\UserType;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Channel;
use App\Models\Lead;
use App\Models\Order;
use App\Models\ProductBrand;
use Illuminate\Http\Request;

class ApiNewReportService
{
    const USER_COLUMNS = "id, name, type, channel_id";
    const CHANNEL_COLUMNS = "id, name, subscribtion_user_id";
    const SUPERVISOR_TYPE = ['store_leader', 'bum', 'hs'];

    protected static function getDates($start_date, $end_date)
    {
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        $startTargetDate = Carbon::createFromFormat('Y-m-d', $start_date)->startOfMonth();
        $endTargetDate = Carbon::createFromFormat('Y-m-d', $end_date)->endOfMonth();

        $startDate = Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $end_date)->endOfDay();

        $sd = date('Y-m-d', strtotime($start_date));
        $ed = date('Y-m-d', strtotime($end_date));

        if ((date('m', strtotime($sd)) === date('m', strtotime($ed))) && $sd === date('Y-m-01', strtotime($sd)) && $ed === date('Y-m-t', strtotime($sd))) {
            $startDateCompare = Carbon::createFromFormat('Y-m-d', $start_date)->subMonth()->startOfMonth();
            $endDateCompare = Carbon::createFromFormat('Y-m-d', $start_date)->subMonth()->endOfMonth();
        } else {
            $diff = $startDate->diffInDays($endDate);
            $startDateCompare = Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay()->subDays($diff + 1);
            $endDateCompare = Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay()->subDay();
        }

        return [
            'startTargetDate' => $startTargetDate,
            'endTargetDate' => $endTargetDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startDateCompare' => $startDateCompare,
            'endDateCompare' => $endDateCompare
        ];
    }

    public static function index(Request $request)
    {
        $subDeals = self::subDeals($request);
        $subNewLeads = self::subNewLeads($request);
        $subActiveLeads = self::subActiveLeads($request);
        $subFollowUp = self::subFollowUp($request);
        $subLeadStatus = self::subLeadStatus($request);
        // $subFollowUpMethod = self::subFollowUpMethod($request);
        $subQuotations = self::subQuotation($request);
        $subEstimations = self::subEstimations($request);

        $dataFollowUp['follow_up'] = array_merge(
            $subFollowUp['data']['follow_up'],
            $subLeadStatus['data']['follow_up'],
        );

        $data = array_merge(
            $subNewLeads['data'],
            $subActiveLeads['data'],
            $dataFollowUp,
            $subEstimations['data'],
            $subQuotations['data'],
            $subDeals['data'],
            $subDeals['info_date'],
        );

        return $data;
    }

    public static function subDeals(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        $infoDate = [
            'original_date' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'compare_date' => [
                'start' => $startDateCompare,
                'end' => $endDateCompare,
            ]
        ];

        $target_deals = 0;

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        $subscribtionUserId = $request->subscribtion_user_id ?? $user->subscribtion_user_id;
        $channelId = $request->channel_id ?? null;
        $targetType = TargetType::DEALS_ORDER_PRICE;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';

            $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $subscribtionUserId)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }

            $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            $userType = 'sales';
            $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
            if ($channelId) {
                $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
            }
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount([
                    'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ])
                ->withSum([
                    'channelOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'channelOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereDeal($startDateCompare, $endDateCompare);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

            $result = $query->first();

            $data = [
                'deals' => [
                    'value' => potongPPN($result->total_deals ?? 0),
                    'compare' => potongPPN($result->compare_total_deals),
                    'total_transaction' => $result->total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                ]
            ];
        } else if (in_array($userType, ['director'])) {
            // $query = User::selectRaw(self::USER_COLUMNS)
            //     ->where('company_id', $companyId)
            //     ->where('type', 2)
            //     ->withCount([
            //         'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ])
            //     ->withSum([
            //         'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ], 'total_price')
            //     ->withSum([
            //         'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //             $q->whereDeal($startDateCompare, $endDateCompare);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ], 'total_price')
            //     ->withSum([
            //         'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate)
            //                 ->whereNotNull('interior_design_id');
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ], 'total_price')
            //     ->withCount([
            //         'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate)
            //                 ->whereNotNull('interior_design_id');
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ]);

            // $result = $query->get();

            // $data = [];

            // $summary_deals = 0;
            // $summary_total_deals_transaction = 0;
            // $summary_compare_deals = 0;
            // $summary_interior_design = 0;
            // $summary_interior_design_total_transaction = 0;
            // foreach ($result as $sales) {
            //     $summary_deals += (int)$sales->total_deals ?? 0;
            //     $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
            //     $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
            //     $summary_interior_design += (int)$sales->interior_design ?? 0;
            //     $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
            // }

            $summary_deals = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $subscribtionUserId)->whereDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $summary_compare_deals = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $subscribtionUserId)->whereDeal($startDateCompare, $endDateCompare)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $summary_total_deals_transaction = Order::where('subscribtion_user_id', $subscribtionUserId)->whereDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->count() ?? 0;

            $data = [
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    'compare' => potongPPN($summary_compare_deals),
                    'total_transaction' => $summary_total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                ]
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
                ->withCount([
                    'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'channelOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'channelOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereDeal($startDateCompare, $endDateCompare);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

            // $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
            //     ->with('leadUsers', function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
            //         $q->has('orders')
            //             ->withCount([
            //                 'orders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //                     $q->whereDeal($startDate, $endDate);
            //                     if ($channelId) $q->where('channel_id', $channelId);

            //                     if ($productBrandId = request()->product_brand_id) {
            //                         $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
            //                     }
            //                 }
            //             ])
            //             ->withSum([
            //                 'orders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
            //                     $q->whereDeal($startDate, $endDate);
            //                     if ($channelId) $q->where('channel_id', $channelId);

            //                     if ($productBrandId = request()->product_brand_id) {
            //                         $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
            //                     }
            //                 }
            //             ], 'total_price')
            //             ->withSum([
            //                 'orders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //                     $q->whereDeal($startDateCompare, $endDateCompare);
            //                     if ($channelId) $q->where('channel_id', $channelId);

            //                     if ($productBrandId = request()->product_brand_id) {
            //                         $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
            //                     }
            //                 }
            //             ], 'total_price');
            //     });

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            if ($request->name) {
                $query = $query->where('name', 'like', '%' . $request->name . '%');
            }

            $result = $query->get();

            $summary_deals = $result->sum('total_deals') ?? 0;
            $summary_total_deals_transaction = $result->sum('total_deals_transaction') ?? 0;
            $summary_compare_deals = $result->sum('compare_total_deals') ?? 0;

            // foreach ($result as $channel) {
            //     $summary_deals += $channel->total_deals ?? 0;
            //     $summary_total_deals_transaction += $channel->total_deals_transaction ?? 0;
            //     $summary_compare_deals += $channel->compare_total_deals ?? 0;
            //     $summary_interior_design += $channel->interior_design ?? 0;
            //     $summary_interior_design_total_transaction += $channel->interior_design_total_transaction ?? 0;
            // }

            $data = [
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    'compare' => potongPPN($summary_compare_deals),
                    'total_transaction' => $summary_total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                ]
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ])
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as compare_total_deals' => function ($q) use ($user, $channelId, $startDateCompare, $endDateCompare) {
                        $q->whereDeal($startDateCompare, $endDateCompare);
                        $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

            $result = $query->first();

            $data = [
                'deals' => [
                    'value' => potongPPN($result->total_deals ?? 0),
                    'compare' => potongPPN($result->compare_total_deals),
                    'total_transaction' => $result->total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                ]
            ];
        }

        return [
            'data' => $data,
            'info_date' => $infoDate,
        ];
    }

    public static function subDealsMtd(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];
        }

        $target_deals = 0;

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $companyId)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            $userType = 'sales';

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
            if ($channelId) {
                // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
            }
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withSum([
                    'channelOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

            $result = $query->first();

            $data = [
                'deals' => [
                    'value' => potongPPN($result->total_deals ?? 0),
                    'target_deals' => (int)$target_deals ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $summary_deals = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $data = [
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    'target_deals' => (int)$target_deals ?? 0,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->with('leadUsers', function ($q) use ($channelId, $startDate, $endDate) {
                    $q->has('orders')
                        ->withSum([
                            'orders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);

                                if ($productBrandId = request()->product_brand_id) {
                                    $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                                }
                            }
                        ], 'total_price');
                });

            if ($request->name) {
                $query = $query->where('name', 'like', '%' . $request->name . '%');
            }

            $result = $query->first();

            $summary_deals = $result->leadUsers?->sum('total_deals') ?? 0;

            $data = [
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    'target_deals' => (int)$target_deals ?? 0,
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

            $result = $query->first();

            $data = [
                'deals' => [
                    'value' => potongPPN($result->total_deals ?? 0),
                    'target_deals' => (int)$target_deals ?? 0,
                ]
            ];
        }

        return [
            'data' => $data,
        ];
    }

    public static function subDealsByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        $infoDate = [
            'original_date' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'compare_date' => [
                'start' => $startDateCompare,
                'end' => $endDateCompare,
            ]
        ];

        $target_deals = 0;
        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            // $userType = 'director';

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $companyId)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            // if ($user->supervisor_type_id == 1) {
            //     $userType = 'store_leader';
            // } else if ($user->supervisor_type_id == 2) {
            //     $userType = 'bum';
            // } else if ($user->supervisor_type_id == 3) {
            //     $userType = 'hs';
            // }

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            // $userType = 'sales';

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        // if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
        if ($channelId) {
            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount([
                    'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ])
                ->withSum([
                    'channelOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'channelOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereDeal($startDateCompare, $endDateCompare);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'channelOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->whereNotNull('interior_design_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withCount([
                    'channelOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->whereNotNull('interior_design_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ])
                ->withSum([
                    'channelOrders as exhibition' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->whereNotNull('exhibition_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withCount([
                    'channelOrders as exhibition_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->whereNotNull('exhibition_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ]);

            $result = $query->first();

            $data = [
                'deals' => [
                    'value' => potongPPN($result->total_deals ?? 0),
                    'compare' => potongPPN($result->compare_total_deals),
                    'total_transaction' => $result->total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                ],
                'interior_design' => [
                    'value' => potongPPN($result->interior_design),
                    'total_transaction' => (int)$result->interior_design_total_transaction ?? 0,
                ],
                'exhibition' => [
                    'value' => potongPPN($result->exhibition),
                    'total_transaction' => (int)$result->exhibition_total_transaction ?? 0,
                ],
                'retail' => [
                    'value' => potongPPN($result->total_deals - $result->interior_design - $result->exhibition),
                    'total_transaction' => (int)($result->total_deals_transaction - $result->interior_design_total_transaction - $result->exhibition_total_transaction) ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            // $query = User::selectRaw(self::USER_COLUMNS)
            //     ->where('company_id', $companyId)
            //     ->where('type', 2)
            //     ->withCount([
            //         'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ])
            //     ->withSum([
            //         'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ], 'total_price')
            //     ->withSum([
            //         'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //             $q->whereDeal($startDateCompare, $endDateCompare);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ], 'total_price')
            //     ->withSum([
            //         'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate)
            //                 ->whereNotNull('interior_design_id');
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ], 'total_price')
            //     ->withCount([
            //         'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate)
            //                 ->whereNotNull('interior_design_id');
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ]);

            // $result = $query->get();

            // $data = [];

            // $summary_deals = 0;
            // $summary_total_deals_transaction = 0;
            // $summary_compare_deals = 0;
            // $summary_interior_design = 0;
            // $summary_interior_design_total_transaction = 0;
            // foreach ($result as $sales) {
            //     $summary_deals += (int)$sales->total_deals ?? 0;
            //     $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
            //     $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
            //     $summary_interior_design += (int)$sales->interior_design ?? 0;
            //     $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
            // }

            $summary_deals = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $summary_compare_deals = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDateCompare, $endDateCompare)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $summary_total_deals_transaction = Order::where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->count() ?? 0;

            // $summary_interior_design = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->whereNotNull('interior_design_id')->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            // $summary_interior_design_total_transaction = Order::where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->whereNotNull('interior_design_id')->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->count() ?? 0;

            // $summary_exhibition = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->whereNotNull('exhibition_id')->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            // $summary_exhibition_total_transaction = Order::where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->whereNotNull('exhibition_id')->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->count() ?? 0;

            $data = [
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    'compare' => potongPPN($summary_compare_deals),
                    'total_transaction' => $summary_total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                ],
                // 'interior_design' => [
                //     'value' => potongPPN($summary_interior_design),
                //     'total_transaction' => $summary_interior_design_total_transaction,
                // ],
                // 'exhibition' => [
                //     'value' => potongPPN($summary_exhibition),
                //     'total_transaction' => $summary_exhibition_total_transaction,
                // ],
                // 'retail' => [
                //     'value' => potongPPN($summary_deals - $summary_interior_design - $summary_exhibition),
                //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction - $summary_exhibition_total_transaction,
                // ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            // ->withCount([
            //     'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ])
            // ->withSum([
            //     'channelOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price')
            // ->withSum([
            //     'channelOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         $q->whereDeal($startDateCompare, $endDateCompare);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price')
            // ->withSum([
            //     'channelOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate)
            //             ->whereNotNull('interior_design_id');
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price')
            // ->withCount([
            //     'channelOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate)
            //             ->whereNotNull('interior_design_id');
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ]);

            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->with('leadUsers', function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $q->has('orders')
                        ->withCount([
                            'orders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ])
                        ->withSum([
                            'orders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withSum([
                            'orders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                                $q->whereDeal($startDateCompare, $endDateCompare);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withSum([
                            'orders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate)
                                    ->whereNotNull('interior_design_id');
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withCount([
                            'orders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate)
                                    ->whereNotNull('interior_design_id');
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withSum([
                            'orders as exhibition' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate)
                                    ->whereNotNull('exhibition_id');
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withCount([
                            'orders as exhibition_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate)
                                    ->whereNotNull('exhibition_id');
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price');
                });

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            $result = $query->first();

            $summary_deals = $result->leadUsers?->sum('total_deals') ?? 0;
            $summary_total_deals_transaction = $result->leadUsers?->sum('total_deals_transaction') ?? 0;
            $summary_compare_deals = $result->leadUsers?->sum('compare_total_deals') ?? 0;
            $summary_interior_design = $result->leadUsers?->sum('interior_design') ?? 0;
            $summary_interior_design_total_transaction = $result->leadUsers?->sum('interior_design_total_transaction') ?? 0;
            $summary_exhibition = $result->leadUsers?->sum('exhibition') ?? 0;
            $summary_exhibition_total_transaction = $result->leadUsers?->sum('exhibition_total_transaction') ?? 0;

            // foreach ($result as $channel) {
            //     $summary_deals += $channel->total_deals ?? 0;
            //     $summary_total_deals_transaction += $channel->total_deals_transaction ?? 0;
            //     $summary_compare_deals += $channel->compare_total_deals ?? 0;
            //     $summary_interior_design += $channel->interior_design ?? 0;
            //     $summary_interior_design_total_transaction += $channel->interior_design_total_transaction ?? 0;
            // }

            $data = [
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    'compare' => potongPPN($summary_compare_deals),
                    'total_transaction' => $summary_total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                ],
                'interior_design' => [
                    'value' => potongPPN($summary_interior_design),
                    'total_transaction' => $summary_interior_design_total_transaction,
                ],
                'exhibition' => [
                    'value' => potongPPN($summary_exhibition),
                    'total_transaction' => $summary_exhibition_total_transaction,
                ],
                'retail' => [
                    'value' => potongPPN($summary_deals - $summary_interior_design - $summary_exhibition),
                    'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction - $summary_exhibition_total_transaction,
                ],
            ];
        } else {
            // else sales
            $userLoggedIn = user();
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ])
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as compare_total_deals' => function ($q) use ($userLoggedIn, $user, $channelId, $startDateCompare, $endDateCompare) {
                        $q->whereDeal($startDateCompare, $endDateCompare);
                        if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as interior_design' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->whereNotNull('interior_design_id');
                        if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withCount([
                    'userOrders as interior_design_total_transaction' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->whereNotNull('interior_design_id');
                        if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ])
                ->withSum([
                    'userOrders as exhibition' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->whereNotNull('exhibition_id');
                        if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withCount([
                    'userOrders as exhibition_total_transaction' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->whereNotNull('exhibition_id');
                        if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ]);

            $result = $query->first();

            $data = [
                'deals' => [
                    'value' => potongPPN($result->total_deals ?? 0),
                    'compare' => potongPPN($result->compare_total_deals),
                    'total_transaction' => $result->total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                ],
                'interior_design' => [
                    'value' => potongPPN($result->interior_design),
                    'total_transaction' => (int)$result->interior_design_total_transaction ?? 0,
                ],
                'exhibition' => [
                    'value' => potongPPN($result->exhibition),
                    'total_transaction' => (int)$result->exhibition_total_transaction ?? 0,
                ],
                'retail' => [
                    'value' => potongPPN($result->total_deals - $result->interior_design - $result->exhibition),
                    'total_transaction' => (int)($result->total_deals_transaction - $result->interior_design_total_transaction - $result->exhibition_total_transaction) ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            'info_date' => $infoDate,
        ];
    }

    public static function subReportDealsByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        $infoDate = [
            'original_date' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'compare_date' => [
                'start' => $startDateCompare,
                'end' => $endDateCompare,
            ]
        ];
        $target_deals = 0;
        $target_deals_ytd = 0;
        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            // $userType = 'director';

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $companyId)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_deals_ytd = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $companyId)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate->copy()->startOfYear())->whereDate('reports.start_date', '<=', $startTargetDate->copy()->endOfYear())->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            // if ($user->supervisor_type_id == 1) {
            //     $userType = 'store_leader';
            // } else if ($user->supervisor_type_id == 2) {
            //     $userType = 'bum';
            // } else if ($user->supervisor_type_id == 3) {
            //     $userType = 'hs';
            // }

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_deals_ytd = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate->copy()->startOfYear())->whereDate('reports.start_date', '<=', $startTargetDate->copy()->endOfYear())->first()?->target ?? 0;
        } else if ($user->is_sales) {
            // $userType = 'sales';

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_deals_ytd = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate->copy()->startOfYear())->whereDate('reports.start_date', '<=', $startTargetDate->copy()->endOfYear())->first()?->target ?? 0;
        }

        // if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
        if ($channelId) {
            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_deals_ytd = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startTargetDate->copy()->startOfYear())->whereDate('reports.start_date', '<=', $startTargetDate->copy()->endOfYear())->first()?->target ?? 0;
        }
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withSum([
                    'channelOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');
            // ->withCount([
            //     'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ])
            // ->withSum([
            //     'channelOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         $q->whereDeal($startDateCompare, $endDateCompare);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price');

            $result = $query->first();

            $data = [
                'deals' => [
                    'value' => potongPPN($result->total_deals ?? 0),
                    // 'compare' => potongPPN($result->compare_total_deals),
                    // 'total_transaction' => $result->total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                    'target_deals_ytd' => (int)$target_deals_ytd ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            // $query = User::selectRaw(self::USER_COLUMNS)
            //     ->where('company_id', $companyId)
            //     ->where('type', 2)
            //     ->withCount([
            //         'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ])
            //     ->withSum([
            //         'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereDeal($startDate, $endDate);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ], 'total_price')
            //     ->withSum([
            //         'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //             $q->whereDeal($startDateCompare, $endDateCompare);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //         }
            //     ], 'total_price');

            // $result = $query->get();

            // $data = [];

            // $summary_deals = 0;
            // $summary_total_deals_transaction = 0;
            // $summary_compare_deals = 0;
            // foreach ($result as $sales) {
            //     $summary_deals += (int)$sales->total_deals ?? 0;
            //     $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
            //     $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
            // }

            $summary_deals = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            // $summary_compare_deals = Order::selectRaw('SUM(total_price) as total_price')->where('company_id', $companyId)->whereDeal($startDateCompare, $endDateCompare)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            // $summary_total_deals_transaction = Order::where('company_id', $companyId)->whereDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->count() ?? 0;

            $data = [
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    // 'compare' => potongPPN($summary_compare_deals),
                    // 'total_transaction' => $summary_total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                    'target_deals_ytd' => (int)$target_deals_ytd ?? 0,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            // ->withCount([
            //     'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ])
            // ->withSum([
            //     'channelOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price')
            // ->withSum([
            //     'channelOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         $q->whereDeal($startDateCompare, $endDateCompare);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price');

            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->with('leadUsers', function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $q->has('orders')
                        ->withSum([
                            'orders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price');
                    // ->withCount([
                    //     'orders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                    //         $q->whereDeal($startDate, $endDate);
                    //         if ($channelId) $q->where('channel_id', $channelId);
                    //     }
                    // ])
                    // ->withSum([
                    //     'orders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    //         $q->whereDeal($startDateCompare, $endDateCompare);
                    //         if ($channelId) $q->where('channel_id', $channelId);
                    //     }
                    // ], 'total_price');
                });

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            $result = $query->first();

            $summary_deals = $result->leadUsers?->sum('total_deals') ?? 0;
            // $summary_total_deals_transaction = $result->leadUsers?->sum('total_deals_transaction') ?? 0;
            // $summary_compare_deals = $result->leadUsers?->sum('compare_total_deals') ?? 0;

            // foreach ($result as $channel) {
            //     $summary_deals += $channel->total_deals ?? 0;
            //     $summary_total_deals_transaction += $channel->total_deals_transaction ?? 0;
            //     $summary_compare_deals += $channel->compare_total_deals ?? 0;
            // }

            $data = [
                'id' => $result->id,
                'name' => $result->name,
                'type' => $result->type?->description ?? $result->type ?? null,
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    // 'compare' => potongPPN($summary_compare_deals),
                    // 'total_transaction' => $summary_total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                    'target_deals_ytd' => (int)$target_deals_ytd ?? 0,
                ],
            ];
        } else {
            // else sales
            $userLoggedIn = user();
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');
            // ->withCount([
            //     'userOrders as total_deals_transaction' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate);
            //         if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ])
            // ->withSum([
            //     'userOrders as compare_total_deals' => function ($q) use ($userLoggedIn, $user, $channelId, $startDateCompare, $endDateCompare) {
            //         $q->whereDeal($startDateCompare, $endDateCompare);
            //         if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price');

            $result = $query->first();

            $data = [
                'id' => $result->id,
                'name' => $result->name,
                'type' => $result->type?->description ?? $result->type ?? null,
                'deals' => [
                    'value' => potongPPN($result->total_deals ?? 0),
                    // 'compare' => potongPPN($result->compare_total_deals),
                    // 'total_transaction' => $result->total_deals_transaction,
                    'target_deals' => (int)$target_deals ?? 0,
                    'target_deals_ytd' => (int)$target_deals_ytd ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            'info_date' => $infoDate,
        ];
    }

    public static function subNewLeads(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        // $infoDate = [
        //     'original_date' => [
        //         'start' => $startDate,
        //         'end' => $endDate,
        //     ],
        //     'compare_date' => [
        //         'start' => $startDateCompare,
        //         'end' => $endDateCompare,
        //     ]
        // ];

        $target_leads = 0;

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        $subscribtionUserId = $request->subscribtion_user_id ?? $user->subscribtion_user_id;
        $channelId = $request->channel_id ?? null;
        $targetType = TargetType::NEW_LEAD_COUNT;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';

            $target_leads = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $subscribtionUserId)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }

            $target_leads = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            $userType = 'sales';
            $target_leads = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
            if ($channelId) {
                $target_leads = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
            }
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount(['channelLeads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelLeads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'new_leads' => [
                    'value' => (int)$result->total_leads ?? 0,
                    'compare' => (int)$result->compare_total_leads ?? 0,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $subscribtionUserId)
                ->where('type', 2)
                ->withCount(['leads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->get();

            $data = [];

            $summary_new_leads = 0;
            $summary_compare_new_leads = 0;
            foreach ($result as $sales) {
                $summary_new_leads += (int)$sales->total_leads ?? 0;
                $summary_compare_new_leads += (int)$sales->compare_total_leads ?? 0;
            }

            $data = [
                'new_leads' => [
                    'value' => $summary_new_leads,
                    'compare' => $summary_compare_new_leads,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $channelName = $request->input('name');

            $total_leads = Lead::selectRaw("count(distinct(customer_id)) as total_leads")
                // ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereIn('channel_id', $user->channels->pluck('id'))
                ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like', '%' . $channelName . '%')))
                ->first()->total_leads ?? 0;

            $compare_total_leads = Lead::selectRaw("count(distinct(customer_id)) as compare_total_leads")
                // ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereIn('channel_id', $user->channels->pluck('id'))
                ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like', '%' . $channelName . '%')))
                ->first()->compare_total_leads ?? 0;

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withCount(['channelLeads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->select(DB::raw('count(distinct(customer_id))'))
            //             ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelLeads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         $q->select(DB::raw('count(distinct(customer_id))'))
            //             ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $summary_new_leads = $total_leads ?? 0;
            $summary_compare_new_leads = $compare_total_leads ?? 0;

            // foreach ($result as $channel) {
            //     $summary_new_leads += $channel->total_leads ?? 0;
            //     $summary_compare_new_leads += $channel->compare_total_leads ?? 0;
            // }

            $data = [
                'new_leads' => [
                    'value' => $summary_new_leads,
                    'compare' => $summary_compare_new_leads,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount(['leads as total_leads' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));
                    $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_total_leads' => function ($q) use ($user, $channelId, $startDateCompare, $endDateCompare) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));
                    $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'new_leads' => [
                    'value' => (int)$result->total_leads ?? 0,
                    'compare' => (int)$result->compare_total_leads ?? 0,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subNewLeadsByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        // $target_deals = 0;

        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        $target_leads = 0;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            // $userType = 'director';

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'company')->where('model_id', $companyId)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            // if ($user->supervisor_type_id == 1) {
            //     $userType = 'store_leader';
            // } else if ($user->supervisor_type_id == 2) {
            //     $userType = 'bum';
            // } else if ($user->supervisor_type_id == 3) {
            //     $userType = 'hs';
            // }

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            // $userType = 'sales';

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        // if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
        if ($channelId) {
            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'channel')->where('model_id', $channelId)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount(['channelLeads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelLeads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'new_leads' => [
                    'value' => (int)$result->total_leads ?? 0,
                    'compare' => (int)$result->compare_total_leads ?? 0,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2)
                ->withCount(['leads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->get();

            $data = [];

            $summary_new_leads = 0;
            $summary_compare_new_leads = 0;
            foreach ($result as $sales) {
                $summary_new_leads += (int)$sales->total_leads ?? 0;
                $summary_compare_new_leads += (int)$sales->compare_total_leads ?? 0;
            }

            $data = [
                'new_leads' => [
                    'value' => $summary_new_leads,
                    'compare' => $summary_compare_new_leads,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $total_leads = Lead::selectRaw("count(distinct(customer_id)) as total_leads")
                ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->first()->total_leads ?? 0;

            $compare_total_leads = Lead::selectRaw("count(distinct(customer_id)) as compare_total_leads")
                ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->first()->compare_total_leads ?? 0;

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withCount(['channelLeads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->select(DB::raw('count(distinct(customer_id))'))
            //             ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelLeads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         $q->select(DB::raw('count(distinct(customer_id))'))
            //             ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $summary_new_leads = $total_leads ?? 0;
            $summary_compare_new_leads = $compare_total_leads ?? 0;

            // foreach ($result as $channel) {
            //     $summary_new_leads += $channel->total_leads ?? 0;
            //     $summary_compare_new_leads += $channel->compare_total_leads ?? 0;
            // }

            $data = [
                'new_leads' => [
                    'value' => $summary_new_leads,
                    'compare' => $summary_compare_new_leads,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else {
            // else sales
            $userLoggedIn = user();
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount(['leads as total_leads' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_total_leads' => function ($q) use ($userLoggedIn, $user, $channelId, $startDateCompare, $endDateCompare) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));

                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'new_leads' => [
                    'value' => (int)$result->total_leads ?? 0,
                    'compare' => (int)$result->compare_total_leads ?? 0,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subTotalLeads(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        $userType = null;
        $target_leads = 0;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';

            // $target_leads = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $companyId)->where('targets.type', 7)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'company')->where('model_id', $companyId)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }

            // $target_leads = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 7)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            $userType = 'sales';

            // $target_leads = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 7)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
            if ($channelId) {
                // $target_leads = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', 7)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;

                // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'channel')->where('model_id', $channelId)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
            }
        }

        if ($userType == 'store') {
            $totalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $compareTotalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereCreatedAtRange($startDateCompare, $endDateCompare)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $data = [
                'total_leads' => [
                    'value' => $totalActivities->count(),
                    'compare' => $compareTotalActivities->count(),
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $totalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereHas('channel', fn ($q) => $q->where('subscribtion_user_id', $user->subscribtion_user_id))
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $compareTotalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereHas('channel', fn ($q) => $q->where('subscribtion_user_id', $user->subscribtion_user_id))
                ->whereCreatedAtRange($startDateCompare, $endDateCompare)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $data = [
                'total_leads' => [
                    'value' => $totalActivities->count(),
                    'compare' => $compareTotalActivities->count(),
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $channelName = $request->input('name');

            $totalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereHas('lead', fn ($q) => $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id)))
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like', '%' . $channelName . '%')))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $compareTotalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereHas('lead', fn ($q) => $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id)))
                ->whereCreatedAtRange($startDateCompare, $endDateCompare)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like', '%' . $channelName . '%')))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $data = [
                'total_leads' => [
                    'value' => $totalActivities->count(),
                    'compare' => $compareTotalActivities->count(),
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else {
            // else sales
            $totalActivities = Activity::selectRaw('count("id") as total_activity')
                ->where('user_id', $user->id)
                ->whereCreatedAtRange($startDate, $endDate);
            if ($channelId) {
                $totalActivities->where('channel_id', $channelId);
            } else {
                $totalActivities->where('channel_id', $user->channel_id);
            }
            $totalActivities = $totalActivities->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $compareTotalActivities = Activity::selectRaw('count("id") as total_activity')
                ->where('user_id', $user->id)
                ->whereCreatedAtRange($startDateCompare, $endDateCompare);
            if ($channelId) {
                $compareTotalActivities->where('channel_id', $channelId);
            } else {
                $compareTotalActivities->where('channel_id', $user->channel_id);
            }
            $compareTotalActivities = $compareTotalActivities->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $data = [
                'total_leads' => [
                    'value' => (int)$totalActivities->count() ?? 0,
                    'compare' => (int)$compareTotalActivities->count() ?? 0,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subTotalLeadsByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        // $target_deals = 0;

        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        $target_leads = 0;

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            // $userType = 'director';

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'company')->where('model_id', $companyId)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            // if ($user->supervisor_type_id == 1) {
            //     $userType = 'store_leader';
            // } else if ($user->supervisor_type_id == 2) {
            //     $userType = 'bum';
            // } else if ($user->supervisor_type_id == 3) {
            //     $userType = 'hs';
            // }

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            // $userType = 'sales';

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        // if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
        if ($channelId) {
            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'channel')->where('model_id', $channelId)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }
        // }

        if ($userType == 'store') {
            $totalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $compareTotalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $data = [
                'total_leads' => [
                    'value' => $totalActivities->count(),
                    'compare' => $compareTotalActivities->count(),
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $totalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereHas('channel', fn ($q) => $q->where('subscribtion_user_id', $user->subscribtion_user_id))
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $compareTotalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereHas('channel', fn ($q) => $q->where('subscribtion_user_id', $user->subscribtion_user_id))
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $data = [
                'total_leads' => [
                    'value' => $totalActivities->count(),
                    'compare' => $compareTotalActivities->count(),
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $totalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereHas('lead', fn ($q) => $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id)))
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $compareTotalActivities = Activity::selectRaw('count("id") as total_activity')
                ->whereHas('lead', fn ($q) => $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id)))
                ->whereCreatedAtRange($startDate, $endDate)
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $data = [
                'total_leads' => [
                    'value' => $totalActivities->count(),
                    'compare' => $compareTotalActivities->count(),
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        } else {
            // else sales
            $userLoggedIn = user();

            $totalActivities = Activity::selectRaw('count("id") as total_activity')
                ->where('user_id', $user->id)
                ->whereCreatedAtRange($startDate, $endDate);
            if ($userLoggedIn->is_sales) $totalActivities->where('channel_id', $user->channel_id);

            if ($channelId) $totalActivities->where('channel_id', $channelId);
            $totalActivities = $totalActivities->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $compareTotalActivities = Activity::selectRaw('count("id") as total_activity')
                ->where('user_id', $user->id)
                ->whereCreatedAtRange($startDateCompare, $endDateCompare);
            if ($userLoggedIn->is_sales) $compareTotalActivities->where('channel_id', $user->channel_id);

            if ($channelId) $compareTotalActivities->where('channel_id', $channelId);
            $compareTotalActivities = $compareTotalActivities->groupByLeadAndDate()
                ->orderBy('id')
                ->get();

            $data = [
                'total_leads' => [
                    'value' => (int)$totalActivities->count() ?? 0,
                    'compare' => (int)$compareTotalActivities->count() ?? 0,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subActiveLeads(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        // $infoDate = [
        //     'original_date' => [
        //         'start' => $startDate,
        //         'end' => $endDate,
        //     ],
        //     'compare_date' => [
        //         'start' => $startDateCompare,
        //         'end' => $endDateCompare,
        //     ]
        // ];

        $target_deals = 0;

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }
        } else if ($user->is_sales) {
            $userType = 'sales';
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount(['channelLeads as active_leads' => function ($q) use ($startDate, $endDate) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelLeads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'active_leads' => [
                    'value' => (int)$result->active_leads ?? 0,
                    'compare' => (int)$result->compare_active_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2)
                ->withCount(['leads as active_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_active_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->get();

            $data = [];

            $summary_active_leads = 0;
            $summary_compare_active_leads = 0;
            foreach ($result as $sales) {
                $summary_active_leads += (int)$sales->active_leads ?? 0;
                $summary_compare_active_leads += (int)$sales->compare_active_leads ?? 0;
            }

            $data = [
                'active_leads' => [
                    'value' => $summary_active_leads,
                    'compare' => $summary_compare_active_leads,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $channelName = $request->input('name');
            $productBrandId = $request->input('product_brand_id');

            $active_leads = Lead::selectRaw("count(id) as active_leads")
                // ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereIn('channel_id', $user->channels->pluck('id'))
                ->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP])
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                    $q2->where('product_brand_id', request()->product_brand_id);
                }))
                ->first()->active_leads ?? 0;

            $compare_active_leads = Lead::selectRaw("count(id) as compare_active_leads")
                // ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereIn('channel_id', $user->channels->pluck('id'))
                ->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare))
                ->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP])
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                    $q2->where('product_brand_id', request()->product_brand_id);
                }))
                ->first()->compare_active_leads ?? 0;

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withCount(['channelLeads as active_leads' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereCreatedAtRange($startDate, $endDate);
            //         $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);
            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelLeads as compare_active_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
            //         $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);
            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $summary_active_leads = $active_leads ?? 0;
            $summary_compare_active_leads = $compare_active_leads ?? 0;

            // foreach ($result as $channel) {
            //     $summary_active_leads += $channel->active_leads ?? 0;
            //     $summary_compare_active_leads += $channel->compare_active_leads ?? 0;
            // }

            $data = [
                'active_leads' => [
                    'value' => $summary_active_leads,
                    'compare' => $summary_compare_active_leads,
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount(['leads as active_leads' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);
                    $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_active_leads' => function ($q) use ($user, $channelId, $startDateCompare, $endDateCompare) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);
                    $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'active_leads' => [
                    'value' => (int)$result->active_leads ?? 0,
                    'compare' => (int)$result->compare_active_leads ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subActiveLeadsByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        // $target_deals = 0;

        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        }
        // else if ($user->is_director || $user->is_digital_marketing) {
        //     $userType = 'director';
        // } else if ($user->is_supervisor) {
        //     if ($user->supervisor_type_id == 1) {
        //         $userType = 'store_leader';
        //     } else if ($user->supervisor_type_id == 2) {
        //         $userType = 'bum';
        //     } else if ($user->supervisor_type_id == 3) {
        //         $userType = 'hs';
        //     }
        // } else if ($user->is_sales) {
        //     $userType = 'sales';
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount(['channelLeads as active_leads' => function ($q) use ($startDate, $endDate) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelLeads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'active_leads' => [
                    'value' => (int)$result->active_leads ?? 0,
                    'compare' => (int)$result->compare_active_leads ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2)
                ->withCount(['leads as active_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_active_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->get();

            $data = [];

            $summary_active_leads = 0;
            $summary_compare_active_leads = 0;
            foreach ($result as $sales) {
                $summary_active_leads += (int)$sales->active_leads ?? 0;
                $summary_compare_active_leads += (int)$sales->compare_active_leads ?? 0;
            }

            $data = [
                'active_leads' => [
                    'value' => $summary_active_leads,
                    'compare' => $summary_compare_active_leads,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $productBrandId = request()->input('product_brand_id');

            $active_leads = Lead::selectRaw("count(id) as active_leads")
                ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP])
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($productBrandId, fn ($q) => $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                    $q2->where('product_brand_id', request()->product_brand_id);
                }))
                ->first()->active_leads ?? 0;

            $compare_active_leads = Lead::selectRaw("count(id) as compare_active_leads")
                ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare))
                ->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP])
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($productBrandId, fn ($q) => $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                    $q2->where('product_brand_id', request()->product_brand_id);
                }))
                ->first()->compare_active_leads ?? 0;

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withCount(['channelLeads as active_leads' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereCreatedAtRange($startDate, $endDate);
            //         $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelLeads as compare_active_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
            //         $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $summary_active_leads = $active_leads ?? 0;
            $summary_compare_active_leads = $compare_active_leads ?? 0;

            // foreach ($result as $channel) {
            //     $summary_active_leads += $channel->active_leads ?? 0;
            //     $summary_compare_active_leads += $channel->compare_active_leads ?? 0;
            // }

            $data = [
                'active_leads' => [
                    'value' => $summary_active_leads,
                    'compare' => $summary_compare_active_leads,
                ],
            ];
        } else {
            // else sales
            $userLoggedIn = user();
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount(['leads as active_leads' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_active_leads' => function ($q) use ($userLoggedIn, $user, $channelId, $startDateCompare, $endDateCompare) {
                    $q->whereHas('latestActivity', fn ($q) => $q->whereCreatedAtRange($startDateCompare, $endDateCompare));
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'active_leads' => [
                    'value' => (int)$result->active_leads ?? 0,
                    'compare' => (int)$result->compare_active_leads ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subFollowUp(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        // $infoDate = [
        //     'original_date' => [
        //         'start' => $startDate,
        //         'end' => $endDate,
        //     ],
        //     'compare_date' => [
        //         'start' => $startDateCompare,
        //         'end' => $endDateCompare,
        //     ]
        // ];

        $target_activities = 0;

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        $subscribtionUserId = $request->subscribtion_user_id ?? $user->subscribtion_user_id;
        $channelId = $request->channel_id ?? null;
        $targetType = TargetType::ACTIVITY_COUNT;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';

            $target_activities = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $subscribtionUserId)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }

            $target_activities = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            $userType = 'sales';

            $target_activities = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
            if ($channelId) {
                $target_activities = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', $targetType)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
            }
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount(['channelActivities as total_activities' => function ($q) use ($startDate, $endDate) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // });

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelActivities as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    // });

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'follow_up' => [
                    'total_activities' => [
                        'value' => (int)$result->total_activities ?? 0,
                        'compare' => (int)$result->compare_total_activities ?? 0,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2)
                ->withCount(['userActivities as total_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as compare_total_activities' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    // });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->get();

            $data = [];

            $summary_total_activities = 0;
            $summary_compare_total_activities = 0;
            foreach ($result as $sales) {
                $summary_total_activities += (int)$sales->total_activities ?? 0;
                $summary_compare_total_activities += (int)$sales->compare_total_activities ?? 0;
            }

            $data = [
                'follow_up' => [
                    'total_activities' => [
                        'value' => $summary_total_activities,
                        'compare' => $summary_compare_total_activities,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $channelName = $request->input('name');
            $productBrandId = request()->input('product_brand_id');

            $total_activities = Activity::selectRaw("count(id) as total_activities")
                ->whereCreatedAtRange($startDate, $endDate)
                ->whereIn('channel_id', $user->channels->pluck('id'))
                // ->whereHas('lead', fn ($q) => $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id)))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->first()->total_activities ?? 0;

            $compare_total_activities = Activity::selectRaw("count(id) as compare_total_activities")
                ->whereCreatedAtRange($startDateCompare, $endDateCompare)
                ->whereIn('channel_id', $user->channels->pluck('id'))
                // ->whereHas('lead', fn ($q) => $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id)))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->first()->compare_total_activities ?? 0;

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withCount(['channelActivities as total_activities' => function ($q) use ($channelId, $startDate, $endDate) {
            //         // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
            //         $q->whereCreatedAtRange($startDate, $endDate);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //         // });

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelActivities as compare_total_activities' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
            //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //         // });

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $summary_total_activities = $total_activities ?? 0;
            $summary_compare_total_activities = $compare_total_activities ?? 0;

            // foreach ($result as $channel) {
            //     $summary_total_activities += $channel->total_activities ?? 0;
            //     $summary_compare_total_activities += $channel->compare_total_activities ?? 0;
            // }

            $data = [
                'follow_up' => [
                    'total_activities' => [
                        'value' => $summary_total_activities,
                        'compare' => $summary_compare_total_activities,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount(['userActivities as total_activities' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    $q->where('channel_id', $user->channel_id);
                    // });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as compare_total_activities' => function ($q) use ($user, $channelId, $startDateCompare, $endDateCompare) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    $q->where('channel_id', $user->channel_id);
                    // });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'follow_up' => [
                    'total_activities' => [
                        'value' => (int)$result->total_activities ?? 0,
                        'compare' => (int)$result->compare_total_activities ?? 0,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subFollowUpByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        $target_activities = 0;

        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            // $userType = 'director';

            // $target_activities = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $companyId)->where('targets.type', 7)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            // if ($user->supervisor_type_id == 1) {
            //     $userType = 'store_leader';
            // } else if ($user->supervisor_type_id == 2) {
            //     $userType = 'bum';
            // } else if ($user->supervisor_type_id == 3) {
            //     $userType = 'hs';
            // }

            // $target_activities = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 7)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            // $userType = 'sales';

            // $target_activities = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 7)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        // if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
        if ($channelId) {
            // $target_activities = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', 7)->whereDate('reports.start_date', '>=', $startTargetDate)->whereDate('reports.end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount(['channelActivities as total_activities' => function ($q) use ($startDate, $endDate) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // });

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelActivities as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    // });

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'follow_up' => [
                    'total_activities' => [
                        'value' => (int)$result->total_activities ?? 0,
                        'compare' => (int)$result->compare_total_activities ?? 0,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2)
                ->withCount(['userActivities as total_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as compare_total_activities' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    // });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->get();

            $data = [];

            $summary_total_activities = 0;
            $summary_compare_total_activities = 0;
            foreach ($result as $sales) {
                $summary_total_activities += (int)$sales->total_activities ?? 0;
                $summary_compare_total_activities += (int)$sales->compare_total_activities ?? 0;
            }

            $data = [
                'follow_up' => [
                    'total_activities' => [
                        'value' => $summary_total_activities,
                        'compare' => $summary_compare_total_activities,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $productBrandId = request()->input('product_brand_id');

            $total_activities = Activity::selectRaw("count(id) as total_activities")
                ->whereCreatedAtRange($startDate, $endDate)
                ->whereHas('lead', fn ($q) => $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id)))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                // ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->first()->total_activities ?? 0;

            $compare_total_activities = Activity::selectRaw("count(id) as compare_total_activities")
                ->whereCreatedAtRange($startDateCompare, $endDateCompare)
                ->whereHas('lead', fn ($q) => $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id)))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                // // ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->first()->compare_total_activities ?? 0;

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withCount(['channelActivities as total_activities' => function ($q) use ($startDate, $endDate) {
            //         // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
            //         $q->whereCreatedAtRange($startDate, $endDate);
            //         // });

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelActivities as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
            //         // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
            //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
            //         // });

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $summary_total_activities = $total_activities ?? 0;
            $summary_compare_total_activities = $compare_total_activities ?? 0;

            // foreach ($result as $channel) {
            //     $summary_total_activities += $channel->total_activities ?? 0;
            //     $summary_compare_total_activities += $channel->compare_total_activities ?? 0;
            // }

            $data = [
                'follow_up' => [
                    'total_activities' => [
                        'value' => $summary_total_activities,
                        'compare' => $summary_compare_total_activities,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                ],
            ];
        } else {
            // else sales
            $userLoggedIn = user();
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount(['userActivities as total_activities' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                    // });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as compare_total_activities' => function ($q) use ($userLoggedIn, $user, $channelId, $startDateCompare, $endDateCompare) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                    // });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'follow_up' => [
                    'total_activities' => [
                        'value' => (int)$result->total_activities ?? 0,
                        'compare' => (int)$result->compare_total_activities ?? 0,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subLeadStatus(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];
        }

        // $infoDate = [
        //     'original_date' => [
        //         'start' => $startDate,
        //         'end' => $endDate,
        //     ],
        //     'compare_date' => [
        //         'start' => $startDateCompare,
        //         'end' => $endDateCompare,
        //     ]
        // ];

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }
        } else if ($user->is_sales) {
            $userType = 'sales';
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount(['channelLeads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 1)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                        ->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelLeads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 2)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelLeads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 3)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'follow_up' => [
                    'hot_activities' => (int)$result->hot_activities ?? 0,
                    'warm_activities' => (int)$result->warm_activities ?? 0,
                    'cold_activities' => (int)$result->cold_activities ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2)
                ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 1)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                        ->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 2)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 3)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->get();

            $data = [];

            $summary_hot_activities = 0;
            $summary_warm_activities = 0;
            $summary_cold_activities = 0;
            foreach ($result as $sales) {
                $summary_hot_activities += (int)$sales->hot_activities ?? 0;
                $summary_warm_activities += (int)$sales->warm_activities ?? 0;
                $summary_cold_activities += (int)$sales->cold_activities ?? 0;
            }

            $data = [
                'follow_up' => [
                    'hot_activities' => $summary_hot_activities,
                    'warm_activities' => $summary_warm_activities,
                    'cold_activities' => $summary_cold_activities,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $channelName = $request->input('name');
            $productBrandId = $request->input('product_brand_id');

            // $hot_activities = Lead::selectRaw("count(id) as hot_activities")
            //     ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
            //     ->where('last_activity_status', 1)
            //     ->whereCreatedAtRange($startDate, $endDate)
            //     ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
            //     ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
            //     ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
            //     ->first()->hot_activities ?? 0;

            // $warm_activities = Lead::selectRaw("count(id) as warm_activities")
            //     ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
            //     ->where('last_activity_status', 2)
            //     ->whereCreatedAtRange($startDate, $endDate)
            //     ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
            //     ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
            //     ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
            //     ->first()->warm_activities ?? 0;

            // $cold_activities = Lead::selectRaw("count(id) as cold_activities")
            //     ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
            //     ->where('last_activity_status', 3)
            //     ->whereCreatedAtRange($startDate, $endDate)
            //     ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
            //     ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
            //     ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
            //     ->first()->cold_activities ?? 0;

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withCount(['channelLeads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal($startDate, $endDate))->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelLeads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelLeads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $hotActivities = Lead::where('last_activity_status', 1)
                // ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereIn('channel_id', $user->channels->pluck('id'))
                ->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate))
                ->count();

            $warmActivities = Lead::where('last_activity_status', 2)
                // ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereIn('channel_id', $user->channels->pluck('id'))
                ->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->count();

            $coldActivities = Lead::where('last_activity_status', 3)
                // ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->whereIn('channel_id', $user->channels->pluck('id'))
                ->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->count();

            $summary_hot_activities = $hotActivities ?? 0;
            $summary_warm_activities = $warmActivities ?? 0;
            $summary_cold_activities = $coldActivities ?? 0;

            // foreach ($result as $channel) {
            //     $summary_hot_activities += $channel->hot_activities ?? 0;
            //     $summary_warm_activities += $channel->warm_activities ?? 0;
            //     $summary_cold_activities += $channel->cold_activities ?? 0;
            // }

            $data = [
                'follow_up' => [
                    'hot_activities' => $summary_hot_activities,
                    'warm_activities' => $summary_warm_activities,
                    'cold_activities' => $summary_cold_activities,
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount(['leads as hot_activities' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 1)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                        ->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate));

                    if ($channelId) {
                        $q->where('channel_id', $channelId);
                    } else {
                        $q->where('channel_id', $user->channel_id);
                    }

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as warm_activities' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 2)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) {
                        $q->where('channel_id', $channelId);
                    } else {
                        $q->where('channel_id', $user->channel_id);
                    }

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as cold_activities' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 3)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) {
                        $q->where('channel_id', $channelId);
                    } else {
                        $q->where('channel_id', $user->channel_id);
                    }

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();
            $data = [
                'follow_up' => [
                    'hot_activities' => (int)$result->hot_activities ?? 0,
                    'warm_activities' => (int)$result->warm_activities ?? 0,
                    'cold_activities' => (int)$result->cold_activities ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subLeadStatusByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        // $target_deals = 0;

        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        }
        // else if ($user->is_director || $user->is_digital_marketing) {
        //     $userType = 'director';
        // } else if ($user->is_supervisor) {
        //     if ($user->supervisor_type_id == 1) {
        //         $userType = 'store_leader';
        //     } else if ($user->supervisor_type_id == 2) {
        //         $userType = 'bum';
        //     } else if ($user->supervisor_type_id == 3) {
        //         $userType = 'hs';
        //     }
        // } else if ($user->is_sales) {
        //     $userType = 'sales';
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount(['channelLeads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 1)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                        ->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelLeads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 2)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['channelLeads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 3)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'follow_up' => [
                    'hot_activities' => (int)$result->hot_activities ?? 0,
                    'warm_activities' => (int)$result->warm_activities ?? 0,
                    'cold_activities' => (int)$result->cold_activities ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2)
                ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 1)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                        ->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 2)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 3)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->get();

            $data = [];

            $summary_hot_activities = 0;
            $summary_warm_activities = 0;
            $summary_cold_activities = 0;
            foreach ($result as $sales) {
                $summary_hot_activities += (int)$sales->hot_activities ?? 0;
                $summary_warm_activities += (int)$sales->warm_activities ?? 0;
                $summary_cold_activities += (int)$sales->cold_activities ?? 0;
            }

            $data = [
                'follow_up' => [
                    'hot_activities' => $summary_hot_activities,
                    'warm_activities' => $summary_warm_activities,
                    'cold_activities' => $summary_cold_activities,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            // $channelName = request()->input('name');
            $productBrandId = request()->input('product_brand_id');

            $hot_activities = Lead::selectRaw("count(id) as hot_activities")
                ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->where('last_activity_status', 1)
                ->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                // ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate))
                ->first()->hot_activities ?? 0;

            $warm_activities = Lead::selectRaw("count(id) as warm_activities")
                ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->where('last_activity_status', 2)
                ->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                // ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->first()->warm_activities ?? 0;

            $cold_activities = Lead::selectRaw("count(id) as cold_activities")
                ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))
                ->where('last_activity_status', 3)
                ->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                // ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                ->first()->cold_activities ?? 0;

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withCount(['channelLeads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal($startDate, $endDate))->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelLeads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }])
            //     ->withCount(['channelLeads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $summary_hot_activities = $hot_activities ?? 0;
            $summary_warm_activities = $warm_activities ?? 0;
            $summary_cold_activities = $cold_activities ?? 0;

            // foreach ($result as $channel) {
            //     $summary_hot_activities += $channel->hot_activities ?? 0;
            //     $summary_warm_activities += $channel->warm_activities ?? 0;
            //     $summary_cold_activities += $channel->cold_activities ?? 0;
            // }

            $data = [
                'follow_up' => [
                    'hot_activities' => $summary_hot_activities,
                    'warm_activities' => $summary_warm_activities,
                    'cold_activities' => $summary_cold_activities,
                ],
            ];
        } else {
            // else sales
            $userLoggedIn = user();
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount(['leads as hot_activities' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 1)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))
                        ->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate));

                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as warm_activities' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 2)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as cold_activities' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                    $q->where('last_activity_status', 3)->whereHas('leadActivities', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);

            $result = $query->first();

            $data = [
                'follow_up' => [
                    'hot_activities' => (int)$result->hot_activities ?? 0,
                    'warm_activities' => (int)$result->warm_activities ?? 0,
                    'cold_activities' => (int)$result->cold_activities ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subFollowUpMethod(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        // $infoDate = [
        //     'original_date' => [
        //         'start' => $startDate,
        //         'end' => $endDate,
        //     ],
        //     'compare_date' => [
        //         'start' => $startDateCompare,
        //         'end' => $endDateCompare,
        //     ]
        // ];

        $target_deals = 0;

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }
        } else if ($user->is_sales) {
            $userType = 'sales';
        }

        $leadCategories = DB::table('lead_categories')->select('id', 'name')->pluck('name', 'id')?->all() ?? [];

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId);

            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $query->withCount(['channelLeads as ' . $leadCategoryId => function ($q) use ($leadCategoryId, $channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->where('lead_category_id', $leadCategoryId)
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
            }

            $result = $query->first();

            $sumTotalLeadCategories = 0;
            $dataLeadCategories = [];
            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $totalLeadCategories = isset($result[$leadCategoryId]) ? $result[$leadCategoryId] : 0;
                $dataLeadCategories[] = [
                    'id' => $leadCategoryId,
                    'name' => $leadCategoryName,
                    'total' => $totalLeadCategories,
                ];
                $sumTotalLeadCategories += $totalLeadCategories;
            }

            $data = [
                'follow_up' => [
                    'total' => $sumTotalLeadCategories,
                    'lead_categories' => $dataLeadCategories,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2);

            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $query->withCount(['leads as ' . $leadCategoryId => function ($q) use ($leadCategoryId, $channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->where('lead_category_id', $leadCategoryId)
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
            }

            $result = $query->get();

            $data = [];
            $sumTotalLeadCategories = 0;
            $dataLeadCategories = [];

            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $totalLeadCategories = $result->sum($leadCategoryId) ?? 0;

                $dataLeadCategories[] = [
                    'id' => $leadCategoryId,
                    'name' => $leadCategoryName,
                    'total' => $totalLeadCategories,
                ];

                $sumTotalLeadCategories += $totalLeadCategories;
            }

            $data = [
                'follow_up' => [
                    'total' => $sumTotalLeadCategories,
                    'lead_categories' => $dataLeadCategories,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $channelName = $request->input('name');
            $productBrandId = $request->input('product_brand_id');

            $sumTotalLeadCategories = 0;
            $dataLeadCategories = [];
            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $renameLeadCategoryName = str_replace(' ', '_', strtolower($leadCategoryName));

                $renameLeadCategoryName = Lead::selectRaw("count(distinct(customer_id)) as " . $renameLeadCategoryName)
                    ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))

                    ->where('lead_category_id', $leadCategoryId)
                    ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))

                    ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                    ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                    ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                    ->first()->{$renameLeadCategoryName} ?? 0;

                $dataLeadCategories[] = [
                    'id' => $leadCategoryId,
                    'name' => $leadCategoryName,
                    'total' => $renameLeadCategoryName,
                ];

                $sumTotalLeadCategories += $renameLeadCategoryName;
            }

            $data = [
                'follow_up' => [
                    'total' => $sumTotalLeadCategories,
                    'lead_categories' => $dataLeadCategories,
                ],
            ];

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all());

            // foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
            //     $query->withCount(['channelLeads as ' . $leadCategoryId => function ($q) use ($leadCategoryId, $channelId, $startDate, $endDate) {
            //         $q->select(DB::raw('count(distinct(customer_id))'))
            //             ->where('lead_category_id', $leadCategoryId)
            //             ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);
            // }

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            // $sumTotalLeadCategories = 0;
            // $dataLeadCategories = [];

            // foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
            //     $totalLeadCategories = $result->sum($leadCategoryId) ?? 0;

            //     $dataLeadCategories[] = [
            //         'id' => $leadCategoryId,
            //         'name' => $leadCategoryName,
            //         'total' => $totalLeadCategories,
            //     ];

            //     $sumTotalLeadCategories += $totalLeadCategories;
            // }

            // $data = [
            //     'follow_up' => [
            //         'total' => $sumTotalLeadCategories,
            //         'lead_categories' => $dataLeadCategories,
            //     ],
            // ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id);

            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $query->withCount(['leads as ' . $leadCategoryId => function ($q) use ($user, $leadCategoryId, $channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->where('lead_category_id', $leadCategoryId)
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));
                    $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
            }

            $result = $query->first();

            $sumTotalLeadCategories = 0;
            $dataLeadCategories = [];
            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $totalLeadCategories = isset($result[$leadCategoryId]) ? $result[$leadCategoryId] : 0;
                $dataLeadCategories[] = [
                    'id' => $leadCategoryId,
                    'name' => $leadCategoryName,
                    'total' => $totalLeadCategories,
                ];
                $sumTotalLeadCategories += $totalLeadCategories;
            }

            $data = [
                'follow_up' => [
                    'total' => $sumTotalLeadCategories,
                    'lead_categories' => $dataLeadCategories,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subFollowUpMethodByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        // $target_deals = 0;

        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        }
        // else if ($user->is_director || $user->is_digital_marketing) {
        //     $userType = 'director';
        // } else if ($user->is_supervisor) {
        //     if ($user->supervisor_type_id == 1) {
        //         $userType = 'store_leader';
        //     } else if ($user->supervisor_type_id == 2) {
        //         $userType = 'bum';
        //     } else if ($user->supervisor_type_id == 3) {
        //         $userType = 'hs';
        //     }
        // } else if ($user->is_sales) {
        //     $userType = 'sales';
        // }

        $leadCategories = DB::table('lead_categories')->select('id', 'name')->pluck('name', 'id')?->all() ?? [];

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId);

            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $query->withCount(['channelLeads as ' . $leadCategoryId => function ($q) use ($leadCategoryId, $channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->where('lead_category_id', $leadCategoryId)
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
            }

            $result = $query->first();

            $sumTotalLeadCategories = 0;
            $dataLeadCategories = [];
            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $totalLeadCategories = isset($result[$leadCategoryId]) ? $result[$leadCategoryId] : 0;
                $dataLeadCategories[] = [
                    'id' => $leadCategoryId,
                    'name' => $leadCategoryName,
                    'total' => $totalLeadCategories,
                ];
                $sumTotalLeadCategories += $totalLeadCategories;
            }

            $data = [
                'follow_up' => [
                    'total' => $sumTotalLeadCategories,
                    'lead_categories' => $dataLeadCategories,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2);

            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $query->withCount(['leads as ' . $leadCategoryId => function ($q) use ($leadCategoryId, $channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->where('lead_category_id', $leadCategoryId)
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
            }

            $result = $query->get();

            $data = [];
            $sumTotalLeadCategories = 0;
            $dataLeadCategories = [];

            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $totalLeadCategories = $result->sum($leadCategoryId) ?? 0;

                $dataLeadCategories[] = [
                    'id' => $leadCategoryId,
                    'name' => $leadCategoryName,
                    'total' => $totalLeadCategories,
                ];

                $sumTotalLeadCategories += $totalLeadCategories;
            }

            $data = [
                'follow_up' => [
                    'total' => $sumTotalLeadCategories,
                    'lead_categories' => $dataLeadCategories,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $channelName = request()->input('name');
            $productBrandId = request()->input('product_brand_id');

            $sumTotalLeadCategories = 0;
            $dataLeadCategories = [];
            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $renameLeadCategoryName = str_replace(' ', '_', strtolower($leadCategoryName));

                $renameLeadCategoryName = Lead::selectRaw("count(distinct(customer_id)) as " . $renameLeadCategoryName)
                    ->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id))

                    ->where('lead_category_id', $leadCategoryId)
                    ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate))

                    ->when(!is_null($channelId), fn ($q) => $q->where('channel_id', $channelId))
                    ->when($channelName, fn ($q) => $q->whereHas('channel', fn ($q) => $q->where('name', 'like ', '%' . $channelName . '%')))
                    ->when($productBrandId, fn ($q) => $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId)))
                    ->first()->{$renameLeadCategoryName} ?? 0;

                $dataLeadCategories[] = [
                    'id' => $leadCategoryId,
                    'name' => $leadCategoryName,
                    'total' => $renameLeadCategoryName,
                ];

                $sumTotalLeadCategories += $renameLeadCategoryName;
            }

            $data = [
                'follow_up' => [
                    'total' => $sumTotalLeadCategories,
                    'lead_categories' => $dataLeadCategories,
                ],
            ];

            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all());

            // foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
            //     $query->withCount(['channelLeads as ' . $leadCategoryId => function ($q) use ($leadCategoryId, $channelId, $startDate, $endDate) {
            //         $q->select(DB::raw('count(distinct(customer_id))'))
            //             ->where('lead_category_id', $leadCategoryId)
            //             ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

            //         if ($channelId) $q->where('channel_id', $channelId);

            //         if (request()->product_brand_id) {
            //             $q->whereHas('activityBrandValues', function ($q2) {
            //                 $q2->where('product_brand_id', request()->product_brand_id);
            //             });
            //         }
            //     }]);
            // }

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // // if ($request->name) {
            // //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // // }

            // $result = $query->get();

            // $data = [];
            // $sumTotalLeadCategories = 0;
            // $dataLeadCategories = [];

            // foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
            //     $totalLeadCategories = $result->sum($leadCategoryId) ?? 0;

            //     $dataLeadCategories[] = [
            //         'id' => $leadCategoryId,
            //         'name' => $leadCategoryName,
            //         'total' => $totalLeadCategories,
            //     ];

            //     $sumTotalLeadCategories += $totalLeadCategories;
            // }

            // $data = [
            //     'follow_up' => [
            //         'total' => $sumTotalLeadCategories,
            //         'lead_categories' => $dataLeadCategories,
            //     ],
            // ];
        } else {
            // else sales
            $userLoggedIn = user();
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id);

            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $query->withCount(['leads as ' . $leadCategoryId => function ($q) use ($userLoggedIn, $user, $leadCategoryId, $channelId, $startDate, $endDate) {
                    $q->select(DB::raw('count(distinct(customer_id))'))
                        ->where('lead_category_id', $leadCategoryId)
                        ->whereHas('customer', fn ($q) => $q->whereCreatedAtRange($startDate, $endDate));

                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }]);
            }

            $result = $query->first();

            $sumTotalLeadCategories = 0;
            $dataLeadCategories = [];
            foreach ($leadCategories as $leadCategoryId => $leadCategoryName) {
                $totalLeadCategories = isset($result[$leadCategoryId]) ? $result[$leadCategoryId] : 0;
                $dataLeadCategories[] = [
                    'id' => $leadCategoryId,
                    'name' => $leadCategoryName,
                    'total' => $totalLeadCategories,
                ];
                $sumTotalLeadCategories += $totalLeadCategories;
            }

            $data = [
                'follow_up' => [
                    'total' => $sumTotalLeadCategories,
                    'lead_categories' => $dataLeadCategories,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subQuotation(Request $request, bool $allTime = false)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        if ($allTime) {
            $startDate = Carbon::parse('2020-01-01');
            $endDate = Carbon::now()->endOfMonth();
        }

        $target_deals = 0;

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }
        } else if ($user->is_sales) {
            $userType = 'sales';
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withSum(['channelOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                    $q->whereNotDeal($startDate, $endDate);
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                }], 'total_price')
                ->withSum(['channelOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                    $q->whereNotDeal($startDateCompare, $endDateCompare);
                }], 'total_price');

            $result = $query->first();

            $data = [
                'quotation' => [
                    'value' => (int) $result->total_quotation ?? 0,
                    'compare' => (int) $result->compare_total_quotation ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            // $query = User::selectRaw(self::USER_COLUMNS)
            //     ->where('company_id', $companyId)
            //     ->where('type', 2)
            //     ->withSum(['userOrders as total_quotation' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereNotDeal($startDate, $endDate);
            //         // $q->whereDoesntHave('orderPayments');
            //         // $q->whereNotIn('status', [5, 6]);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }], 'total_price')
            //     ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         // $q->whereDoesntHave('orderPayments');
            //         // $q->whereNotIn('status', [5, 6]);
            //         $q->whereNotDeal($startDateCompare, $endDateCompare);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }], 'total_price');

            // $result = $query->get();

            // $data = [];
            // $summary_quotation = 0;
            // $summary_compare_quotation = 0;
            // foreach ($result as $sales) {
            //     $summary_quotation += (int)$sales->total_quotation ?? 0;
            //     $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
            // }

            $summary_quotation = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereNotDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $summary_compare_quotation = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereNotDeal($startDateCompare, $endDateCompare)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $data = [
                'quotation' => [
                    'value' => $summary_quotation,
                    'compare' => $summary_compare_quotation,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
                ->withSum(['channelOrders as total_quotation' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotDeal($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                }], 'total_price')
                ->withSum(['channelOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                    $q->whereNotDeal($startDateCompare, $endDateCompare);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price');

            // $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
            //     ->with('leadUsers', function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
            //         $q->withSum(['orders as total_quotation' => function ($q) use ($channelId, $startDate, $endDate) {
            //             $q->whereNotDeal($startDate, $endDate);
            //             if ($channelId) $q->where('channel_id', $channelId);
            //             // $q->whereDoesntHave('orderPayments');
            //             // $q->whereNotIn('status', [5, 6]);
            //         }], 'total_price')
            //             ->withSum(['orders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //                 $q->whereNotDeal($startDateCompare, $endDateCompare);
            //                 if ($channelId) $q->where('channel_id', $channelId);
            //                 // $q->whereDoesntHave('orderPayments');
            //                 // $q->whereNotIn('status', [5, 6]);
            //             }], 'total_price');
            //     });

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            if ($request->name) {
                $query = $query->where('name', 'like', '%' . $request->name . '%');
            }

            $result = $query->get();
            // $result = $query->first();

            $summary_quotation = $result->sum('total_quotation') ?? 0;
            $summary_compare_quotation = $result->sum('compare_total_quotation') ?? 0;

            // foreach ($result as $channel) {
            //     $summary_quotation += $channel->total_quotation ?? 0;
            //     $summary_compare_quotation += $channel->compare_total_quotation ?? 0;
            // }

            $data = [
                'quotation' => [
                    'value' => $summary_quotation,
                    'compare' => $summary_compare_quotation,
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withSum(['userOrders as total_quotation' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                    $q->whereNotDeal($startDate, $endDate);
                    $q->where('channel_id', $user->channel_id);
                    // $q->whereNotDeal($startDate, $endDate);
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($user, $channelId, $startDateCompare, $endDateCompare) {
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                    $q->whereNotDeal($startDateCompare, $endDateCompare);
                    $q->where('channel_id', $user->channel_id);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price');

            $result = $query->first();

            $data = [
                'quotation' => [
                    'value' => (int) $result->total_quotation ?? 0,
                    'compare' => (int) $result->compare_total_quotation ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subQuotationsByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        // $target_deals = 0;

        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        }
        // else if ($user->is_director || $user->is_digital_marketing) {
        //     $userType = 'director';
        // } else if ($user->is_supervisor) {
        //     if ($user->supervisor_type_id == 1) {
        //         $userType = 'store_leader';
        //     } else if ($user->supervisor_type_id == 2) {
        //         $userType = 'bum';
        //     } else if ($user->supervisor_type_id == 3) {
        //         $userType = 'hs';
        //     }
        // } else if ($user->is_sales) {
        //     $userType = 'sales';
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withSum(['channelOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                    $q->whereNotDeal($startDate, $endDate);
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                }], 'total_price')
                ->withSum(['channelOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                    $q->whereNotDeal($startDateCompare, $endDateCompare);
                }], 'total_price');

            $result = $query->first();

            $data = [
                'quotation' => [
                    'value' => (int) $result->total_quotation ?? 0,
                    'compare' => (int) $result->compare_total_quotation ?? 0,
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            // $query = User::selectRaw(self::USER_COLUMNS)
            //     ->where('company_id', $companyId)
            //     ->where('type', 2)
            //     ->withSum(['userOrders as total_quotation' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereNotDeal($startDate, $endDate);
            //         // $q->whereDoesntHave('orderPayments');
            //         // $q->whereNotIn('status', [5, 6]);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }], 'total_price')
            //     ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
            //         // $q->whereDoesntHave('orderPayments');
            //         // $q->whereNotIn('status', [5, 6]);
            //         $q->whereNotDeal($startDateCompare, $endDateCompare);
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }], 'total_price');

            // $result = $query->get();

            // $data = [];
            // $summary_quotation = 0;
            // $summary_compare_quotation = 0;
            // foreach ($result as $sales) {
            //     $summary_quotation += (int)$sales->total_quotation ?? 0;
            //     $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
            // }

            $summary_quotation = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereNotDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $summary_compare_quotation = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereNotDeal($startDateCompare, $endDateCompare)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $data = [
                'quotation' => [
                    'value' => $summary_quotation,
                    'compare' => $summary_compare_quotation,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all())
            //     ->withSum(['channelOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
            //         $q->whereNotDeal($startDate, $endDate);
            //         // $q->whereDoesntHave('orderPayments');
            //         // $q->whereNotIn('status', [5, 6]);
            //     }], 'total_price')
            //     ->withSum(['channelOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
            //         // $q->whereDoesntHave('orderPayments');
            //         // $q->whereNotIn('status', [5, 6]);
            //         $q->whereNotDeal($startDateCompare, $endDateCompare);
            //     }], 'total_price');

            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->with('leadUsers', function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $q->withSum(['orders as total_quotation' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                        // $q->whereDoesntHave('orderPayments');
                        // $q->whereNotIn('status', [5, 6]);
                    }], 'total_price')
                        ->withSum(['orders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                            $q->whereNotDeal($startDateCompare, $endDateCompare);
                            if ($channelId) $q->where('channel_id', $channelId);
                            // $q->whereDoesntHave('orderPayments');
                            // $q->whereNotIn('status', [5, 6]);
                        }], 'total_price');
                });

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();
            $result = $query->first();

            $summary_quotation = $result->leadUsers?->sum('total_quotation');
            $summary_compare_quotation = $result->leadUsers?->sum('compare_total_quotation');

            // foreach ($result as $channel) {
            //     $summary_quotation += $channel->total_quotation ?? 0;
            //     $summary_compare_quotation += $channel->compare_total_quotation ?? 0;
            // }

            $data = [
                'quotation' => [
                    'value' => $summary_quotation,
                    'compare' => $summary_compare_quotation,
                ],
            ];
        } else {
            // else sales
            $userLoggedIn = user();
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withSum(['userOrders as total_quotation' => function ($q) use ($userLoggedIn, $user, $channelId, $startDate, $endDate) {
                    $q->whereNotDeal($startDate, $endDate);
                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($userLoggedIn, $user, $channelId, $startDateCompare, $endDateCompare) {
                    // $q->whereDoesntHave('orderPayments');
                    // $q->whereNotIn('status', [5, 6]);
                    $q->whereNotDeal($startDateCompare, $endDateCompare);
                    if ($userLoggedIn->is_sales) $q->where('channel_id', $user->channel_id);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price');

            $result = $query->first();

            $data = [
                'quotation' => [
                    'value' => (int) $result->total_quotation ?? 0,
                    'compare' => (int) $result->compare_total_quotation ?? 0,
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subEstimations(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        // $infoDate = [
        //     'original_date' => [
        //         'start' => $startDate,
        //         'end' => $endDate,
        //     ],
        //     'compare_date' => [
        //         'start' => $startDateCompare,
        //         'end' => $endDateCompare,
        //     ]
        // ];

        $target_deals = 0;

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }
        } else if ($user->is_sales) {
            $userType = 'sales';
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId);

            $result = $query->first();

            $pbs = self::getPbs($result, $startDate, $endDate, $startDateCompare, $endDateCompare);

            $data = [
                'estimation' => [
                    'value' => $pbs['estimated_value'],
                    'compare' => $pbs['compare_estimated_value'],
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2);

            $result = $query->get();

            $data = [];
            $summary_estimation = 0;
            $summary_compare_estimation = 0;
            foreach ($result as $sales) {
                $pbs = self::getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare);

                $summary_estimation += $pbs['estimated_value'];
                $summary_compare_estimation += $pbs['compare_estimated_value'];
            }

            $data = [
                'estimation' => [
                    'value' => $summary_estimation,
                    'compare' => $summary_compare_estimation,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            // $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all());

            // if ($channelId) {
            //     $query = $query->where('id', $channelId);
            // }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            // $result = $query->get();

            $pbs = self::getPbs($user, $startDate, $endDate, $startDateCompare, $endDateCompare);
            $summary_estimation = (int)$pbs['estimated_value'] ?? 0;
            $summary_compare_estimation = (int)$pbs['compare_estimated_value'] ?? 0;

            // foreach ($result as $channel) {
            //     $pbs = self::getPbs($channel, $startDate, $endDate, $startDateCompare, $endDateCompare);
            //     $summary_estimation += (int)$pbs['estimated_value'] ?? 0;
            //     $summary_compare_estimation += (int)$pbs['compare_estimated_value'] ?? 0;
            // }


            $data = [
                'estimation' => [
                    'value' => $summary_estimation,
                    'compare' => $summary_compare_estimation,
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id);

            $result = $query->first();

            $pbs = self::getPbs($result, $startDate, $endDate, $startDateCompare, $endDateCompare);

            $data = [
                'estimation' => [
                    'value' => $pbs['estimated_value'],
                    'compare' => $pbs['compare_estimated_value'],
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function subEstimationsByUser(
        $user,
        $userType,
        $startDate,
        $endDate,
        $startDateCompare,
        $endDateCompare,
        $startTargetDate,
        $endTargetDate,
        $companyId = null,
        $channelId = null,
    ) {
        // $target_deals = 0;

        // $userType = null;

        // if ($request->user_type == 'store') {
        //     $userType = 'store';
        //     $user = Channel::find($request->user_id);
        // } else {
        //     $user = $request->user_id ? User::find($request->user_id) : user();
        // }

        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        }
        // else if ($user->is_director || $user->is_digital_marketing) {
        //     $userType = 'director';
        // } else if ($user->is_supervisor) {
        //     if ($user->supervisor_type_id == 1) {
        //         $userType = 'store_leader';
        //     } else if ($user->supervisor_type_id == 2) {
        //         $userType = 'bum';
        //     } else if ($user->supervisor_type_id == 3) {
        //         $userType = 'hs';
        //     }
        // } else if ($user->is_sales) {
        //     $userType = 'sales';
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId);

            $result = $query->first();

            $pbs = self::getPbs($result, $startDate, $endDate, $startDateCompare, $endDateCompare);

            $data = [
                'estimation' => [
                    'value' => $pbs['estimated_value'],
                    'compare' => $pbs['compare_estimated_value'],
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $query = User::selectRaw(self::USER_COLUMNS)
                ->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->where('type', 2);

            $result = $query->get();

            $data = [];
            $summary_estimation = 0;
            $summary_compare_estimation = 0;
            foreach ($result as $sales) {
                $pbs = self::getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare);

                $summary_estimation += $pbs['estimated_value'];
                $summary_compare_estimation += $pbs['compare_estimated_value'];
            }

            $data = [
                'estimation' => [
                    'value' => $summary_estimation,
                    'compare' => $summary_compare_estimation,
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->whereIn('id', $user->channels->pluck('id')->all());

            if ($channelId) {
                $query = $query->where('id', $channelId);
            }

            // if ($request->name) {
            //     $query = $query->where('name', 'like', '%' . $request->name . '%');
            // }

            $result = $query->get();

            $data = [];
            $summary_estimation = 0;
            $summary_compare_estimation = 0;

            foreach ($result as $channel) {
                $pbs = self::getPbs($channel, $startDate, $endDate, $startDateCompare, $endDateCompare);
                $summary_estimation += (int)$pbs['estimated_value'] ?? 0;
                $summary_compare_estimation += (int)$pbs['compare_estimated_value'] ?? 0;
            }

            $data = [
                'estimation' => [
                    'value' => $summary_estimation,
                    'compare' => $summary_compare_estimation,
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id);

            $result = $query->first();

            $pbs = self::getPbs($result, $startDate, $endDate, $startDateCompare, $endDateCompare);

            $data = [
                'estimation' => [
                    'value' => $pbs['estimated_value'],
                    'compare' => $pbs['compare_estimated_value'],
                ],
            ];
        }

        return [
            'data' => $data,
            // 'info_date' => $infoDate,
        ];
    }

    public static function getPbs($user, $startDate, $endDate, $startDateCompare, $endDateCompare, $filterChannelId = null, $companyId = null)
    {
        $query = ProductBrand::selectRaw('id');
        if ($user instanceof Channel) {
            $query->where('subscribtion_user_id', $user->subscribtion_user_id)
                ->withSum(['activityBrandValues as estimated_value' => function ($q) use ($user, $filterChannelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $user->id));

                    $q->whereDoesntHave(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)->where('channel_id', $user->id)
                    );

                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }], 'estimated_value')
                ->withSum(['activityBrandValuesDeals as compare_estimated_value' => function ($q) use ($user, $filterChannelId, $startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $user->id));

                    $q->whereDoesntHave(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDateCompare, $endDateCompare)->where('channel_id', $user->id)
                    );

                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }], 'estimated_value');
            // ->with(['activityBrandValues' => function ($q) use ($user, $filterChannelId, $startDate, $endDate) {
            //     $q->whereCreatedAtRange($startDate, $endDate)
            //         ->orderBy('lead_id', 'desc')
            //         ->orderBy('activity_id', 'desc');

            //     $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $user->id));

            //     $q->whereHas(
            //         'order',
            //         fn ($q2) => $q2->whereNotDeal($startDate, $endDate)->where('channel_id', $user->id)
            //     );

            //     if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            // }])
            //     ->with(['activityBrandValuesDeals' => function ($q) use ($user, $filterChannelId, $startDateCompare, $endDateCompare) {
            //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
            //             ->orderBy('lead_id', 'desc')
            //             ->orderBy('activity_id', 'desc');

            //         $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $user->id));

            //         $q->whereHas(
            //             'order',
            //             fn ($q2) => $q2->whereDeal($startDateCompare, $endDateCompare)->where('channel_id', $user->id)
            //         );

            //         if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            //     }]);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            // $companyIds = [$companyId] ?? $user->companies->pluck('id')->all() ?? [];
            // $companyId = $companyId ?? $user->company_id ?? 1;
            $subscribtion_user_id = $user->subscribtion_user_id;

            $query->where('subscribtion_user_id', $subscribtion_user_id)
                ->withSum(['activityBrandValues as estimated_value' => function ($q) use ($filterChannelId, $subscribtion_user_id, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', Channel::where('subscribtion_user_id', $subscribtion_user_id)->pluck('id')->all()));

                    $q->whereDoesntHave(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)->where('subscribtion_user_id', $subscribtion_user_id)
                    );

                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }], 'estimated_value')
                ->withSum(['activityBrandValuesDeals as compare_estimated_value' => function ($q) use ($filterChannelId, $subscribtion_user_id, $startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', Channel::where('subscribtion_user_id', $subscribtion_user_id)->pluck('id')->all()));

                    $q->whereDoesntHave(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDateCompare, $endDateCompare)->where('subscribtion_user_id', $subscribtion_user_id)
                    );

                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }], 'estimated_value');
            //     ->with(['activityBrandValues' => function ($q) use ($filterChannelId, $companyId, $startDate, $endDate) {
            //     $q->whereCreatedAtRange($startDate, $endDate)
            //         ->orderBy('lead_id', 'desc')
            //         ->orderBy('activity_id', 'desc');
            //     $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', Channel::where('company_id', $companyId)->pluck('id')->all()));

            //     $q->whereHas(
            //         'order',
            //         fn ($q2) => $q2->whereNotDeal($startDate, $endDate)->where('company_id', $companyId)
            //     );

            //     if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            // }])
            //     ->with(['activityBrandValuesDeals' => function ($q) use ($filterChannelId, $companyId, $startDateCompare, $endDateCompare) {
            //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
            //             ->orderBy('lead_id', 'desc')
            //             ->orderBy('activity_id', 'desc');

            //         $q->whereHas(
            //             'order',
            //             fn ($q2) => $q2->whereDeal($startDateCompare, $endDateCompare)->where('company_id', $companyId)
            //         );
            //         if ($filterChannelId) $q->whereHas('order', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            //     }]);
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            // $channelIds = $user->channels->pluck('id')->all();
            $query
                ->withSum(['activityBrandValues as estimated_value' => function ($q) use ($user, $filterChannelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas('lead', fn ($q2) => $q2->whereHas('leadUsers', fn ($q2) => $q2->where('user_id', $user->id)));

                    $q->whereDoesntHave(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)->whereHas('leadUser', fn ($q2) => $q2->where('user_id', $user->id))
                    );

                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }], 'estimated_value')
                ->withSum(['activityBrandValuesDeals as compare_estimated_value' => function ($q) use ($user, $filterChannelId, $startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas('leadUser', fn ($q2) => $q2->where('user_id', $user->id));

                    $q->whereDoesntHave(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDateCompare, $endDateCompare)->whereHas('leadUser', fn ($q2) => $q2->where('user_id', $user->id))
                    );

                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }], 'estimated_value');

            // $query
            //     ->withSum(['activityBrandValues as estimated_value' => function ($q) use ($filterChannelId, $channelIds, $startDate, $endDate) {
            //         $q->whereCreatedAtRange($startDate, $endDate)
            //             ->orderBy('lead_id', 'desc')
            //             ->orderBy('activity_id', 'desc');

            //         $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', $channelIds));

            //         $q->whereDoesntHave(
            //             'order',
            //             fn ($q2) => $q2->whereDeal($startDate, $endDate)->whereIn('channel_id', $channelIds)
            //         );

            //         if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            //     }], 'estimated_value')
            //     ->withSum(['activityBrandValuesDeals as compare_estimated_value' => function ($q) use ($filterChannelId, $channelIds, $startDateCompare, $endDateCompare) {
            //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
            //             ->orderBy('lead_id', 'desc')
            //             ->orderBy('activity_id', 'desc');

            //         $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', $channelIds));

            //         $q->whereDoesntHave(
            //             'order',
            //             fn ($q2) => $q2->whereDeal($startDateCompare, $endDateCompare)->whereIn('channel_id', $channelIds)
            //         );

            //         if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            //     }], 'estimated_value');

            // ->with(['activityBrandValues' => function ($q) use ($filterChannelId, $channelIds, $startDate, $endDate) {
            //     $q->whereCreatedAtRange($startDate, $endDate)
            //         ->orderBy('lead_id', 'desc')
            //         ->orderBy('activity_id', 'desc');
            //     $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', $channelIds));

            //     $q->whereHas(
            //         'order',
            //         fn ($q2) => $q2->whereNotDeal($startDate, $endDate)->whereIn('channel_id', $channelIds)
            //     );

            //     if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            // }])
            //     ->with(['activityBrandValuesDeals' => function ($q) use ($filterChannelId, $channelIds, $startDateCompare, $endDateCompare) {
            //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
            //             // ->where('user_id', $id)
            //             ->orderBy('lead_id', 'desc')
            //             ->orderBy('activity_id', 'desc');

            //         $q->whereHas(
            //             'order',
            //             fn ($q2) => $q2->whereDeal($startDateCompare, $endDateCompare)->whereIn('channel_id', $channelIds)
            //         );
            //         if ($filterChannelId) $q->whereHas('order', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            //     }])
            //     ->where('company_id', $companyId);
        } else {
            $userLoggedIn = user();
            $query
                ->withSum(['activityBrandValues as estimated_value' => function ($q) use ($userLoggedIn, $user, $filterChannelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        ->where('user_id', $user->id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas('lead', function ($q2) use ($userLoggedIn, $user, $filterChannelId) {
                        $q2->where('user_id', $user->id);
                        if ($userLoggedIn->is_sales) $q2->where('channel_id', $user->channel_id);
                        if ($filterChannelId) $q2->where('channel_id', $filterChannelId);
                    });

                    $q->whereDoesntHave('order', function ($q2) use ($userLoggedIn, $user, $filterChannelId, $startDate, $endDate) {
                        $q2->whereDeal($startDate, $endDate)->where('user_id', $user->id);
                        if ($userLoggedIn->is_sales) $q2->where('channel_id', $user->channel_id);
                        if ($filterChannelId) $q2->where('channel_id', $filterChannelId);
                    });
                }], 'estimated_value')
                ->withSum(['activityBrandValuesDeals as compare_estimated_value' => function ($q) use ($userLoggedIn, $user, $filterChannelId, $startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
                        ->where('user_id', $user->id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas('lead', function ($q2) use ($userLoggedIn, $user, $filterChannelId) {
                        $q2->where('user_id', $user->id);
                        if ($userLoggedIn->is_sales) $q2->where('channel_id', $user->channel_id);
                        if ($filterChannelId) $q2->where('channel_id', $filterChannelId);
                    });

                    $q->whereDoesntHave('order', function ($q2) use ($userLoggedIn, $user, $filterChannelId, $startDateCompare, $endDateCompare) {
                        $q2->whereDeal($startDateCompare, $endDateCompare)->where('user_id', $user->id);
                        if ($userLoggedIn->is_sales) $q2->where('channel_id', $user->channel_id);
                        if ($filterChannelId) $q2->where('channel_id', $filterChannelId);
                    });
                }], 'estimated_value');

            // ->with(['activityBrandValues' => function ($q) use ($user, $filterChannelId, $startDate, $endDate) {
            //     $q->whereCreatedAtRange($startDate, $endDate)
            //         ->where('user_id', $user->id)
            //         ->orderBy('lead_id', 'desc')
            //         ->orderBy('activity_id', 'desc');

            //     $q->whereHas(
            //         'order',
            //         fn ($q2) => $q2->whereNotDeal($startDate, $endDate)->where('user_id', $user->id)
            //     );

            //     if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            // }])
            // ->with(['activityBrandValuesDeals' => function ($q) use ($user, $filterChannelId, $startDateCompare, $endDateCompare) {
            //     $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
            //         ->where('user_id', $user->id)
            //         ->orderBy('lead_id', 'desc')
            //         ->orderBy('activity_id', 'desc');

            //     $q->whereHas(
            //         'order',
            //         fn ($q2) => $q2->whereDeal($startDateCompare, $endDateCompare)->where('user_id', $user->id)
            //     );

            //     if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            // }]);
        }

        $data = $query->get();
        // foreach ($data as $productBrand) {
        //     // $activityBrandValues = $productBrand->activityBrandValues?->unique('lead_id');
        //     // $activityBrandValuesDeals = $productBrand->activityBrandValuesDeals?->unique('lead_id');

        //     $estimated_value += $productBrand->activityBrandValues?->sum('estimated_value') ?? 0;
        //     $compare_estimated_value += $productBrand->activityBrandValuesDeals?->sum('estimated_value') ?? 0;
        //     // $estimated_value += $activityBrandValues?->sum('estimated_value');
        //     // $compare_estimated_value += $activityBrandValuesDeals?->sum('estimated_value');
        // }

        return [
            'estimated_value' => $data->sum('estimated_value') ?? 0,
            'compare_estimated_value' => $data->sum('compare_estimated_value') ?? 0,
            // 'estimated_value' => $estimated_value ?? 0,
            // 'compare_estimated_value' => $compare_estimated_value ?? 0
        ];
    }

    public static function exportReportDeals(Request $request, User $user = null)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }

        $mtdStart = Carbon::createFromFormat('Y-m-d', $startDate->format('Y-m-d'))->firstOfMonth();
        $mtdEnd = Carbon::createFromFormat('Y-m-d', $endDate->format('Y-m-d'))->endOfMonth();

        $quarter1 = false;
        $quarter2 = false;
        $quarter3 = false;
        $quarter4 = false;
        if ($quarter = $request->quarter) {
            if (str_contains($quarter, 1)) {
                $quarter1 = true;
                $q1 = Carbon::parse($startDate->format('Y') . '-01-01');
                $startDateQ1 = $q1->copy()->firstOfQuarter();
                $endDateQ1 = $q1->copy()->lastOfQuarter();
            }
            if (str_contains($quarter, 2)) {
                $quarter2 = true;
                $q2 = Carbon::parse($startDate->format('Y') . '-04-01');
                $startDateQ2 = $q2->copy()->firstOfQuarter();
                $endDateQ2 = $q2->copy()->lastOfQuarter();
            }
            if (str_contains($quarter, 3)) {
                $quarter3 = true;
                $q3 = Carbon::parse($startDate->format('Y') . '-07-01');
                $startDateQ3 = $q3->copy()->firstOfQuarter();
                $endDateQ3 = $q3->copy()->lastOfQuarter();
            }
            if (str_contains($quarter, 4)) {
                $quarter4 = true;
                $q4 = Carbon::parse($startDate->format('Y') . '-10-01');
                $startDateQ4 = $q4->copy()->firstOfQuarter();
                $endDateQ4 = $q4->copy()->lastOfQuarter();
            }
        }

        $user = $request->user_id ? User::find($request->user_id) : ($user ? $user : user());
        $userType = null;

        // $companyId = $request->company_id ?? $user->company_id;
        $companyId = $user->subscribtion_user_id;
        $channelId = $request->channel_id ?? null;

        if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }
        } else if ($user->is_sales) {
            $userType = 'sales';
        }

        $dates = [];
        $data = [];
        $dataTotal = [];

        if ($request->is_my_report && ($request->is_my_report == true || $request->is_my_report == 1)) {
            $subDeals = ApiNewReportService::subDeals($request);

            $request->start_date = $mtdStart;
            $request->end_date = $mtdEnd;
            $subDealsMtd = ApiNewReportService::subDealsMtd($request);

            $data = [
                array_merge(
                    [
                        'name' => $user->is_director ? DB::table('subscribtion_users')->where('id', $companyId)->select('name')->first()->name : $user->name
                    ],
                    $subDeals['data'],
                    [
                        'deals_mtd' => $subDealsMtd['data']
                    ]
                )
            ];
        } else if (in_array($userType, ['director'])) {
            if ($request->user_type == 'bum') {
                $bum = User::where('subscribtion_user_id', $companyId)
                    ->where('type', UserType::SUPERVISOR)
                    ->where('supervisor_type_id', 2)
                    ->when($request->supervisor_id, fn ($q) => $q->where('id', $request->supervisor_id))
                    ->get();

                foreach ($bum as $bum) {
                    $subDeals = ApiNewReportService::subReportDealsByUser(
                        $bum,
                        'bum',
                        $startDate,
                        $endDate,
                        $startDateCompare,
                        $endDateCompare,
                        $startTargetDate,
                        $endTargetDate,
                        $companyId,
                        $channelId,
                    );

                    $date = $subDeals['info_date']['original_date']['start']->format('F-Y');
                    $dates[$date] = $date;
                    $data[$bum->id]['id'] = $subDeals['data']['id'];
                    $data[$bum->id]['name'] = $subDeals['data']['name'];
                    $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                    $dataTotal[$date][] = $subDeals['data']['deals'];

                    // show data quater
                    if ($quarter1) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDateQ1,
                            $endDateQ1,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ1,
                            $endDateQ1,
                            $companyId,
                            $channelId,
                        );
                        $date = 'Quarter 1 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                        $dates[$date] = $date;
                        $data[$bum->id]['id'] = $subDeals['data']['id'];
                        $data[$bum->id]['name'] = $subDeals['data']['name'];
                        $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter2) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDateQ2,
                            $endDateQ2,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ2,
                            $endDateQ2,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 2 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$bum->id]['id'] = $subDeals['data']['id'];
                        $data[$bum->id]['name'] = $subDeals['data']['name'];
                        $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter3) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDateQ3,
                            $endDateQ3,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ3,
                            $endDateQ3,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 3 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$bum->id]['id'] = $subDeals['data']['id'];
                        $data[$bum->id]['name'] = $subDeals['data']['name'];
                        $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter4) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDateQ4,
                            $endDateQ4,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ4,
                            $endDateQ4,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 4 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$bum->id]['id'] = $subDeals['data']['id'];
                        $data[$bum->id]['name'] = $subDeals['data']['name'];
                        $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }
                    // show data quater

                    foreach ($bum->getAllChildrenSupervisors(1) ?? [] as $storeLeader) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $storeLeader,
                            'store_leader',
                            $startDate,
                            $endDate,
                            $startDateCompare,
                            $endDateCompare,
                            $startTargetDate,
                            $endTargetDate,
                            $companyId,
                            $channelId,
                        );

                        $date = $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                            'id' => $subDeals['data']['id'],
                            'name' => $subDeals['data']['name'],
                            'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                        ];

                        // show data quater
                        if ($quarter1) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $storeLeader,
                                'store_leader',
                                $startDateQ1,
                                $endDateQ1,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ1,
                                $endDateQ1,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 1 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter2) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $storeLeader,
                                'store_leader',
                                $startDateQ2,
                                $endDateQ2,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ2,
                                $endDateQ2,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 2 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter3) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $storeLeader,
                                'store_leader',
                                $startDateQ3,
                                $endDateQ3,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ3,
                                $endDateQ3,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 3 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter4) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $storeLeader,
                                'store_leader',
                                $startDateQ4,
                                $endDateQ4,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ4,
                                $endDateQ4,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 4 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }
                        // show data quater
                    }
                }
            } else {
                // default show hs
                $headSales = User::where('subscribtion_user_id', $user->subscribtion_user_id)
                    ->where('type', UserType::SUPERVISOR)
                    ->where('supervisor_type_id', 3)
                    ->when($request->supervisor_id, fn ($q) => $q->where('id', $request->supervisor_id))
                    ->get();

                foreach ($headSales as $hs) {
                    $subDeals = ApiNewReportService::subReportDealsByUser(
                        $hs,
                        'hs',
                        $startDate,
                        $endDate,
                        $startDateCompare,
                        $endDateCompare,
                        $startTargetDate,
                        $endTargetDate,
                        $companyId,
                        $channelId,
                    );

                    $date = $subDeals['info_date']['original_date']['start']->format('F-Y');
                    $dates[$date] = $date;
                    $data[$hs->id]['id'] = $subDeals['data']['id'];
                    $data[$hs->id]['name'] = $subDeals['data']['name'];
                    $data[$hs->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                    $dataTotal[$date][] = $subDeals['data']['deals'];

                    // show data quater
                    if ($quarter1) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $hs,
                            'hs',
                            $startDateQ1,
                            $endDateQ1,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ1,
                            $endDateQ1,
                            $companyId,
                            $channelId,
                        );
                        $date = 'Quarter 1 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                        $dates[$date] = $date;
                        $data[$hs->id]['id'] = $subDeals['data']['id'];
                        $data[$hs->id]['name'] = $subDeals['data']['name'];
                        $data[$hs->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter2) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $hs,
                            'hs',
                            $startDateQ2,
                            $endDateQ2,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ2,
                            $endDateQ2,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 2 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$hs->id]['id'] = $subDeals['data']['id'];
                        $data[$hs->id]['name'] = $subDeals['data']['name'];
                        $data[$hs->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter3) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $hs,
                            'hs',
                            $startDateQ3,
                            $endDateQ3,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ3,
                            $endDateQ3,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 3 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$hs->id]['id'] = $subDeals['data']['id'];
                        $data[$hs->id]['name'] = $subDeals['data']['name'];
                        $data[$hs->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter4) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $hs,
                            'hs',
                            $startDateQ4,
                            $endDateQ4,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ4,
                            $endDateQ4,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 4 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$hs->id]['id'] = $subDeals['data']['id'];
                        $data[$hs->id]['name'] = $subDeals['data']['name'];
                        $data[$hs->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }
                    // show data quater

                    foreach ($hs->getAllChildrenSupervisors(2) ?? [] as $bum) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDate,
                            $endDate,
                            $startDateCompare,
                            $endDateCompare,
                            $startTargetDate,
                            $endTargetDate,
                            $companyId,
                            $channelId,
                        );

                        $date = $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $data[$hs->id]['childs'][$subDeals['data']['id']][] = [
                            'id' => $subDeals['data']['id'],
                            'name' => $subDeals['data']['name'],
                            'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                        ];

                        // show data quater
                        if ($quarter1) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $bum,
                                'bum',
                                $startDateQ1,
                                $endDateQ1,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ1,
                                $endDateQ1,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 1 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$hs->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter2) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $bum,
                                'bum',
                                $startDateQ2,
                                $endDateQ2,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ2,
                                $endDateQ2,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 2 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$hs->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter3) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $bum,
                                'bum',
                                $startDateQ3,
                                $endDateQ3,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ3,
                                $endDateQ3,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 3 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$hs->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter4) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $bum,
                                'bum',
                                $startDateQ4,
                                $endDateQ4,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ4,
                                $endDateQ4,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 4 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$hs->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }
                        // show data quater
                    }
                }
            }
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            if ($request->user_type == 'store_leader') {
                $storeLeader = User::where('subscribtion_user_id', $user->subscribtion_user_id)
                    ->where('type', UserType::SUPERVISOR)
                    ->where('supervisor_type_id', 1)
                    ->when($request->supervisor_id, fn ($q) => $q->where('id', $request->supervisor_id))
                    ->get();

                foreach ($storeLeader as $storeLeader) {
                    $subDeals = ApiNewReportService::subReportDealsByUser(
                        $storeLeader,
                        'store_leader',
                        $startDate,
                        $endDate,
                        $startDateCompare,
                        $endDateCompare,
                        $startTargetDate,
                        $endTargetDate,
                        $companyId,
                        $channelId,
                    );

                    $date = $subDeals['info_date']['original_date']['start']->format('F-Y');
                    $dates[$date] = $date;
                    $data[$storeLeader->id]['id'] = $subDeals['data']['id'];
                    $data[$storeLeader->id]['name'] = $subDeals['data']['name'];
                    $data[$storeLeader->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                    $dataTotal[$date][] = $subDeals['data']['deals'];

                    // show data quater
                    if ($quarter1) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $storeLeader,
                            'store_leader',
                            $startDateQ1,
                            $endDateQ1,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ1,
                            $endDateQ1,
                            $companyId,
                            $channelId,
                        );
                        $date = 'Quarter 1 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                        $dates[$date] = $date;
                        $data[$storeLeader->id]['id'] = $subDeals['data']['id'];
                        $data[$storeLeader->id]['name'] = $subDeals['data']['name'];
                        $data[$storeLeader->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter2) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $storeLeader,
                            'store_leader',
                            $startDateQ2,
                            $endDateQ2,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ2,
                            $endDateQ2,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 2 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$storeLeader->id]['id'] = $subDeals['data']['id'];
                        $data[$storeLeader->id]['name'] = $subDeals['data']['name'];
                        $data[$storeLeader->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter3) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $storeLeader,
                            'store_leader',
                            $startDateQ3,
                            $endDateQ3,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ3,
                            $endDateQ3,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 3 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$storeLeader->id]['id'] = $subDeals['data']['id'];
                        $data[$storeLeader->id]['name'] = $subDeals['data']['name'];
                        $data[$storeLeader->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter4) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $storeLeader,
                            'store_leader',
                            $startDateQ4,
                            $endDateQ4,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ4,
                            $endDateQ4,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 4 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$storeLeader->id]['id'] = $subDeals['data']['id'];
                        $data[$storeLeader->id]['name'] = $subDeals['data']['name'];
                        $data[$storeLeader->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }
                    // show data quater

                    $channels = Channel::selectRaw('id,name')
                        ->whereIn('id', $storeLeader->channels->pluck('id')->all());

                    if ($channelId) {
                        $channels = $channels->where('id', $channelId);
                    }

                    $channels = $channels->get();

                    foreach ($channels as $channel) {
                        foreach ($channel->sales as $sales) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $sales,
                                'sales',
                                $startDate,
                                $endDate,
                                $startDateCompare,
                                $endDateCompare,
                                $startTargetDate,
                                $endTargetDate,
                                $companyId,
                                $channelId,
                            );

                            $date = $subDeals['info_date']['original_date']['start']->format('F-Y');
                            $data[$storeLeader->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];

                            // show data quater
                            if ($quarter1) {
                                $subDeals = ApiNewReportService::subReportDealsByUser(
                                    $sales,
                                    'sales',
                                    $startDateQ1,
                                    $endDateQ1,
                                    $startDateCompare,
                                    $endDateCompare,
                                    $startDateQ1,
                                    $endDateQ1,
                                    $companyId,
                                    $channelId,
                                );

                                $date = 'Quarter 1 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                                $data[$storeLeader->id]['childs'][$subDeals['data']['id']][] = [
                                    'id' => $subDeals['data']['id'],
                                    'name' => $subDeals['data']['name'],
                                    'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                                ];
                            }

                            if ($quarter2) {
                                $subDeals = ApiNewReportService::subReportDealsByUser(
                                    $sales,
                                    'sales',
                                    $startDateQ2,
                                    $endDateQ2,
                                    $startDateCompare,
                                    $endDateCompare,
                                    $startDateQ2,
                                    $endDateQ2,
                                    $companyId,
                                    $channelId,
                                );

                                $date = 'Quarter 2 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                                $data[$storeLeader->id]['childs'][$subDeals['data']['id']][] = [
                                    'id' => $subDeals['data']['id'],
                                    'name' => $subDeals['data']['name'],
                                    'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                                ];
                            }

                            if ($quarter3) {
                                $subDeals = ApiNewReportService::subReportDealsByUser(
                                    $sales,
                                    'sales',
                                    $startDateQ3,
                                    $endDateQ3,
                                    $startDateCompare,
                                    $endDateCompare,
                                    $startDateQ3,
                                    $endDateQ3,
                                    $companyId,
                                    $channelId,
                                );

                                $date = 'Quarter 3 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                                $data[$storeLeader->id]['childs'][$subDeals['data']['id']][] = [
                                    'id' => $subDeals['data']['id'],
                                    'name' => $subDeals['data']['name'],
                                    'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                                ];
                            }

                            if ($quarter4) {
                                $subDeals = ApiNewReportService::subReportDealsByUser(
                                    $sales,
                                    'sales',
                                    $startDateQ4,
                                    $endDateQ4,
                                    $startDateCompare,
                                    $endDateCompare,
                                    $startDateQ4,
                                    $endDateQ4,
                                    $companyId,
                                    $channelId,
                                );

                                $date = 'Quarter 4 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                                $data[$storeLeader->id]['childs'][$subDeals['data']['id']][] = [
                                    'id' => $subDeals['data']['id'],
                                    'name' => $subDeals['data']['name'],
                                    'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                                ];
                            }
                            // show data quater
                        }
                    }
                }
            } else {
                // default show bum
                $bum = User::where('subscribtion_user_id', $user->subscribtion_user_id)
                    ->where('type', UserType::SUPERVISOR)
                    ->where('supervisor_type_id', 2)
                    ->when($request->supervisor_id, fn ($q) => $q->where('id', $request->supervisor_id))
                    ->get();

                foreach ($bum as $bum) {
                    $subDeals = ApiNewReportService::subReportDealsByUser(
                        $bum,
                        'bum',
                        $startDate,
                        $endDate,
                        $startDateCompare,
                        $endDateCompare,
                        $startTargetDate,
                        $endTargetDate,
                        $companyId,
                        $channelId,
                    );
                    // dd($subDeals);

                    $date = $subDeals['info_date']['original_date']['start']->format('F-Y');
                    $dates[$date] = $date;
                    $data[$bum->id]['id'] = $subDeals['data']['id'];
                    $data[$bum->id]['name'] = $subDeals['data']['name'];
                    $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                    $dataTotal[$date][] = $subDeals['data']['deals'];

                    // show data quater
                    if ($quarter1) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDateQ1,
                            $endDateQ1,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ1,
                            $endDateQ1,
                            $companyId,
                            $channelId,
                        );
                        $date = 'Quarter 1 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                        $dates[$date] = $date;
                        $data[$bum->id]['id'] = $subDeals['data']['id'];
                        $data[$bum->id]['name'] = $subDeals['data']['name'];
                        $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter2) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDateQ2,
                            $endDateQ2,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ2,
                            $endDateQ2,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 2 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$bum->id]['id'] = $subDeals['data']['id'];
                        $data[$bum->id]['name'] = $subDeals['data']['name'];
                        $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter3) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDateQ3,
                            $endDateQ3,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ3,
                            $endDateQ3,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 3 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$bum->id]['id'] = $subDeals['data']['id'];
                        $data[$bum->id]['name'] = $subDeals['data']['name'];
                        $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }

                    if ($quarter4) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $bum,
                            'bum',
                            $startDateQ4,
                            $endDateQ4,
                            $startDateCompare,
                            $endDateCompare,
                            $startDateQ4,
                            $endDateQ4,
                            $companyId,
                            $channelId,
                        );

                        $date = 'Quarter 4 - ' . $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $dates[$date] = $date;
                        $data[$bum->id]['id'] = $subDeals['data']['id'];
                        $data[$bum->id]['name'] = $subDeals['data']['name'];
                        $data[$bum->id]['deals'][] = array_merge(['date' => $date], $subDeals['data']['deals']);

                        $dataTotal[$date][] = $subDeals['data']['deals'];
                    }
                    // show data quater

                    foreach ($bum->getAllChildrenSupervisors(1) ?? [] as $storeLeader) {
                        $subDeals = ApiNewReportService::subReportDealsByUser(
                            $storeLeader,
                            'store_leader',
                            $startDate,
                            $endDate,
                            $startDateCompare,
                            $endDateCompare,
                            $startTargetDate,
                            $endTargetDate,
                            $companyId,
                            $channelId,
                        );

                        $date = $subDeals['info_date']['original_date']['start']->format('F-Y');
                        $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                            'id' => $subDeals['data']['id'],
                            'name' => $subDeals['data']['name'],
                            'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                        ];

                        // show data quater
                        if ($quarter1) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $storeLeader,
                                'store_leader',
                                $startDateQ1,
                                $endDateQ1,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ1,
                                $endDateQ1,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 1 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter2) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $storeLeader,
                                'store_leader',
                                $startDateQ2,
                                $endDateQ2,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ2,
                                $endDateQ2,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 2 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter3) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $storeLeader,
                                'store_leader',
                                $startDateQ3,
                                $endDateQ3,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ3,
                                $endDateQ3,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 3 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }

                        if ($quarter4) {
                            $subDeals = ApiNewReportService::subReportDealsByUser(
                                $storeLeader,
                                'store_leader',
                                $startDateQ4,
                                $endDateQ4,
                                $startDateCompare,
                                $endDateCompare,
                                $startDateQ4,
                                $endDateQ4,
                                $companyId,
                                $channelId,
                            );

                            $date = 'Quarter 4 - ' . $subDeals['info_date']['original_date']['start']->format('Y');
                            $data[$bum->id]['childs'][$subDeals['data']['id']][] = [
                                'id' => $subDeals['data']['id'],
                                'name' => $subDeals['data']['name'],
                                'deals' => array_merge(['date' => $date], $subDeals['data']['deals'])
                            ];
                        }
                        // show data quater
                    }
                }
            }
        }

        $sumDataTotal = [];
        foreach ($dataTotal as $date => $dataTotals) {

            $dataTotalTargetDeals = 0;
            $dataTotalAcheivement = 0;
            foreach ($dataTotals as $total) {
                $dataTotalTargetDeals += $total['target_deals'];
                $dataTotalAcheivement += $total['value'];
            }

            $sumDataTotal[$date] = [
                'target_deals' => $dataTotalTargetDeals,
                'achievement' => $dataTotalAcheivement,
                'achievement_percentage' => $dataTotalAcheivement / $dataTotalTargetDeals,
                'revenue' => $dataTotalAcheivement - $dataTotalTargetDeals,
            ];
        }

        $data = array_values($data);
        $result = [
            'dates' => array_values($dates),
            'data' => $data,
            'data_total' => $sumDataTotal,
        ];

        $dataYearlyTarget = [];
        foreach ($data as $data) {
            $yearlyTarget = 0;
            $achievement = 0;
            $achievementPercentage = 0;
            $revenue = 0;

            $yearlyTarget = $data['deals'][0]['target_deals_ytd'];

            // kalo ada data quarter, perhitungan yearly nya ngambil dari quarter aja
            if ($quarter1 || $quarter2 || $quarter3 || $quarter4) {
                unset($data['deals'][0]);
            }

            foreach ($data['deals'] as $deals) {
                $achievement += $deals['value'];
            }

            $achievementPercentage = $yearlyTarget == 0 ? 0 : round($achievement / $yearlyTarget, 2);
            $revenue = $achievement - $yearlyTarget;

            $dataYearlyTarget[$data['id']] = [
                'yearly_target' => round($yearlyTarget),
                'achievement' => round($achievement),
                'achievement_percentage' => $achievementPercentage,
                'revenue' => round($revenue),
            ];

            if (isset($data['childs']) && count($data['childs']) > 0) {
                foreach ($data['childs'] as $childs) {
                    $yearlyTarget = 0;
                    $achievement = 0;
                    $achievementPercentage = 0;
                    $revenue = 0;

                    // dump($childs);
                    $yearlyTarget = $childs[0]['deals']['target_deals_ytd'];
                    // dump($yearlyTarget);

                    // kalo ada data quarter, perhitungan yearly nya ngambil dari quarter aja
                    if ($quarter1 || $quarter2 || $quarter3 || $quarter4) {
                        unset($childs[0]);
                    }

                    foreach (array_values($childs) as $child) {
                        $achievement += $child['deals']['value'];
                    }

                    $achievementPercentage = $yearlyTarget == 0 ? 0 : round($achievement / $yearlyTarget, 2);
                    $revenue = $achievement - $yearlyTarget;
                    $dataYearlyTarget[$child['id']] = [
                        'yearly_target' => round($yearlyTarget),
                        'achievement' => round($achievement),
                        'achievement_percentage' => $achievementPercentage,
                        'revenue' => round($revenue),
                    ];
                }
            }
        }

        $result['yearly_targets'] = $dataYearlyTarget;
        return $result;
    }



    public static function topSales(
        $user,
        $userType,
        $startDate,
        $endDate,
        $companyId = null,
        $channelId = null,
    ) {
        $target_deals = 0;
        // $companyId = $companyId ?? $user->company_id;
        $channelId = $channelId ?? null;

        if ($userType == 'store') {
            $channelId = $user->id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'subscribtion_user')->where('targets.model_id', $companyId)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startDate)->whereDate('reports.end_date', '<=', $endDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startDate)->whereDate('reports.end_date', '<=', $endDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            // $userType = 'sales';

            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'user')->where('targets.model_id', $user->id)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startDate)->whereDate('reports.end_date', '<=', $endDate)->first()?->target ?? 0;
        }

        // if ($user->is_director || $user->is_digital_marketing || $user->is_supervisor || $userType == 'store') {
        if ($channelId) {
            // $target_deals = DB::table('targets')->join('reports', 'reports.id', '=', 'targets.report_id')->selectRaw('SUM(target) as target')->where('targets.model_type', 'channel')->where('targets.model_id', $channelId)->where('targets.type', 0)->whereDate('reports.start_date', '>=', $startDate)->whereDate('reports.end_date', '<=', $endDate)->first()?->target ?? 0;
        }
        // }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withSum([
                    'channelOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

            $result = $query->first();

            $data = [
                'value' => potongPPN($result->total_deals ?? 0),
                'target_deals' => (int)$target_deals ?? 0,
            ];
        } else if (in_array($userType, ['director'])) {
            $summary_deals = Order::selectRaw('SUM(total_price) as total_price')->where('subscribtion_user_id', $user->subscribtion_user_id)->whereDeal($startDate, $endDate)->when($channelId, fn ($q) => $q->where('channel_id', $channelId))->first()?->total_price ?? 0;

            $data = [
                'value' => potongPPN($summary_deals),
                'target_deals' => (int)$target_deals ?? 0,
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->with('leadUsers', function ($q) use ($channelId, $startDate, $endDate) {
                    $q->has('orders')
                        ->withSum([
                            'orders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price');
                });

            $result = $query->first();

            $summary_deals = $result->leadUsers?->sum('total_deals') ?? 0;

            $data = [
                'value' => potongPPN($summary_deals),
                'target_deals' => (int)$target_deals ?? 0,
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

            $result = $query->first();

            $data = [
                'value' => potongPPN($result->total_deals ?? 0),
                'target_deals' => (int)$target_deals ?? 0,
            ];
        }

        return [
            'data' => $data,
        ];
    }

    public static function dealsPercentage(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = self::getDates($request->start_date, $request->end_date);

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];
        }

        $infoDate = [
            'start' => $startDate,
            'end' => $endDate,
        ];

        $userType = null;

        if ($request->user_type == 'store') {
            $userType = 'store';
            $user = Channel::find($request->user_id);
        } else {
            $user = $request->user_id ? User::find($request->user_id) : user();
        }

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($userType == 'store') {
            $channelId = $request->user_id ?? null;
        } else if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'store_leader';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }
        } else if ($user->is_sales) {
            $userType = 'sales';
        }

        if ($userType == 'store') {
            $query = Channel::selectRaw(self::CHANNEL_COLUMNS)->where('id', $channelId)
                ->withCount([
                    'channelOrders as total_orders_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate)
                            ->where('status', '!=', 6);

                        if ($channelId) $q->where('channel_id', $channelId);

                        if ($productBrandId = request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                        }
                    }
                ])
                ->withCount([
                    'channelOrders as total_cancelled_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate)
                            ->where('status', 5);

                        if ($channelId) $q->where('channel_id', $channelId);

                        if ($productBrandId = request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                        }
                    }
                ])
                // ->withCount([
                //     'channelOrders as total_quotation_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                //         $q->whereNotDeal($startDate, $endDate);

                //         if ($channelId) $q->where('channel_id', $channelId);

                //         if ($productBrandId = request()->product_brand_id) {
                //             $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                //         }
                //     }
                // ])
                ->withCount([
                    'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);

                        if ($channelId) $q->where('channel_id', $channelId);

                        if ($productBrandId = request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                        }
                    }
                ]);

            $result = $query->first();

            $cancelledPercentage = $result->total_orders_transaction <= 0 ? 0 : (($result->total_cancelled_transaction / $result->total_orders_transaction) * 100);
            $dealsPercentage = $result->total_orders_transaction <= 0 ? 0 : (($result->total_deals_transaction / $result->total_orders_transaction) * 100);

            $data = [
                'orders' => [
                    'total_transaction' => $result->total_orders_transaction,
                ],
                'cancelled' => [
                    'total_transaction' => $result->total_cancelled_transaction,
                    'percentage' => round($cancelledPercentage ?? 0, 2)
                ],
                // 'quotation' => [
                //     'total_transaction' => $total_quotation_transaction,
                // ],
                'deals' => [
                    'total_transaction' => $result->total_deals_transaction,
                    'percentage' => round($dealsPercentage ?? 0, 2)
                ],
            ];
        } else if (in_array($userType, ['director'])) {
            $total_orders_transaction = Order::where('subscribtion_user_id', $user->subscribtion_user_id)
                ->whereCreatedAtRange($startDate, $endDate)
                ->where('status', '!=', 6)
                ->when($channelId, fn ($q) => $q->where('channel_id', $channelId))
                ->count() ?? 0;

            $total_cancelled_transaction = Order::where('subscribtion_user_id', $user->subscribtion_user_id)
                ->whereCreatedAtRange($startDate, $endDate)
                ->where('status', 5)
                ->when($channelId, fn ($q) => $q->where('channel_id', $channelId))
                ->count() ?? 0;

            // $total_quotation_transaction = Order::where('company_id', $companyId)
            //     ->whereNotDeal($startDate, $endDate)
            //     ->when($channelId, fn ($q) => $q->where('channel_id', $channelId))
            //     ->count() ?? 0;

            $total_deals_transaction = Order::where('subscribtion_user_id', $user->subscribtion_user_id)
                ->whereDeal($startDate, $endDate)
                ->when($channelId, fn ($q) => $q->where('channel_id', $channelId))
                ->count() ?? 0;

            $cancelledPercentage = $total_orders_transaction <= 0 ? 0 : (($total_cancelled_transaction / $total_orders_transaction) * 100);
            $dealsPercentage = $total_orders_transaction <= 0 ? 0 : (($total_deals_transaction / $total_orders_transaction) * 100);

            $data = [
                'orders' => [
                    'total_transaction' => $total_orders_transaction,
                ],
                'cancelled' => [
                    'total_transaction' => $total_cancelled_transaction,
                    'percentage' => round($cancelledPercentage ?? 0, 2)
                ],
                // 'quotation' => [
                //     'total_transaction' => $total_quotation_transaction,
                // ],
                'deals' => [
                    'total_transaction' => $total_deals_transaction,
                    'percentage' => round($dealsPercentage ?? 0, 2)
                ],
            ];
        } else if (in_array($userType, self::SUPERVISOR_TYPE)) {
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->with('leadUsers', function ($q) use ($channelId, $startDate, $endDate) {
                    $q->has('orders')
                        ->withCount([
                            'orders as total_orders_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereCreatedAtRange($startDate, $endDate)
                                    ->where('status', '!=', 6);

                                if ($channelId) $q->where('channel_id', $channelId);

                                if ($productBrandId = request()->product_brand_id) {
                                    $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                                }
                            }
                        ])
                        ->withCount([
                            'orders as total_cancelled_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereCreatedAtRange($startDate, $endDate)
                                    ->where('status', 5);

                                if ($channelId) $q->where('channel_id', $channelId);

                                if ($productBrandId = request()->product_brand_id) {
                                    $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                                }
                            }
                        ])
                        // ->withCount([
                        //     'orders as total_quotation_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        //         $q->whereNotDeal($startDate, $endDate);

                        //         if ($channelId) $q->where('channel_id', $channelId);

                        //         if ($productBrandId = request()->product_brand_id) {
                        //             $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                        //         }
                        //     }
                        // ])
                        ->withCount([
                            'orders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);

                                if ($productBrandId = request()->product_brand_id) {
                                    $q->whereHas('activityBrandValues', fn ($q2) => $q2->where('product_brand_id', $productBrandId));
                                }
                            }
                        ]);
                });

            if ($request->name) {
                $query = $query->where('name', 'like', '%' . $request->name . '%');
            }

            $result = $query->first();

            $total_orders_transaction = $result->leadUsers?->sum('total_orders_transaction') ?? 0;
            $total_cancelled_transaction = $result->leadUsers?->sum('total_cancelled_transaction') ?? 0;
            // $total_quotation_transaction = $result->leadUsers?->sum('total_quotation_transaction') ?? 0;
            $total_deals_transaction = $result->leadUsers?->sum('total_deals_transaction') ?? 0;

            $cancelledPercentage = $total_orders_transaction <= 0 ? 0 : (($total_cancelled_transaction / $total_orders_transaction) * 100);
            $dealsPercentage = $total_orders_transaction <= 0 ? 0 : (($total_deals_transaction / $total_orders_transaction) * 100);

            $data = [
                'orders' => [
                    'total_transaction' => $total_orders_transaction,
                ],
                'cancelled' => [
                    'total_transaction' => $total_cancelled_transaction,
                    'percentage' => round($cancelledPercentage ?? 0, 2)
                ],
                // 'quotation' => [
                //     'total_transaction' => $total_quotation_transaction,
                // ],
                'deals' => [
                    'total_transaction' => $total_deals_transaction,
                    'percentage' => round($dealsPercentage ?? 0, 2)
                ],
            ];
        } else {
            // else sales
            $query = User::selectRaw(self::USER_COLUMNS)->where('id', $user->id)
                ->withCount([
                    'userOrders as total_orders_transaction' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate)
                            ->where('status', '!=', 6)
                            ->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ])
                ->withCount([
                    'userOrders as total_cancelled_transaction' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate)
                            ->where('status', 5)
                            ->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ])
                // ->withCount([
                //     'userOrders as total_quotation_transaction' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                //         $q->whereNotDeal($startDate, $endDate)
                //             ->where('channel_id', $user->channel_id);
                //         if ($channelId) $q->where('channel_id', $channelId);
                //     }
                // ])
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($user, $channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate)
                            ->where('channel_id', $user->channel_id);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ]);

            $result = $query->first();


            $cancelledPercentage = $result->total_orders_transaction <= 0 ? 0 : (($result->total_cancelled_transaction / $result->total_orders_transaction) * 100);
            $dealsPercentage = $result->total_orders_transaction <= 0 ? 0 : (($result->total_deals_transaction / $result->total_orders_transaction) * 100);

            $data = [
                'orders' => [
                    'total_transaction' => $result->total_orders_transaction,
                ],
                'cancelled' => [
                    'total_transaction' => $result->total_cancelled_transaction,
                    'percentage' => round($cancelledPercentage ?? 0, 2)
                ],
                // 'quotation' => [
                //     'total_transaction' => $result->total_quotation_transaction,
                // ],
                'deals' => [
                    'total_transaction' => $result->total_deals_transaction,
                    'percentage' => round($dealsPercentage ?? 0, 2)
                ],
            ];
        }

        return [
            'data' => $data,
            'info_date' => $infoDate,
        ];
    }
}
