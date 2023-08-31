<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\ActivityStatus;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\NewTargetType;
use App\Enums\OrderPaymentStatus;
use App\Enums\UserType;
use App\Models\Channel;
use App\Models\Lead;
use App\Models\Order;
use App\Models\ProductBrand;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewReportControllerBackup extends BaseApiController
{
    protected function getDates($start_date, $end_date)
    {
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

    public function index(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = $this->getDates($request->start_date, $request->end_date);

            $startTargetDate = $dates['startTargetDate'];
            $endTargetDate = $dates['endTargetDate'];

            $startDate = $dates['startDate'];
            $endDate = $dates['endDate'];

            $startDateCompare = $dates['startDateCompare'];
            $endDateCompare = $dates['endDateCompare'];
        }
        // dump($startDateCompare);
        // dd($endDateCompare);

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

        $user = user();

        $target_deals = 0;

        $userType = null;

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($user->is_director) {
            $userType = 'director';

            // $target_deals = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'company')->where('model_id', $companyId)->where('type', 0)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;
            $target_deals = DB::table('targets')->selectRaw('SUM(target) as target')->where('type', 0)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_activities = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'company')->where('model_id', $companyId)->where('type', 7)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;
            $target_activities = DB::table('targets')->selectRaw('SUM(target) as target')->where('type', 7)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

            // $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'company')->where('model_id', $companyId)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
            $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'sl';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }

            $target_deals = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', 0)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

            $target_activities = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', 7)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

            $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        } else if ($user->is_sales) {
            $userType = 'sales';

            $target_deals = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', 0)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

            $target_activities = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', 7)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

            $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
        }

        if ($user->is_director || $user->is_supervisor) {
            if ($channelId) {
                $target_deals = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'channel')->where('model_id', $channelId)->where('type', 0)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

                $target_activities = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'channel')->where('model_id', $channelId)->where('type', 7)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

                $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'channel')->where('model_id', $channelId)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;
            }
        }

        if (in_array($userType, ['director', 'hs'])) {
            $query = User::selectRaw("id, name, type")
                ->selectRaw("(
                    SELECT
                        count(DISTINCT customer_id)
                    from
                        leads
                        JOIN customers on customers.id = leads.customer_id
                    WHERE
                        date(customers.created_at) >= '" . $startDate . "'
                        and date(customers.created_at) <= '" . $endDate . "'
                        and user_id = users.id
                        " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                    ) as total_leads
                ")
                // ->where('company_id', $companyId)
                ->where('type', 2)
                // ->withCount(['leads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                //     $q->whereHas('customer', fn ($customer) => $customer->whereCreatedAtRange($startDate, $endDate));

                //     if ($channelId) $q->where('channel_id', $channelId);

                //     if (request()->product_brand_id) {
                //         $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                //             $q2->where('product_brand_id', request()->product_brand_id);
                //         });
                //     }
                // }])
                ->withCount(['leads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as active_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_active_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
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
                }])
                ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withSum([
                    'activityBrandValues as total_estimation' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate);
                        // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        if ($channelId) $q->whereHas('activity', fn ($q2) => $q2->where('channel_id', $channelId));

                        if (request()->product_brand_id) {
                            $q->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'estimated_value')
                ->withSum([
                    'activityBrandValues as compare_total_estimation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        if ($channelId) $q->whereHas('activity', fn ($q2) => $q2->where('channel_id', $channelId));

                        if (request()->product_brand_id) {
                            $q->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'estimated_value')
                ->withSum(['userOrders as total_quotation' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                    $q->whereDoesntHave('orderPayments');
                    $q->whereNotIn('status', [5, 6]);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereDoesntHave('orderPayments');
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereDeal($startDateCompare, $endDateCompare);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');
            // ->withSum([
            //     'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate)
            //             ->whereNotNull('interior_design_id');
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price')
            // ->withCount([
            //     'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate)
            //             ->whereNotNull('interior_design_id');
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price');

            $result = $query->get();

            $data = [];

            $summary_new_leads = 0;
            $summary_compare_new_leads = 0;
            $summary_active_leads = 0;
            $summary_compare_active_leads = 0;
            $summary_total_activities = 0;
            $summary_compare_total_activities = 0;
            $summary_hot_activities = 0;
            $summary_warm_activities = 0;
            $summary_cold_activities = 0;
            $summary_estimation = 0;
            $summary_compare_estimation = 0;
            $summary_quotation = 0;
            $summary_compare_quotation = 0;
            $summary_deals = 0;
            $summary_total_deals_transaction = 0;
            $summary_compare_deals = 0;
            // $summary_interior_design = 0;
            // $summary_interior_design_total_transaction = 0;
            foreach ($result as $sales) {

                $pbs = $this->getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare);

                $summary_new_leads += (int)$sales->total_leads ?? 0;
                $summary_compare_new_leads += (int)$sales->compare_total_leads ?? 0;
                $summary_active_leads += (int)$sales->active_leads ?? 0;
                $summary_compare_active_leads += (int)$sales->compare_active_leads ?? 0;
                $summary_total_activities += (int)$sales->total_activities ?? 0;
                $summary_compare_total_activities += (int)$sales->compare_total_activities ?? 0;
                $summary_hot_activities += (int)$sales->hot_activities ?? 0;
                $summary_warm_activities += (int)$sales->warm_activities ?? 0;
                $summary_cold_activities += (int)$sales->cold_activities ?? 0;
                $summary_estimation += $pbs['estimated_value'];
                $summary_compare_estimation += $pbs['compare_estimated_value'];
                // $summary_estimation += (int)$sales->total_estimation ?? 0;
                // $summary_compare_estimation += (int)$sales->compare_total_estimation ?? 0;
                $summary_quotation += (int)$sales->total_quotation ?? 0;
                $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
                $summary_deals += (int)$sales->total_deals ?? 0;
                $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
                $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
                // $summary_interior_design += (int)$sales->interior_design ?? 0;
                // $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
            }

            $data = [
                'new_leads' => [
                    'value' => $summary_new_leads,
                    'compare' => $summary_compare_new_leads,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
                'active_leads' => [
                    'value' => $summary_active_leads,
                    'compare' => $summary_compare_active_leads,
                ],
                'follow_up' => [
                    'total_activities' => [
                        'value' => $summary_total_activities,
                        'compare' => $summary_compare_total_activities,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                    'hot_activities' => $summary_hot_activities,
                    'warm_activities' => $summary_warm_activities,
                    'cold_activities' => $summary_cold_activities,
                ],
                'estimation' => [
                    'value' => $summary_estimation,
                    'compare' => $summary_compare_estimation,
                ],
                'quotation' => [
                    'value' => $summary_quotation,
                    'compare' => $summary_compare_quotation,
                ],
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
                // 'retail' => [
                //     'value' => potongPPN($summary_deals - $summary_interior_design),
                //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                // ],
            ];

            return response()->json(array_merge($data, $infoDate));
        } else if (in_array($userType, ['sl', 'bum'])) {
            $query = User::selectRaw('id, name, type')
                ->selectRaw("(
                    SELECT
                        count(DISTINCT customer_id)
                    from
                        leads
                        JOIN customers on customers.id = leads.customer_id
                    WHERE
                        date(customers.created_at) >= '" . $startDate . "'
                        and date(customers.created_at) <= '" . $endDate . "'
                        and user_id = users.id
                        " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                    ) as total_leads
                ")
                ->whereIn('channel_id', $user->channels->pluck('id')->all())
                ->where('type', 2)
                // ->withCount(['leads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                //     $q->whereHas('customer', fn ($customer) => $customer->whereCreatedAtRange($startDate, $endDate));

                //     if ($channelId) $q->where('channel_id', $channelId);

                //     if (request()->product_brand_id) {
                //         $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                //             $q2->where('product_brand_id', request()->product_brand_id);
                //         });
                //     }
                // }])
                ->withCount(['leads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as active_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_active_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
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
                }])
                ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withSum([
                    'activityBrandValues as total_estimation' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate);
                        // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        if ($channelId) $q->whereHas('activity', fn ($q2) => $q2->where('channel_id', $channelId));

                        if (request()->product_brand_id) {
                            $q->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'estimated_value')
                ->withSum([
                    'activityBrandValues as compare_total_estimation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        if ($channelId) $q->whereHas('activity', fn ($q2) => $q2->where('channel_id', $channelId));

                        if (request()->product_brand_id) {
                            $q->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'estimated_value')
                ->withSum(['userOrders as total_quotation' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                    $q->whereDoesntHave('orderPayments');
                    $q->whereNotIn('status', [5, 6]);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereDoesntHave('orderPayments');
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereDeal($startDateCompare, $endDateCompare);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');
            // ->withSum([
            //     'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate)
            //             ->whereNotNull('interior_design_id');
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price')
            // ->withCount([
            //     'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate)
            //             ->whereNotNull('interior_design_id');
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price');

            $result = $query->get();

            $data = [];

            $summary_new_leads = 0;
            $summary_compare_new_leads = 0;
            $summary_active_leads = 0;
            $summary_compare_active_leads = 0;
            $summary_total_activities = 0;
            $summary_compare_total_activities = 0;
            $summary_hot_activities = 0;
            $summary_warm_activities = 0;
            $summary_cold_activities = 0;
            $summary_estimation = 0;
            $summary_compare_estimation = 0;
            $summary_quotation = 0;
            $summary_compare_quotation = 0;
            $summary_deals = 0;
            $summary_total_deals_transaction = 0;
            $summary_compare_deals = 0;
            // $summary_interior_design = 0;
            // $summary_interior_design_total_transaction = 0;
            foreach ($result as $sales) {
                $pbs = $this->getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare);

                $summary_new_leads += (int)$sales->total_leads ?? 0;
                $summary_compare_new_leads += (int)$sales->compare_total_leads ?? 0;
                $summary_active_leads += (int)$sales->active_leads ?? 0;
                $summary_compare_active_leads += (int)$sales->compare_active_leads ?? 0;
                $summary_total_activities += (int)$sales->total_activities ?? 0;
                $summary_compare_total_activities += (int)$sales->compare_total_activities ?? 0;
                $summary_hot_activities += (int)$sales->hot_activities ?? 0;
                $summary_warm_activities += (int)$sales->warm_activities ?? 0;
                $summary_cold_activities += (int)$sales->cold_activities ?? 0;
                $summary_estimation += $pbs['estimated_value'];
                $summary_compare_estimation += $pbs['compare_estimated_value'];
                // $summary_estimation += (int)$sales->total_estimation ?? 0;
                // $summary_compare_estimation += (int)$sales->compare_total_estimation ?? 0;
                $summary_quotation += (int)$sales->total_quotation ?? 0;
                $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
                $summary_deals += (int)$sales->total_deals ?? 0;
                $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
                $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
                // $summary_interior_design += (int)$sales->interior_design ?? 0;
                // $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
            }

            $data = [
                'new_leads' => [
                    'value' => $summary_new_leads,
                    'compare' => $summary_compare_new_leads,
                    'target_leads' => (int)$target_leads ?? 0,
                ],
                'active_leads' => [
                    'value' => $summary_active_leads,
                    'compare' => $summary_compare_active_leads,
                ],
                'follow_up' => [
                    'total_activities' => [
                        'value' => $summary_total_activities,
                        'compare' => $summary_compare_total_activities,
                        'target_activities' => (int)$target_activities ?? 0,
                    ],
                    'hot_activities' => $summary_hot_activities,
                    'warm_activities' => $summary_warm_activities,
                    'cold_activities' => $summary_cold_activities,
                ],
                'estimation' => [
                    'value' => $summary_estimation,
                    'compare' => $summary_compare_estimation,
                ],
                'quotation' => [
                    'value' => $summary_quotation,
                    'compare' => $summary_compare_quotation,
                ],
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
                // 'retail' => [
                //     'value' => potongPPN($summary_deals - $summary_interior_design),
                //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                // ],
            ];

            return response()->json(array_merge($data, $infoDate));
        }

        // else sales
        $query = User::selectRaw('id,name,type')->where('id', $user->id)
            ->selectRaw("(
                SELECT
                    count(DISTINCT customer_id)
                from
                    leads
                    JOIN customers on customers.id = leads.customer_id
                WHERE
                    date(customers.created_at) >= '" . $startDate . "'
                    and date(customers.created_at) <= '" . $endDate . "'
                    and user_id = users.id
                    " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                ) as total_leads
            ")
            // ->withCount(['leads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
            //     $q->whereHas('customer', fn ($customer) => $customer->whereCreatedAtRange($startDate, $endDate));

            //     if ($channelId) $q->where('channel_id', $channelId);

            //     if (request()->product_brand_id) {
            //         $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
            //             $q2->where('product_brand_id', request()->product_brand_id);
            //         });
            //     }
            // }])
            ->withCount(['leads as compare_total_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as active_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as compare_active_leads' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
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
            }])
            ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withSum([
                'activityBrandValues as total_estimation' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                    if ($channelId) $q->whereHas('activity', fn ($q2) => $q2->where('channel_id', $channelId));

                    if (request()->product_brand_id) {
                        $q->where('product_brand_id', request()->product_brand_id);
                    }
                }
            ], 'estimated_value')
            ->withSum([
                'activityBrandValues as compare_total_estimation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                    if ($channelId) $q->whereHas('activity', fn ($q2) => $q2->where('channel_id', $channelId));

                    if (request()->product_brand_id) {
                        $q->where('product_brand_id', request()->product_brand_id);
                    }
                }
            ], 'estimated_value')
            ->withSum(['userOrders as total_quotation' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate);
                // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                $q->whereDoesntHave('orderPayments');
                $q->whereNotIn('status', [5, 6]);
                if ($channelId) $q->where('channel_id', $channelId);
            }], 'total_price')
            ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                $q->whereDoesntHave('orderPayments');
                $q->whereNotIn('status', [5, 6]);
                $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                if ($channelId) $q->where('channel_id', $channelId);
            }], 'total_price')
            ->withCount([
                'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereDeal($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereDeal($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereDeal($startDateCompare, $endDateCompare);
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price');
        // ->withSum([
        //     'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
        //         $q->whereDeal($startDate, $endDate)
        //             ->whereNotNull('interior_design_id');
        //         if ($channelId) $q->where('channel_id', $channelId);
        //     }
        // ], 'total_price')
        // ->withCount([
        //     'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
        //         $q->whereDeal($startDate, $endDate)
        //             ->whereNotNull('interior_design_id');
        //         if ($channelId) $q->where('channel_id', $channelId);
        //     }
        // ], 'total_price');

        $result = $query->first();

        $pbs = $this->getPbs($result, $startDate, $endDate, $startDateCompare, $endDateCompare);

        $data = [
            'new_leads' => [
                'value' => (int)$result->total_leads ?? 0,
                'compare' => (int)$result->compare_total_leads ?? 0,
                'target_leads' => (int)$target_leads ?? 0,
            ],
            'active_leads' => [
                'value' => (int)$result->active_leads ?? 0,
                'compare' => (int)$result->compare_active_leads ?? 0,
            ],
            'follow_up' => [
                'total_activities' => [
                    'value' => (int)$result->total_activities ?? 0,
                    'compare' => (int)$result->compare_total_activities ?? 0,
                    'target_activities' => (int)$target_activities ?? 0,
                ],
                'hot_activities' => (int)$result->hot_activities ?? 0,
                'warm_activities' => (int)$result->warm_activities ?? 0,
                'cold_activities' => (int)$result->cold_activities ?? 0,
            ],
            'estimation' => [
                'value' => $pbs['estimated_value'],
                'compare' => $pbs['compare_estimated_value'],
                // 'value' => (int)$result->total_estimation ?? 0,
                // 'compare' => (int)$result->compare_total_estimation ?? 0,
            ],
            'quotation' => [
                'value' => (int) $result->total_quotation ?? 0,
                'compare' => (int) $result->compare_total_quotation ?? 0,
            ],
            'deals' => [
                'value' => potongPPN($result->total_deals),
                'compare' => potongPPN($result->compare_total_deals),
                'total_transaction' => $result->total_deals_transaction,
                'target_deals' => (int)$target_deals ?? 0,
            ],
            // 'interior_design' => [
            //     'value' => potongPPN($result->interior_design),
            //     'total_transaction' => (int)$result->interior_design_total_transaction ?? 0,
            // ],
            // 'retail' => [
            //     'value' => potongPPN($result->total_deals - $result->interior_design),
            //     'total_transaction' => (int)($result->total_deals_transaction - $result->interior_design_total_transaction) ?? 0,
            // ],
        ];

        return response()->json(array_merge($data, $infoDate));
    }

    public function details(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $startDateCompare = Carbon::now()->subMonth()->startOfMonth();
        $endDateCompare = Carbon::now()->subMonth()->endOfMonth();
        // $targetDate = Carbon::now()->startOfDay();

        $startTargetDate = Carbon::now()->startOfMonth();
        $endTargetDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $dates = $this->getDates($request->start_date, $request->end_date);

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

        $user = user();
        $userType = null;

        // $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

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

        if (in_array($userType, ['director', 'hs'])) {
            if ($request->user_type == 'bum') {
                $query = User::selectRaw('id, name, type')
                    ->where('type', UserType::SUPERVISOR)
                    ->where('supervisor_type_id', 2);
                // ->where('company_id', $companyId);

                $query = $query->selectRaw("(SELECT SUM(target) FROM new_targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(start_date) >= '" . $startTargetDate . "' AND DATE(end_date) <= '" . $endTargetDate . "') as target_leads");

                $query = $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=7 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_activities");

                // if ($startDate->month == $endDate->month) {
                $query = $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_deals");
                // }
                $query = $query->with(['channels' => function ($channel) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $channel
                        // ->where('company_id', $companyId)
                        ->with(['sales' => function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                            $q->selectRaw("
                            id,name,type,channel_id,
                            (
                                SELECT
                                    count(DISTINCT customer_id)
                                from
                                    leads
                                    JOIN customers on customers.id = leads.customer_id
                                WHERE
                                    date(customers.created_at) >= '" . $startDate . "'
                                    and date(customers.created_at) <= '" . $endDate . "'
                                    and user_id = users.id
                                    " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                                ) as total_leads
                            ")
                                ->withCount(['leads as compare_total_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                                    if (request()->product_brand_id) {
                                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['leads as active_leads' => function ($q) use ($startDate, $endDate) {
                                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                                    if (request()->product_brand_id) {
                                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['leads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                                    if (request()->product_brand_id) {
                                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['userActivities as total_activities' => function ($q) use ($startDate, $endDate) {
                                    // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                                    $q->whereCreatedAtRange($startDate, $endDate);
                                    // });

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['userActivities as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                                    // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                                    // });

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                                    $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                                    if ($channelId) $q->where('channel_id', $channelId);

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                                    $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                                    if ($channelId) $q->where('channel_id', $channelId);

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                                    $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                                    if ($channelId) $q->where('channel_id', $channelId);

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withSum([
                                    'activityBrandValues as total_estimation' => function ($q) use ($startDate, $endDate) {
                                        $q->whereCreatedAtRange($startDate, $endDate);
                                        // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                                        if (request()->product_brand_id) {
                                            $q->where('product_brand_id', request()->product_brand_id);
                                        }
                                    }
                                ], 'estimated_value')
                                ->withSum([
                                    'activityBrandValues as compare_total_estimation' => function ($q) use ($startDateCompare, $endDateCompare) {
                                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                                        // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                                        if (request()->product_brand_id) {
                                            $q->where('product_brand_id', request()->product_brand_id);
                                        }
                                    }
                                ], 'estimated_value')
                                ->withSum(['userOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                                    $q->whereCreatedAtRange($startDate, $endDate);
                                    // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                                    $q->whereDoesntHave('orderPayments');
                                    $q->whereNotIn('status', [5, 6]);
                                }], 'total_price')
                                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                                    $q->whereDoesntHave('orderPayments');
                                    $q->whereNotIn('status', [5, 6]);
                                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                                }], 'total_price')
                                ->withCount([
                                    'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                        $q->whereDeal($startDate, $endDate);
                                        if ($channelId) $q->where('channel_id', $channelId);
                                    }
                                ], 'total_price')
                                ->withSum([
                                    'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                                        $q->whereDeal($startDate, $endDate);
                                        if ($channelId) $q->where('channel_id', $channelId);
                                    }
                                ], 'total_price')
                                ->withSum([
                                    'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                                        $q->whereDeal($startDateCompare, $endDateCompare);
                                        if ($channelId) $q->where('channel_id', $channelId);
                                    }
                                ], 'total_price');
                            // ->withSum([
                            //     'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                            //         $q->whereDeal($startDate, $endDate)
                            //             ->whereNotNull('interior_design_id');
                            //         if ($channelId) $q->where('channel_id', $channelId);
                            //     }
                            // ], 'total_price')
                            // ->withCount([
                            //     'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                            //         $q->whereDeal($startDate, $endDate)
                            //             ->whereNotNull('interior_design_id');
                            //         if ($channelId) $q->where('channel_id', $channelId);
                            //     }
                            // ], 'total_price');
                        }]);

                    if ($channelId) {
                        $channel = $channel->where('id', $channelId);
                    }
                }]);

                if ($channelId) {
                    $query = $query->whereHas('channels', fn ($q) => $q->where('id', $channelId));
                }

                if ($request->name) {
                    $query = $query->where('name', 'like', '%' . $request->name . '%');
                }

                $result = $query->get();

                $data = [];
                $summary_new_leads = 0;
                $summary_compare_new_leads = 0;
                $summary_active_leads = 0;
                $summary_compare_active_leads = 0;
                $summary_total_activities = 0;
                $summary_compare_total_activities = 0;
                $summary_hot_activities = 0;
                $summary_warm_activities = 0;
                $summary_cold_activities = 0;
                $summary_estimation = 0;
                $summary_compare_estimation = 0;
                $summary_quotation = 0;
                $summary_compare_quotation = 0;
                $summary_deals = 0;
                $summary_total_deals_transaction = 0;
                $summary_compare_deals = 0;
                // $summary_interior_design = 0;
                // $summary_interior_design_total_transaction = 0;

                foreach ($result as $bum) {

                    $channel_new_leads = 0;
                    $channel_compare_new_leads = 0;
                    $channel_active_leads = 0;
                    $channel_compare_active_leads = 0;
                    $channel_total_activities = 0;
                    $channel_compare_total_activities = 0;
                    $channel_hot_activities = 0;
                    $channel_warm_activities = 0;
                    $channel_cold_activities = 0;
                    $channel_estimation = 0;
                    $channel_compare_estimation = 0;
                    $channel_quotation = 0;
                    $channel_compare_quotation = 0;
                    $channel_deals = 0;
                    $channel_total_deals_transaction = 0;
                    $channel_compare_deals = 0;
                    // $channel_interior_design = 0;
                    // $channel_interior_design_total_transaction = 0;
                    foreach ($bum->channels as $channel) {

                        $sales_new_leads = 0;
                        $sales_compare_new_leads = 0;
                        $sales_active_leads = 0;
                        $sales_compare_active_leads = 0;
                        $sales_total_activities = 0;
                        $sales_compare_total_activities = 0;
                        $sales_hot_activities = 0;
                        $sales_warm_activities = 0;
                        $sales_cold_activities = 0;
                        $sales_estimation = 0;
                        $sales_compare_estimation = 0;
                        $sales_quotation = 0;
                        $sales_compare_quotation = 0;
                        $sales_deals = 0;
                        $sales_total_deals_transaction = 0;
                        $sales_compare_deals = 0;
                        // $sales_interior_design = 0;
                        // $sales_interior_design_total_transaction = 0;
                        foreach ($channel->sales as $sales) {
                            $pbs = $this->getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare, $channelId);

                            $sales_new_leads += $sales->total_leads ?? 0;
                            $sales_compare_new_leads += $sales->compare_total_leads ?? 0;
                            $sales_active_leads += $sales->active_leads ?? 0;
                            $sales_compare_active_leads += $sales->compare_active_leads ?? 0;
                            $sales_total_activities += $sales->total_activities ?? 0;
                            $sales_compare_total_activities += $sales->compare_total_activities ?? 0;
                            $sales_hot_activities += $sales->hot_activities ?? 0;
                            $sales_warm_activities += $sales->warm_activities ?? 0;
                            $sales_cold_activities += $sales->cold_activities ?? 0;
                            $sales_estimation += $pbs['estimated_value'];
                            $sales_compare_estimation += $pbs['compare_estimated_value'];
                            // $sales_estimation += $sales->total_estimation ?? 0;
                            // $sales_compare_estimation += $sales->compare_total_estimation ?? 0;
                            $sales_quotation += $sales->total_quotation ?? 0;
                            $sales_compare_quotation += $sales->compare_total_quotation ?? 0;
                            $sales_deals += $sales->total_deals ?? 0;
                            $sales_total_deals_transaction += $sales->total_deals_transaction ?? 0;
                            $sales_compare_deals += $sales->compare_total_deals ?? 0;
                            // $sales_interior_design += $sales->interior_design ?? 0;
                            // $sales_interior_design_total_transaction += $sales->interior_design_total_transaction ?? 0;
                        }

                        $channel_new_leads += $sales_new_leads ?? 0;
                        $channel_compare_new_leads += $sales_compare_new_leads ?? 0;
                        $channel_active_leads += $sales_active_leads ?? 0;
                        $channel_compare_active_leads += $sales_compare_active_leads ?? 0;
                        $channel_total_activities += $sales_total_activities ?? 0;
                        $channel_compare_total_activities += $sales_compare_total_activities ?? 0;
                        $channel_hot_activities += $sales_hot_activities ?? 0;
                        $channel_warm_activities += $sales_warm_activities ?? 0;
                        $channel_cold_activities += $sales_cold_activities ?? 0;
                        $channel_estimation += $sales_estimation ?? 0;
                        $channel_compare_estimation += $sales_compare_estimation ?? 0;
                        $channel_quotation += $sales_quotation ?? 0;
                        $channel_compare_quotation += $sales_compare_quotation ?? 0;
                        $channel_deals += $sales_deals ?? 0;
                        $channel_total_deals_transaction += $sales_total_deals_transaction ?? 0;
                        $channel_compare_deals += $sales_compare_deals ?? 0;
                        // $channel_interior_design += $sales_interior_design ?? 0;
                        // $channel_interior_design_total_transaction += $sales_interior_design_total_transaction ?? 0;
                    }

                    $data['details'][] = [
                        'id' => $bum->id,
                        'name' => $bum->name,
                        'new_leads' => [
                            'value' => (int)$channel_new_leads ?? 0,
                            'compare' => (int)$channel_compare_new_leads ?? 0,
                            'target_leads' => (int)$bum->target_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => (int)$channel_active_leads ?? 0,
                            'compare' => (int)$channel_compare_active_leads ?? 0,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => (int)$channel_total_activities ?? 0,
                                'compare' => (int)$channel_compare_total_activities ?? 0,
                                'target_activities' => (int)$bum->target_activities ?? 0,
                            ],
                            'hot_activities' => (int)$channel_hot_activities ?? 0,
                            'warm_activities' => (int)$channel_warm_activities ?? 0,
                            'cold_activities' => (int)$channel_cold_activities ?? 0,
                        ],
                        'estimation' => [
                            'value' => (int)$channel_estimation ?? 0,
                            'compare' => (int)$channel_compare_estimation ?? 0,
                        ],
                        'quotation' => [
                            'value' => (int) $channel_quotation ?? 0,
                            'compare' => (int) $channel_compare_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => potongPPN($channel_deals),
                            'compare' => potongPPN($channel_compare_deals),
                            'total_transaction' => $channel_total_deals_transaction,
                            'target_deals' => (int)$bum->target_deals ?? 0,
                        ],
                        // 'interior_design' => [
                        //     'value' => potongPPN($channel_interior_design),
                        //     'total_transaction' => $channel_interior_design_total_transaction,
                        // ],
                        // 'retail' => [
                        //     'value' => potongPPN($channel_deals - $channel_interior_design),
                        //     'total_transaction' => $channel_total_deals_transaction - $channel_interior_design_total_transaction,
                        // ],
                    ];

                    $summary_new_leads += $channel_new_leads ?? 0;
                    $summary_compare_new_leads += $channel_compare_new_leads ?? 0;
                    $summary_active_leads += $channel_active_leads ?? 0;
                    $summary_compare_active_leads += $channel_compare_active_leads ?? 0;
                    $summary_total_activities += $channel_total_activities ?? 0;
                    $summary_compare_total_activities += $channel_compare_total_activities ?? 0;
                    $summary_hot_activities += $channel_hot_activities ?? 0;
                    $summary_warm_activities += $channel_warm_activities ?? 0;
                    $summary_cold_activities += $channel_cold_activities ?? 0;
                    $summary_estimation += $channel_estimation ?? 0;
                    $summary_compare_estimation += $channel_compare_estimation ?? 0;
                    $summary_quotation += $channel_quotation ?? 0;
                    $summary_compare_quotation += $channel_compare_quotation ?? 0;
                    $summary_deals += $channel_deals ?? 0;
                    $summary_total_deals_transaction += $channel_total_deals_transaction ?? 0;
                    $summary_compare_deals += $channel_compare_deals ?? 0;
                    // $summary_interior_design += $channel_interior_design ?? 0;
                    // $summary_interior_design_total_transaction += $channel_interior_design_total_transaction ?? 0;
                }

                $data['summary'] = [
                    'new_leads' => [
                        'value' => $summary_new_leads,
                        'compare' => $summary_compare_new_leads,
                    ],
                    'active_leads' => [
                        'value' => $summary_active_leads,
                        'compare' => $summary_compare_active_leads,
                    ],
                    'follow_up' => [
                        'total_activities' => [
                            'value' => $summary_total_activities,
                            'compare' => $summary_compare_total_activities,
                        ],
                        'hot_activities' => $summary_hot_activities,
                        'warm_activities' => $summary_warm_activities,
                        'cold_activities' => $summary_cold_activities,
                    ],
                    'estimation' => [
                        'value' => $summary_estimation,
                        'compare' => $summary_compare_estimation,
                    ],
                    'quotation' => [
                        'value' => $summary_quotation,
                        'compare' => $summary_compare_quotation,
                    ],
                    'deals' => [
                        'value' => potongPPN($summary_deals),
                        'compare' => potongPPN($summary_compare_deals),
                        'total_transaction' => $summary_total_deals_transaction,
                    ],
                    // 'interior_design' => [
                    //     'value' => potongPPN($summary_interior_design),
                    //     'total_transaction' => $summary_interior_design_total_transaction,
                    // ],
                    // 'retail' => [
                    //     'value' => potongPPN($summary_deals - $summary_interior_design),
                    //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                    // ],
                ];

                return response()->json(array_merge($data, $infoDate));
            } elseif ($request->user_type == 'store_leader') {
                $query = User::selectRaw('id, name, type')
                    ->where('type', UserType::SUPERVISOR)
                    ->where('supervisor_type_id', 1);
                // ->where('company_id', $companyId);

                $query = $query->selectRaw("(SELECT SUM(target) FROM new_targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(start_date) >= '" . $startTargetDate . "' AND DATE(end_date) <= '" . $endTargetDate . "') as target_leads");

                $query = $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=7 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_activities");

                // if ($startDate->month == $endDate->month) {
                $query = $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_deals");
                // }

                $query = $query->with(['channels' => function ($channel) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $channel->selectRaw("id, name");

                    $channel->selectRaw("(SELECT SUM(estimated_value) FROM activity_brand_values abv
                    WHERE abv.lead_id IN (SELECT id FROM leads ld WHERE ld.channel_id=channels.id)
                    AND DATE(abv.created_at) >= '" . $startDate . "'
                    AND DATE(abv.created_at) <= '" . $endDate . "')
                    " . (request()->product_brand_id ? " and abv.product_brand_id='" . request()->product_brand_id . "'" : '') . "
                    as total_estimation");

                    $channel->selectRaw("(SELECT SUM(estimated_value) FROM activity_brand_values abv
                    WHERE abv.lead_id IN (SELECT id FROM leads ld WHERE ld.channel_id=channels.id)
                    AND DATE(abv.created_at) >= '" . $startDateCompare . "'
                    AND DATE(abv.created_at) <= '" . $endDateCompare . "')
                    " . (request()->product_brand_id ? " and abv.product_brand_id='" . request()->product_brand_id . "'" : '') . "
                    as compare_total_estimation");

                    $channel->selectRaw("(
                        SELECT
                            count(DISTINCT customer_id)
                        from
                            leads
                            JOIN customers on customers.id = leads.customer_id
                        WHERE
                            date(customers.created_at) >= '" . $startDate . "'
                            and date(customers.created_at) <= '" . $endDate . "'
                            and channel_id = channels.id
                        ) as total_leads
                    ");

                    $channel->withCount(['channelLeads as compare_total_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                        ->withCount(['channelLeads as active_leads' => function ($q) use ($startDate, $endDate) {
                            $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                            if (request()->product_brand_id) {
                                $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['channelLeads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                            if (request()->product_brand_id) {
                                $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
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
                        }])
                        ->withCount(['channelLeads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['channelLeads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['channelLeads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        // ->withSum([
                        //     'channelLeads.activityBrandValues as total_estimation' => function ($q) use ($startDate, $endDate) {
                        //         $q->whereCreatedAtRange($startDate, $endDate);
                        //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        //         if (request()->product_brand_id) {
                        //             $q->where('product_brand_id', request()->product_brand_id);
                        //         }
                        //     }
                        // ], 'estimated_value')
                        // ->withSum([
                        //     'channelLeads.activityBrandValues as compare_total_estimation' => function ($q) use ($startDateCompare, $endDateCompare) {
                        //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        //         if (request()->product_brand_id) {
                        //             $q->where('product_brand_id', request()->product_brand_id);
                        //         }
                        //     }
                        // ], 'estimated_value')
                        ->withSum(['channelOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                            $q->whereCreatedAtRange($startDate, $endDate);
                            // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                            $q->whereDoesntHave('orderPayments');
                            $q->whereNotIn('status', [5, 6]);
                        }], 'total_price')
                        ->withSum(['channelOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereDoesntHave('orderPayments');
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        }], 'total_price')
                        ->withCount([
                            'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withSum([
                            'channelOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                            }
                        ], 'total_price')
                        ->withSum([
                            'channelOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                                $q->whereDeal($startDateCompare, $endDateCompare);
                            }
                        ], 'total_price');
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
                    // ], 'total_price');
                }]);

                if ($request->name) {
                    $query = $query->where('name', 'like', '%' . $request->name . '%');
                }

                $result = $query->get();
                // return response()->json($result);

                $summary_new_leads = 0;
                $summary_compare_new_leads = 0;
                $summary_active_leads = 0;
                $summary_compare_active_leads = 0;
                $summary_total_activities = 0;
                $summary_compare_total_activities = 0;
                $summary_target_activities = 0;
                $summary_hot_activities = 0;
                $summary_warm_activities = 0;
                $summary_cold_activities = 0;
                $summary_estimation = 0;
                $summary_compare_estimation = 0;
                $summary_quotation = 0;
                $summary_compare_quotation = 0;
                $summary_deals = 0;
                $summary_total_deals_transaction = 0;
                $summary_compare_deals = 0;
                // $summary_interior_design = 0;
                // $summary_interior_design_total_transaction = 0;

                $data = [];
                foreach ($result as $sl) {

                    $channel_new_leads = 0;
                    $channel_compare_new_leads = 0;
                    $channel_active_leads = 0;
                    $channel_compare_active_leads = 0;
                    $channel_total_activities = 0;
                    $channel_compare_total_activities = 0;
                    $channel_target_activities = 0;
                    $channel_hot_activities = 0;
                    $channel_warm_activities = 0;
                    $channel_cold_activities = 0;
                    $channel_estimation = 0;
                    $channel_compare_estimation = 0;
                    $channel_quotation = 0;
                    $channel_compare_quotation = 0;
                    $channel_deals = 0;
                    $channel_total_deals_transaction = 0;
                    $channel_compare_deals = 0;
                    // $channel_interior_design = 0;
                    // $channel_interior_design_total_transaction = 0;
                    foreach ($sl->channels as $channel) {
                        $pbs = $this->getPbs($channel, $startDate, $endDate, $startDateCompare, $endDateCompare, $channelId);

                        $channel_new_leads += (int)$channel->total_leads ?? 0;
                        $channel_compare_new_leads += (int)$channel->compare_total_leads ?? 0;
                        $channel_active_leads += (int)$channel->active_leads ?? 0;
                        $channel_compare_active_leads += (int)$channel->compare_active_leads ?? 0;
                        $channel_total_activities += (int)$channel->total_activities ?? 0;
                        $channel_compare_total_activities += (int)$channel->compare_total_activities ?? 0;
                        $channel_target_activities += (int)$channel->target_activities ?? 0;
                        $channel_hot_activities += (int)$channel->hot_activities ?? 0;
                        $channel_warm_activities += (int)$channel->warm_activities ?? 0;
                        $channel_cold_activities += (int)$channel->cold_activities ?? 0;
                        $channel_estimation += $pbs['estimated_value'];
                        $channel_compare_estimation += $pbs['compare_estimated_value'];
                        // $channel_estimation += (int)$channel->total_estimation ?? 0;
                        // $channel_compare_estimation += (int)$channel->compare_total_estimation ?? 0;
                        $channel_quotation += (int)$channel->total_quotation ?? 0;
                        $channel_compare_quotation += (int)$channel->compare_total_quotation ?? 0;
                        $channel_deals += (int)$channel->total_deals ?? 0;
                        $channel_total_deals_transaction += (int)$channel->total_deals_transaction ?? 0;
                        $channel_compare_deals += (int)$channel->compare_total_deals ?? 0;
                        // $channel_interior_design += (int)$channel->interior_design ?? 0;
                        // $channel_interior_design_total_transaction += (int)$channel->interior_design_total_transaction ?? 0;

                        // $summary_new_leads += (int)$channel->total_leads ?? 0;
                        // $summary_compare_new_leads += (int)$channel->compare_total_leads ?? 0;
                        // $summary_active_leads += (int)$channel->active_leads ?? 0;
                        // $summary_compare_active_leads += (int)$channel->compare_active_leads ?? 0;
                        // $summary_total_activities += (int)$channel->total_activities ?? 0;
                        // $summary_compare_total_activities += (int)$channel->compare_total_activities ?? 0;
                        // $summary_target_activities += (int)$channel->target_activities ?? 0;
                        // $summary_hot_activities += (int)$channel->hot_activities ?? 0;
                        // $summary_warm_activities += (int)$channel->warm_activities ?? 0;
                        // $summary_cold_activities += (int)$channel->cold_activities ?? 0;
                        // $summary_estimation += $pbs['estimated_value'];
                        // $summary_compare_estimation += $pbs['compare_estimated_value'];
                        // // $summary_estimation += (int)$channel->total_estimation ?? 0;
                        // // $summary_compare_estimation += (int)$channel->compare_total_estimation ?? 0;
                        // $summary_quotation += (int)$channel->total_quotation ?? 0;
                        // $summary_compare_quotation += (int)$channel->compare_total_quotation ?? 0;
                        // $summary_deals += (int)$channel->total_deals ?? 0;
                        // $summary_total_deals_transaction += (int)$channel->total_deals_transaction ?? 0;
                        // $summary_compare_deals += (int)$channel->compare_total_deals ?? 0;
                        // $summary_interior_design += (int)$channel->interior_design ?? 0;
                        // $summary_interior_design_total_transaction += (int)$channel->interior_design_total_transaction ?? 0;
                    }

                    $data['details'][] = [
                        'id' => $sl->id,
                        'name' => $sl->name,
                        'new_leads' => [
                            'value' => $channel_new_leads,
                            'compare' => $channel_compare_new_leads,
                            'target_leads' => (int)$sl->target_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => $channel_active_leads,
                            'compare' => $channel_compare_active_leads,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => $channel_total_activities,
                                'compare' => $channel_compare_total_activities,
                                'target_activities' => (int)$sl->target_activities ?? 0,
                            ],
                            'hot_activities' => $channel_hot_activities,
                            'warm_activities' => $channel_warm_activities,
                            'cold_activities' => $channel_cold_activities,
                        ],
                        'estimation' => [
                            'value' => $channel_estimation,
                            'compare' => $channel_compare_estimation,
                        ],
                        'quotation' => [
                            'value' => (int) $channel_quotation ?? 0,
                            'compare' => (int) $channel_compare_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => potongPPN($channel_deals),
                            'compare' => potongPPN($channel_compare_deals),
                            'total_transaction' => $channel_total_deals_transaction,
                            'target_deals' => (int)$sl->target_deals ?? 0,
                        ],
                        // 'interior_design' => [
                        //     'value' => potongPPN($channel_interior_design),
                        //     'total_transaction' => $channel_interior_design_total_transaction,
                        // ],
                        // 'retail' => [
                        //     'value' => potongPPN($channel_deals - $channel_interior_design),
                        //     'total_transaction' => $channel_total_deals_transaction - $channel_interior_design_total_transaction,
                        // ],
                    ];

                    $summary_new_leads += $channel_new_leads;
                    $summary_compare_new_leads += $channel_compare_new_leads;
                    $summary_active_leads += $channel_active_leads;
                    $summary_compare_active_leads += $channel_compare_active_leads;
                    $summary_total_activities += $channel_total_activities;
                    $summary_compare_total_activities += $channel_compare_total_activities;
                    $summary_target_activities += $channel_target_activities;
                    $summary_hot_activities += $channel_hot_activities;
                    $summary_warm_activities += $channel_warm_activities;
                    $summary_cold_activities += $channel_cold_activities;
                    $summary_estimation += $channel_estimation;
                    $summary_compare_estimation += $channel_compare_estimation;
                    $summary_quotation += $channel_quotation;
                    $summary_compare_quotation += $channel_compare_quotation;
                    $summary_deals += $channel_deals;
                    $summary_total_deals_transaction += $channel_total_deals_transaction;
                    $summary_compare_deals += $channel_compare_deals;
                    // $summary_interior_design += $channel_interior_design;
                    // $summary_interior_design_total_transaction += $channel_interior_design_total_transaction;
                }

                // ini belum bener totalnya
                $data['summary'] = [
                    'new_leads' => [
                        'value' => $summary_new_leads,
                        'compare' => $summary_compare_new_leads,
                    ],
                    'active_leads' => [
                        'value' => $summary_active_leads,
                        'compare' => $summary_compare_active_leads,
                    ],
                    'follow_up' => [
                        'total_activities' => [
                            'value' => $summary_total_activities,
                            'compare' => $summary_compare_total_activities,
                            'target_activities' => $summary_target_activities,
                        ],
                        'hot_activities' => $summary_hot_activities,
                        'warm_activities' => $summary_warm_activities,
                        'cold_activities' => $summary_cold_activities,
                    ],
                    'estimation' => [
                        'value' => $summary_estimation,
                        'compare' => $summary_compare_estimation,
                    ],
                    'quotation' => [
                        'value' => $summary_quotation,
                        'compare' => $summary_compare_quotation,
                    ],
                    'deals' => [
                        'value' => potongPPN($summary_deals),
                        'compare' => potongPPN($summary_compare_deals),
                        'total_transaction' => $summary_total_deals_transaction,
                    ],
                    // 'interior_design' => [
                    //     'value' => potongPPN($summary_interior_design),
                    //     'total_transaction' => $summary_interior_design_total_transaction,
                    // ],
                    // 'retail' => [
                    //     'value' => potongPPN($summary_deals - $summary_interior_design),
                    //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                    // ],
                ];

                return response()->json(array_merge($data, $infoDate));
            } elseif ($request->user_type == 'store') {
                $query = Channel::selectRaw('id, name');
                // ->where('company_id', $companyId);
                // ->whereIn('id', $user->channels->pluck('id')->all());

                $query->selectRaw("(SELECT SUM(target) FROM new_targets WHERE model_type='channel' AND model_id=channels.id AND type=0 AND DATE(start_date) >= '" . $startTargetDate . "' AND DATE(end_date) <= '" . $endTargetDate . "') as target_leads");

                $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='channel' AND model_id=channels.id AND type=7 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_activities");

                // if ($startDate->month == $endDate->month) {
                $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='channel' AND model_id=channels.id AND type=0 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_deals");
                // }

                $query->selectRaw("(SELECT SUM(estimated_value) FROM activity_brand_values abv
                    WHERE abv.lead_id IN (SELECT id FROM leads ld WHERE ld.channel_id=channels.id)
                    AND DATE(abv.created_at) >= '" . $startDate . "'
                    AND DATE(abv.created_at) <= '" . $endDate . "')
                    " . (request()->product_brand_id ? " and abv.product_brand_id='" . request()->product_brand_id . "'" : '') . "
                    as total_estimation");

                $query->selectRaw("(SELECT SUM(estimated_value) FROM activity_brand_values abv
                    WHERE abv.lead_id IN (SELECT id FROM leads ld WHERE ld.channel_id=channels.id)
                    AND DATE(abv.created_at) >= '" . $startDateCompare . "'
                    AND DATE(abv.created_at) <= '" . $endDateCompare . "')
                    " . (request()->product_brand_id ? " and abv.product_brand_id='" . request()->product_brand_id . "'" : '') . "
                    as compare_total_estimation");
                $query->selectRaw("
                    (
                        SELECT
                            count(DISTINCT customer_id)
                        from
                            leads
                            JOIN customers on customers.id = leads.customer_id
                        WHERE
                            date(customers.created_at) >= '" . $startDate . "'
                            and date(customers.created_at) <= '" . $endDate . "'
                            and channel_id = channels.id
                            " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                        ) as total_leads
                    ")
                    ->withCount(['channelLeads as compare_total_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['channelLeads as active_leads' => function ($q) use ($startDate, $endDate) {
                        $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['channelLeads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
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
                    }])
                    ->withCount(['channelLeads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                        if ($channelId) $q->where('channel_id', $channelId);

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['channelLeads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                        if ($channelId) $q->where('channel_id', $channelId);

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['channelLeads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                        if ($channelId) $q->where('channel_id', $channelId);

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    // ->withSum([
                    //     'activityBrandValues as total_estimation' => function ($q) use ($startDate, $endDate) {
                    //         $q->whereCreatedAtRange($startDate, $endDate);
                    //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                    //         if (request()->product_brand_id) {
                    //             $q->where('product_brand_id', request()->product_brand_id);
                    //         }
                    //     }
                    // ], 'estimated_value')
                    // ->withSum([
                    //     'activityBrandValues as compare_total_estimation' => function ($q) use ($startDateCompare, $endDateCompare) {
                    //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                    //         if (request()->product_brand_id) {
                    //             $q->where('product_brand_id', request()->product_brand_id);
                    //         }
                    //     }
                    // ], 'estimated_value')
                    ->withSum(['channelOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate);
                        // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                        $q->whereDoesntHave('orderPayments');
                        $q->whereNotIn('status', [5, 6]);
                    }], 'total_price')
                    ->withSum(['channelOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereDoesntHave('orderPayments');
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    }], 'total_price')
                    ->withCount([
                        'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereDeal($startDate, $endDate);
                            if ($channelId) $q->where('channel_id', $channelId);
                        }
                    ], 'total_price')
                    ->withSum([
                        'channelOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                            $q->whereDeal($startDate, $endDate);
                        }
                    ], 'total_price')
                    ->withSum([
                        'channelOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereDeal($startDateCompare, $endDateCompare);
                        }
                    ], 'total_price');
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
                // ], 'total_price');

                if ($channelId) {
                    $query = $query->where('id', $channelId);
                }

                if ($request->name) {
                    $query = $query->where('name', 'like', '%' . $request->name . '%');
                }

                $result = $query->get();

                $data = [];
                $summary_new_leads = 0;
                $summary_compare_new_leads = 0;
                $summary_active_leads = 0;
                $summary_compare_active_leads = 0;
                $summary_total_activities = 0;
                $summary_compare_total_activities = 0;
                $summary_hot_activities = 0;
                $summary_warm_activities = 0;
                $summary_cold_activities = 0;
                $summary_estimation = 0;
                $summary_compare_estimation = 0;
                $summary_quotation = 0;
                $summary_compare_quotation = 0;
                $summary_deals = 0;
                $summary_total_deals_transaction = 0;
                $summary_compare_deals = 0;
                $summary_total_deals_transaction = 0;
                // $summary_interior_design = 0;
                // $summary_interior_design_total_transaction = 0;

                foreach ($result as $channel) {

                    // $sales_new_leads = 0;
                    // $sales_compare_new_leads = 0;
                    // $sales_active_leads = 0;
                    // $sales_compare_active_leads = 0;
                    // $sales_total_activities = 0;
                    // $sales_compare_total_activities = 0;
                    // $sales_hot_activities = 0;
                    // $sales_warm_activities = 0;
                    // $sales_cold_activities = 0;
                    // $sales_estimation = 0;
                    // $sales_compare_estimation = 0;
                    // $sales_quotation = 0;
                    // $sales_compare_quotation = 0;
                    // $sales_deals = 0;
                    // $sales_total_deals_transaction = 0;
                    // $sales_compare_deals = 0;
                    // $sales_interior_design = 0;
                    // $sales_interior_design_total_transaction = 0;
                    // foreach ($channel->sales as $sales) {
                    //     $pbs = $this->getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare);
                    //     $sales_new_leads += $sales->total_leads ?? 0;
                    //     $sales_compare_new_leads += $sales->compare_total_leads ?? 0;
                    //     $sales_active_leads += $sales->active_leads ?? 0;
                    //     $sales_compare_active_leads += $sales->compare_active_leads ?? 0;
                    //     $sales_total_activities += $sales->total_activities ?? 0;
                    //     $sales_compare_total_activities += $sales->compare_total_activities ?? 0;
                    //     $sales_hot_activities += $sales->hot_activities ?? 0;
                    //     $sales_warm_activities += $sales->warm_activities ?? 0;
                    //     $sales_cold_activities += $sales->cold_activities ?? 0;
                    //     $sales_estimation += $pbs['estimated_value'];
                    //     $sales_compare_estimation += $pbs['compare_estimated_value'];
                    //     // $sales_estimation += $sales->total_estimation ?? 0;
                    //     // $sales_compare_estimation += $sales->compare_total_estimation ?? 0;
                    //     $sales_quotation += $sales->total_quotation ?? 0;
                    //     $sales_compare_quotation += $sales->compare_total_quotation ?? 0;
                    //     $sales_deals += $sales->total_deals ?? 0;
                    //     $sales_total_deals_transaction += $sales->total_deals_transaction ?? 0;
                    //     $sales_compare_deals += $sales->compare_total_deals ?? 0;
                    //     $sales_interior_design += $sales->interior_design ?? 0;
                    //     $sales_interior_design_total_transaction += $sales->interior_design_total_transaction ?? 0;
                    // }

                    $pbs = $this->getPbs($channel, $startDate, $endDate, $startDateCompare, $endDateCompare);
                    $data['details'][] = [
                        'id' => $channel->id,
                        'name' => $channel->name,
                        'new_leads' => [
                            'value' => (int)$channel->total_leads ?? 0,
                            'compare' => (int)$channel->compare_total_leads ?? 0,
                            'target_leads' => (int)$channel->target_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => (int)$channel->active_leads ?? 0,
                            'compare' => (int)$channel->compare_active_leads ?? 0,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => (int)$channel->total_activities ?? 0,
                                'compare' => (int)$channel->compare_total_activities ?? 0,
                                'target_activities' => (int)$channel->target_activities ?? 0,
                            ],
                            'hot_activities' => (int)$channel->hot_activities ?? 0,
                            'warm_activities' => (int)$channel->warm_activities ?? 0,
                            'cold_activities' => (int)$channel->cold_activities ?? 0,
                        ],
                        'estimation' => [
                            'value' => (int)$pbs['estimated_value'] ?? 0,
                            'compare' => (int)$pbs['compare_estimated_value'] ?? 0,
                        ],
                        'quotation' => [
                            'value' => (int) $channel->total_quotation ?? 0,
                            'compare' => (int) $channel->compare_total_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => potongPPN($channel->total_deals),
                            'compare' => potongPPN($channel->compare_total_deals),
                            'total_transaction' => $channel->total_deals_transaction,
                            'target_deals' => (int)$channel->target_deals ?? 0,
                        ],
                        // 'interior_design' => [
                        //     'value' => potongPPN($channel->interior_design),
                        //     'total_transaction' => (int)$channel->interior_design_total_transaction ?? 0,
                        // ],
                        // 'retail' => [
                        //     'value' => potongPPN($channel->total_deals - $channel->interior_design),
                        //     'total_transaction' => (int)($channel->total_deals_transaction - $channel->interior_design_total_transaction) ?? 0,
                        // ],
                    ];

                    $summary_new_leads += $channel->total_leads ?? 0;
                    $summary_compare_new_leads += $channel->compare_total_leads ?? 0;
                    $summary_active_leads += $channel->active_leads ?? 0;
                    $summary_compare_active_leads += $channel->compare_active_leads ?? 0;
                    $summary_total_activities += $channel->total_activities ?? 0;
                    $summary_compare_total_activities += $channel->compare_total_activities ?? 0;
                    $summary_hot_activities += $channel->hot_activities ?? 0;
                    $summary_warm_activities += $channel->warm_activities ?? 0;
                    $summary_cold_activities += $channel->cold_activities ?? 0;
                    $summary_estimation += (int)$pbs['estimated_value'] ?? 0;
                    $summary_compare_estimation += (int)$pbs['compare_estimated_value'] ?? 0;
                    $summary_quotation += $channel->total_quotation ?? 0;
                    $summary_compare_quotation += $channel->compare_total_quotation ?? 0;
                    $summary_deals += $channel->total_deals ?? 0;
                    $summary_total_deals_transaction += $channel->total_deals_transaction ?? 0;
                    $summary_compare_deals += $channel->compare_total_deals ?? 0;
                    // $summary_interior_design += $channel->interior_design ?? 0;
                    // $summary_interior_design_total_transaction += $channel->interior_design_total_transaction ?? 0;
                }

                $data['summary'] = [
                    'new_leads' => [
                        'value' => $summary_new_leads,
                        'compare' => $summary_compare_new_leads,
                    ],
                    'active_leads' => [
                        'value' => $summary_active_leads,
                        'compare' => $summary_compare_active_leads,
                    ],
                    'follow_up' => [
                        'total_activities' => [
                            'value' => $summary_total_activities,
                            'compare' => $summary_compare_total_activities,
                        ],
                        'hot_activities' => $summary_hot_activities,
                        'warm_activities' => $summary_warm_activities,
                        'cold_activities' => $summary_cold_activities,
                    ],
                    'estimation' => [
                        'value' => $summary_estimation,
                        'compare' => $summary_compare_estimation,
                    ],
                    'quotation' => [
                        'value' => $summary_quotation,
                        'compare' => $summary_compare_quotation,
                    ],
                    'deals' => [
                        'value' => potongPPN($summary_deals),
                        'compare' => potongPPN($summary_compare_deals),
                        'total_transaction' => $summary_total_deals_transaction,
                    ],
                    // 'interior_design' => [
                    //     'value' => potongPPN($summary_interior_design),
                    //     'total_transaction' => $summary_interior_design_total_transaction,
                    // ],
                    // 'retail' => [
                    //     'value' => potongPPN($summary_deals - $summary_interior_design),
                    //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                    // ],
                ];

                return response()->json(array_merge($data, $infoDate));
            }

            $query = User::selectRaw('id, name, type')
                ->selectRaw("(
                    SELECT
                        count(DISTINCT customer_id)
                    from
                        leads
                        JOIN customers on customers.id = leads.customer_id
                    WHERE
                        date(customers.created_at) >= '" . $startDate . "'
                        and date(customers.created_at) <= '" . $endDate . "'
                        and user_id = users.id
                        " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                    ) as total_leads
                ");

            $query = $query->selectRaw("(SELECT SUM(target) FROM new_targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(start_date) >= '" . $startTargetDate . "' AND DATE(end_date) <= '" . $endTargetDate . "') as target_leads");

            $query = $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=7 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_activities");

            // if ($startDate->month == $endDate->month) {
            $query = $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_deals");
            // }
            $query = $query->where('type', 2)
                // ->where('company_id', $companyId)
                ->withCount(['leads as compare_total_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as active_leads' => function ($q) use ($startDate, $endDate) {
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as total_activities' => function ($q) use ($startDate, $endDate) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // });

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                    // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    // });

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withSum([
                    'activityBrandValues as total_estimation' => function ($q) use ($startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate);
                        // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        if (request()->product_brand_id) {
                            $q->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'estimated_value')
                ->withSum([
                    'activityBrandValues as compare_total_estimation' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        if (request()->product_brand_id) {
                            $q->where('product_brand_id', request()->product_brand_id);
                        }
                    }
                ], 'estimated_value')
                ->withSum(['userOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                    $q->whereDoesntHave('orderPayments');
                    $q->whereNotIn('status', [5, 6]);
                }], 'total_price')
                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereDoesntHave('orderPayments');
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                }], 'total_price')
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                        $q->whereDeal($startDate, $endDate);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereDeal($startDateCompare, $endDateCompare);
                    }
                ], 'total_price');
            // ->withSum([
            //     'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate)
            //             ->whereNotNull('interior_design_id');
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price')
            // ->withCount([
            //     'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
            //         $q->whereDeal($startDate, $endDate)
            //             ->whereNotNull('interior_design_id');
            //         if ($channelId) $q->where('channel_id', $channelId);
            //     }
            // ], 'total_price');

            if ($request->name) {
                $query = $query->where('name', 'like', '%' . $request->name . '%');
            }

            $result = $query->get();

            $summary_new_leads = 0;
            $summary_compare_new_leads = 0;
            $summary_active_leads = 0;
            $summary_compare_active_leads = 0;
            $summary_total_activities = 0;
            $summary_compare_total_activities = 0;
            $summary_hot_activities = 0;
            $summary_warm_activities = 0;
            $summary_cold_activities = 0;
            $summary_estimation = 0;
            $summary_compare_estimation = 0;
            $summary_quotation = 0;
            $summary_compare_quotation = 0;
            $summary_deals = 0;
            $summary_total_deals_transaction = 0;
            $summary_compare_deals = 0;
            // $summary_interior_design = 0;
            // $summary_interior_design_total_transaction = 0;

            $data = [];
            foreach ($result as $sales) {
                $pbs = $this->getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare, $channelId);

                $data['details'][] = [
                    'id' => $sales->id,
                    'name' => $sales->name,
                    'new_leads' => [
                        'value' => (int)$sales->total_leads ?? 0,
                        'compare' => (int)$sales->compare_total_leads ?? 0,
                        'target_leads' => (int)$sales->target_leads ?? 0,
                    ],
                    'active_leads' => [
                        'value' => (int)$sales->active_leads ?? 0,
                        'compare' => (int)$sales->compare_active_leads ?? 0,
                    ],
                    'follow_up' => [
                        'total_activities' => [
                            'value' => (int)$sales->total_activities ?? 0,
                            'compare' => (int)$sales->compare_total_activities ?? 0,
                            'target_activities' => (int)$sales->target_activities ?? 0,
                        ],
                        'hot_activities' => (int)$sales->hot_activities ?? 0,
                        'warm_activities' => (int)$sales->warm_activities ?? 0,
                        'cold_activities' => (int)$sales->cold_activities ?? 0,
                    ],
                    'estimation' => [
                        'value' => $pbs['estimated_value'],
                        'compare' => $pbs['compare_estimated_value'],
                        // 'value' => (int)$sales->total_estimation ?? 0,
                        // 'compare' => (int)$sales->compare_total_estimation ?? 0,
                    ],
                    'quotation' => [
                        'value' => (int) $sales->total_quotation ?? 0,
                        'compare' => (int) $sales->compare_total_quotation ?? 0,
                    ],
                    'deals' => [
                        'value' => potongPPN($sales->total_deals),
                        'compare' => potongPPN($sales->compare_total_deals),
                        'total_transaction' => (int)$sales->total_deals_transaction ?? 0,
                        'target_deals' => (int)$sales->target_deals ?? 0,
                    ],
                    // 'interior_design' => [
                    //     'value' => potongPPN($sales->interior_design),
                    //     'total_transaction' => (int)$sales->interior_design_total_transaction ?? 0,
                    // ],
                    // 'retail' => [
                    //     'value' => potongPPN($sales->total_deals - $sales->interior_design),
                    //     'total_transaction' => (int)($sales->total_deals_transaction - $sales->interior_design_total_transaction) ?? 0,
                    // ],
                ];

                $summary_new_leads += (int)$sales->total_leads ?? 0;
                $summary_compare_new_leads += (int)$sales->compare_total_leads ?? 0;
                $summary_active_leads += (int)$sales->active_leads ?? 0;
                $summary_compare_active_leads += (int)$sales->compare_active_leads ?? 0;
                $summary_total_activities += (int)$sales->total_activities ?? 0;
                $summary_compare_total_activities += (int)$sales->compare_total_activities ?? 0;
                $summary_hot_activities += (int)$sales->hot_activities ?? 0;
                $summary_warm_activities += (int)$sales->warm_activities ?? 0;
                $summary_cold_activities += (int)$sales->cold_activities ?? 0;
                $summary_estimation += $pbs['estimated_value'];
                $summary_compare_estimation += $pbs['compare_estimated_value'];
                // $summary_estimation += (int)$sales->total_estimation ?? 0;
                // $summary_compare_estimation += (int)$sales->compare_total_estimation ?? 0;
                $summary_quotation += (int)$sales->total_quotation ?? 0;
                $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
                $summary_deals += (int)$sales->total_deals ?? 0;
                $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
                $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
                // $summary_interior_design += (int)$sales->interior_design ?? 0;
                // $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
            }

            $data['summary'] = [
                'new_leads' => [
                    'value' => $summary_new_leads,
                    'compare' => $summary_compare_new_leads,
                ],
                'active_leads' => [
                    'value' => $summary_active_leads,
                    'compare' => $summary_compare_active_leads,
                ],
                'follow_up' => [
                    'total_activities' => [
                        'value' => $summary_total_activities,
                        'compare' => $summary_compare_total_activities,
                    ],
                    'hot_activities' => $summary_hot_activities,
                    'warm_activities' => $summary_warm_activities,
                    'cold_activities' => $summary_cold_activities,
                ],
                'estimation' => [
                    'value' => $summary_estimation,
                    'compare' => $summary_compare_estimation,
                ],
                'quotation' => [
                    'value' => $summary_quotation,
                    'compare' => $summary_compare_quotation,
                ],
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    'compare' => potongPPN($summary_compare_deals),
                    'total_transaction' => $summary_total_deals_transaction,
                ],
                // 'interior_design' => [
                //     'value' => potongPPN($summary_interior_design),
                //     'total_transaction' => $summary_interior_design_total_transaction,
                // ],
                // 'retail' => [
                //     'value' => potongPPN($summary_deals - $summary_interior_design),
                //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                // ],
            ];

            return response()->json(array_merge($data, $infoDate));
        } else if (in_array($userType, ['sl', 'bum'])) {
            if ($request->user_type == 'store') {
                $query = Channel::selectRaw('id, name')->whereIn('id', $user->channels->pluck('id')->all());

                $query->selectRaw("(SELECT SUM(target) FROM new_targets WHERE model_type='channel' AND model_id=channels.id AND type=0 AND DATE(start_date) >= '" . $startTargetDate . "' AND DATE(end_date) <= '" . $endTargetDate . "') as target_leads");

                $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='channel' AND model_id=channels.id AND type=7 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_activities");

                // if ($startDate->month == $endDate->month) {
                $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='channel' AND model_id=channels.id AND type=0 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_deals");
                // }

                $query->selectRaw("(SELECT SUM(estimated_value) FROM activity_brand_values abv
                    WHERE abv.lead_id IN (SELECT id FROM leads ld WHERE ld.channel_id=channels.id)
                    AND DATE(abv.created_at) >= '" . $startDate . "'
                    AND DATE(abv.created_at) <= '" . $endDate . "')
                    " . (request()->product_brand_id ? " and abv.product_brand_id='" . request()->product_brand_id . "'" : '') . "
                    as total_estimation");

                $query->selectRaw("(SELECT SUM(estimated_value) FROM activity_brand_values abv
                    WHERE abv.lead_id IN (SELECT id FROM leads ld WHERE ld.channel_id=channels.id)
                    AND DATE(abv.created_at) >= '" . $startDateCompare . "'
                    AND DATE(abv.created_at) <= '" . $endDateCompare . "')
                    " . (request()->product_brand_id ? " and abv.product_brand_id='" . request()->product_brand_id . "'" : '') . "
                    as compare_total_estimation");
                $query->selectRaw("
                    (
                        SELECT
                            count(DISTINCT customer_id)
                        from
                            leads
                            JOIN customers on customers.id = leads.customer_id
                        WHERE
                            date(customers.created_at) >= '" . $startDate . "'
                            and date(customers.created_at) <= '" . $endDate . "'
                            and channel_id = channels.id
                            " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                        ) as total_leads
                    ")
                    ->withCount(['channelLeads as compare_total_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['channelLeads as active_leads' => function ($q) use ($startDate, $endDate) {
                        $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['channelLeads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
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
                    }])
                    ->withCount(['channelLeads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                        if ($channelId) $q->where('channel_id', $channelId);

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['channelLeads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                        if ($channelId) $q->where('channel_id', $channelId);

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['channelLeads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                        if ($channelId) $q->where('channel_id', $channelId);

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    // ->withSum([
                    //     'activityBrandValues as total_estimation' => function ($q) use ($startDate, $endDate) {
                    //         $q->whereCreatedAtRange($startDate, $endDate);
                    //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                    //         if (request()->product_brand_id) {
                    //             $q->where('product_brand_id', request()->product_brand_id);
                    //         }
                    //     }
                    // ], 'estimated_value')
                    // ->withSum([
                    //     'activityBrandValues as compare_total_estimation' => function ($q) use ($startDateCompare, $endDateCompare) {
                    //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                    //         if (request()->product_brand_id) {
                    //             $q->where('product_brand_id', request()->product_brand_id);
                    //         }
                    //     }
                    // ], 'estimated_value')
                    ->withSum(['channelOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate);
                        // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                        $q->whereDoesntHave('orderPayments');
                        $q->whereNotIn('status', [5, 6]);
                    }], 'total_price')
                    ->withSum(['channelOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereDoesntHave('orderPayments');
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    }], 'total_price')
                    ->withCount([
                        'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereDeal($startDate, $endDate);
                            if ($channelId) $q->where('channel_id', $channelId);
                        }
                    ], 'total_price')
                    ->withSum([
                        'channelOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                            $q->whereDeal($startDate, $endDate);
                        }
                    ], 'total_price')
                    ->withSum([
                        'channelOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereDeal($startDateCompare, $endDateCompare);
                        }
                    ], 'total_price');
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
                // ], 'total_price');

                if ($channelId) {
                    $query = $query->where('id', $channelId);
                }

                if ($request->name) {
                    $query = $query->where('name', 'like', '%' . $request->name . '%');
                }

                $result = $query->get();

                $data = [];
                $summary_new_leads = 0;
                $summary_compare_new_leads = 0;
                $summary_active_leads = 0;
                $summary_compare_active_leads = 0;
                $summary_total_activities = 0;
                $summary_compare_total_activities = 0;
                $summary_hot_activities = 0;
                $summary_warm_activities = 0;
                $summary_cold_activities = 0;
                $summary_estimation = 0;
                $summary_compare_estimation = 0;
                $summary_quotation = 0;
                $summary_compare_quotation = 0;
                $summary_deals = 0;
                $summary_total_deals_transaction = 0;
                $summary_compare_deals = 0;
                $summary_total_deals_transaction = 0;
                // $summary_interior_design = 0;
                // $summary_interior_design_total_transaction = 0;

                foreach ($result as $channel) {

                    // $sales_new_leads = 0;
                    // $sales_compare_new_leads = 0;
                    // $sales_active_leads = 0;
                    // $sales_compare_active_leads = 0;
                    // $sales_total_activities = 0;
                    // $sales_compare_total_activities = 0;
                    // $sales_hot_activities = 0;
                    // $sales_warm_activities = 0;
                    // $sales_cold_activities = 0;
                    // $sales_estimation = 0;
                    // $sales_compare_estimation = 0;
                    // $sales_quotation = 0;
                    // $sales_compare_quotation = 0;
                    // $sales_deals = 0;
                    // $sales_total_deals_transaction = 0;
                    // $sales_compare_deals = 0;
                    // $sales_interior_design = 0;
                    // $sales_interior_design_total_transaction = 0;
                    // foreach ($channel->sales as $sales) {
                    //     $pbs = $this->getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare);
                    //     $sales_new_leads += $sales->total_leads ?? 0;
                    //     $sales_compare_new_leads += $sales->compare_total_leads ?? 0;
                    //     $sales_active_leads += $sales->active_leads ?? 0;
                    //     $sales_compare_active_leads += $sales->compare_active_leads ?? 0;
                    //     $sales_total_activities += $sales->total_activities ?? 0;
                    //     $sales_compare_total_activities += $sales->compare_total_activities ?? 0;
                    //     $sales_hot_activities += $sales->hot_activities ?? 0;
                    //     $sales_warm_activities += $sales->warm_activities ?? 0;
                    //     $sales_cold_activities += $sales->cold_activities ?? 0;
                    //     $sales_estimation += $pbs['estimated_value'];
                    //     $sales_compare_estimation += $pbs['compare_estimated_value'];
                    //     // $sales_estimation += $sales->total_estimation ?? 0;
                    //     // $sales_compare_estimation += $sales->compare_total_estimation ?? 0;
                    //     $sales_quotation += $sales->total_quotation ?? 0;
                    //     $sales_compare_quotation += $sales->compare_total_quotation ?? 0;
                    //     $sales_deals += $sales->total_deals ?? 0;
                    //     $sales_total_deals_transaction += $sales->total_deals_transaction ?? 0;
                    //     $sales_compare_deals += $sales->compare_total_deals ?? 0;
                    //     $sales_interior_design += $sales->interior_design ?? 0;
                    //     $sales_interior_design_total_transaction += $sales->interior_design_total_transaction ?? 0;
                    // }

                    $pbs = $this->getPbs($channel, $startDate, $endDate, $startDateCompare, $endDateCompare);
                    $data['details'][] = [
                        'id' => $channel->id,
                        'name' => $channel->name,
                        'new_leads' => [
                            'value' => (int)$channel->total_leads ?? 0,
                            'compare' => (int)$channel->compare_total_leads ?? 0,
                            'target_leads' => (int)$channel->target_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => (int)$channel->active_leads ?? 0,
                            'compare' => (int)$channel->compare_active_leads ?? 0,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => (int)$channel->total_activities ?? 0,
                                'compare' => (int)$channel->compare_total_activities ?? 0,
                                'target_activities' => (int)$channel->target_activities ?? 0,
                            ],
                            'hot_activities' => (int)$channel->hot_activities ?? 0,
                            'warm_activities' => (int)$channel->warm_activities ?? 0,
                            'cold_activities' => (int)$channel->cold_activities ?? 0,
                        ],
                        'estimation' => [
                            'value' => (int)$pbs['estimated_value'] ?? 0,
                            'compare' => (int)$pbs['compare_estimated_value'] ?? 0,
                        ],
                        'quotation' => [
                            'value' => (int) $channel->total_quotation ?? 0,
                            'compare' => (int) $channel->compare_total_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => potongPPN($channel->total_deals),
                            'compare' => potongPPN($channel->compare_total_deals),
                            'total_transaction' => $channel->total_deals_transaction,
                            'target_deals' => (int)$channel->target_deals ?? 0,
                        ],
                        // 'interior_design' => [
                        //     'value' => potongPPN($channel->interior_design),
                        //     'total_transaction' => (int)$channel->interior_design_total_transaction ?? 0,
                        // ],
                        // 'retail' => [
                        //     'value' => potongPPN($channel->total_deals - $channel->interior_design),
                        //     'total_transaction' => (int)($channel->total_deals_transaction - $channel->interior_design_total_transaction) ?? 0,
                        // ],
                    ];

                    $summary_new_leads += $channel->total_leads ?? 0;
                    $summary_compare_new_leads += $channel->compare_total_leads ?? 0;
                    $summary_active_leads += $channel->active_leads ?? 0;
                    $summary_compare_active_leads += $channel->compare_active_leads ?? 0;
                    $summary_total_activities += $channel->total_activities ?? 0;
                    $summary_compare_total_activities += $channel->compare_total_activities ?? 0;
                    $summary_hot_activities += $channel->hot_activities ?? 0;
                    $summary_warm_activities += $channel->warm_activities ?? 0;
                    $summary_cold_activities += $channel->cold_activities ?? 0;
                    $summary_estimation += (int)$pbs['estimated_value'] ?? 0;
                    $summary_compare_estimation += (int)$pbs['compare_estimated_value'] ?? 0;
                    $summary_quotation += $channel->total_quotation ?? 0;
                    $summary_compare_quotation += $channel->compare_total_quotation ?? 0;
                    $summary_deals += $channel->total_deals ?? 0;
                    $summary_total_deals_transaction += $channel->total_deals_transaction ?? 0;
                    $summary_compare_deals += $channel->compare_total_deals ?? 0;
                    // $summary_interior_design += $channel->interior_design ?? 0;
                    // $summary_interior_design_total_transaction += $channel->interior_design_total_transaction ?? 0;
                }

                $data['summary'] = [
                    'new_leads' => [
                        'value' => $summary_new_leads,
                        'compare' => $summary_compare_new_leads,
                    ],
                    'active_leads' => [
                        'value' => $summary_active_leads,
                        'compare' => $summary_compare_active_leads,
                    ],
                    'follow_up' => [
                        'total_activities' => [
                            'value' => $summary_total_activities,
                            'compare' => $summary_compare_total_activities,
                        ],
                        'hot_activities' => $summary_hot_activities,
                        'warm_activities' => $summary_warm_activities,
                        'cold_activities' => $summary_cold_activities,
                    ],
                    'estimation' => [
                        'value' => $summary_estimation,
                        'compare' => $summary_compare_estimation,
                    ],
                    'quotation' => [
                        'value' => $summary_quotation,
                        'compare' => $summary_compare_quotation,
                    ],
                    'deals' => [
                        'value' => potongPPN($summary_deals),
                        'compare' => potongPPN($summary_compare_deals),
                        'total_transaction' => $summary_total_deals_transaction,
                    ],
                    // 'interior_design' => [
                    //     'value' => potongPPN($summary_interior_design),
                    //     'total_transaction' => $summary_interior_design_total_transaction,
                    // ],
                    // 'retail' => [
                    //     'value' => potongPPN($summary_deals - $summary_interior_design),
                    //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                    // ],
                ];

                return response()->json(array_merge($data, $infoDate));
            } elseif ($request->user_type == 'store_leader') {
                $query = User::selectRaw('id, name, type')
                    ->where('type', 3)
                    ->where('supervisor_type_id', 1)
                    ->where('supervisor_id', $user->id)
                    ->whereHas('channels', fn ($q) => $q->whereIn('id', $user->channels->pluck('id')->all()));

                $query = $query->selectRaw("(SELECT SUM(target) FROM new_targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(start_date) >= '" . $startTargetDate . "' AND DATE(end_date) <= '" . $endTargetDate . "') as target_leads");

                $query = $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=7 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_activities");

                // if ($startDate->month == $endDate->month) {
                $query = $query->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_deals");
                // }

                $query = $query->with(['channels' => function ($channel) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $channel->selectRaw("id, name");

                    $channel->selectRaw("(SELECT SUM(estimated_value) FROM activity_brand_values abv
                    WHERE abv.lead_id IN (SELECT id FROM leads ld WHERE ld.channel_id=channels.id)
                    AND DATE(abv.created_at) >= '" . $startDate . "'
                    AND DATE(abv.created_at) <= '" . $endDate . "')
                    " . (request()->product_brand_id ? " and abv.product_brand_id='" . request()->product_brand_id . "'" : '') . "
                    as total_estimation");

                    $channel->selectRaw("(SELECT SUM(estimated_value) FROM activity_brand_values abv
                    WHERE abv.lead_id IN (SELECT id FROM leads ld WHERE ld.channel_id=channels.id)
                    AND DATE(abv.created_at) >= '" . $startDateCompare . "'
                    AND DATE(abv.created_at) <= '" . $endDateCompare . "')
                    " . (request()->product_brand_id ? " and abv.product_brand_id='" . request()->product_brand_id . "'" : '') . "
                    as compare_total_estimation");

                    $channel->selectRaw("(
                        SELECT
                            count(DISTINCT customer_id)
                        from
                            leads
                            JOIN customers on customers.id = leads.customer_id
                        WHERE
                            date(customers.created_at) >= '" . $startDate . "'
                            and date(customers.created_at) <= '" . $endDate . "'
                            and channel_id = channels.id
                        ) as total_leads
                    ");

                    $channel->withCount(['channelLeads as compare_total_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                        ->withCount(['channelLeads as active_leads' => function ($q) use ($startDate, $endDate) {
                            $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                            if (request()->product_brand_id) {
                                $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['channelLeads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                            if (request()->product_brand_id) {
                                $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
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
                        }])
                        ->withCount(['channelLeads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['channelLeads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['channelLeads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        // ->withSum([
                        //     'channelLeads.activityBrandValues as total_estimation' => function ($q) use ($startDate, $endDate) {
                        //         $q->whereCreatedAtRange($startDate, $endDate);
                        //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        //         if (request()->product_brand_id) {
                        //             $q->where('product_brand_id', request()->product_brand_id);
                        //         }
                        //     }
                        // ], 'estimated_value')
                        // ->withSum([
                        //     'channelLeads.activityBrandValues as compare_total_estimation' => function ($q) use ($startDateCompare, $endDateCompare) {
                        //         $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                        //         if (request()->product_brand_id) {
                        //             $q->where('product_brand_id', request()->product_brand_id);
                        //         }
                        //     }
                        // ], 'estimated_value')
                        ->withSum(['channelOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                            $q->whereCreatedAtRange($startDate, $endDate);
                            // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                            $q->whereDoesntHave('orderPayments');
                            $q->whereNotIn('status', [5, 6]);
                        }], 'total_price')
                        ->withSum(['channelOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereDoesntHave('orderPayments');
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        }], 'total_price')
                        ->withCount([
                            'channelOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withSum([
                            'channelOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                            }
                        ], 'total_price')
                        ->withSum([
                            'channelOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                                $q->whereDeal($startDateCompare, $endDateCompare);
                            }
                        ], 'total_price');
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
                    // ], 'total_price');
                }]);

                if ($request->name) {
                    $query = $query->where('name', 'like', '%' . $request->name . '%');
                }

                $result = $query->get();
                // return response()->json($result);

                $summary_new_leads = 0;
                $summary_compare_new_leads = 0;
                $summary_active_leads = 0;
                $summary_compare_active_leads = 0;
                $summary_total_activities = 0;
                $summary_compare_total_activities = 0;
                $summary_target_activities = 0;
                $summary_hot_activities = 0;
                $summary_warm_activities = 0;
                $summary_cold_activities = 0;
                $summary_estimation = 0;
                $summary_compare_estimation = 0;
                $summary_quotation = 0;
                $summary_compare_quotation = 0;
                $summary_deals = 0;
                $summary_total_deals_transaction = 0;
                $summary_compare_deals = 0;
                // $summary_interior_design = 0;
                // $summary_interior_design_total_transaction = 0;

                $data = [];
                foreach ($result as $sl) {
                    foreach ($sl->channels as $channel) {
                        $pbs = $this->getPbs($channel, $startDate, $endDate, $startDateCompare, $endDateCompare, $channelId);

                        $summary_new_leads += (int)$channel->total_leads ?? 0;
                        $summary_compare_new_leads += (int)$channel->compare_total_leads ?? 0;
                        $summary_active_leads += (int)$channel->active_leads ?? 0;
                        $summary_compare_active_leads += (int)$channel->compare_active_leads ?? 0;
                        $summary_total_activities += (int)$channel->total_activities ?? 0;
                        $summary_compare_total_activities += (int)$channel->compare_total_activities ?? 0;
                        $summary_target_activities += (int)$channel->target_activities ?? 0;
                        $summary_hot_activities += (int)$channel->hot_activities ?? 0;
                        $summary_warm_activities += (int)$channel->warm_activities ?? 0;
                        $summary_cold_activities += (int)$channel->cold_activities ?? 0;
                        $summary_estimation += $pbs['estimated_value'];
                        $summary_compare_estimation += $pbs['compare_estimated_value'];
                        // $summary_estimation += (int)$channel->total_estimation ?? 0;
                        // $summary_compare_estimation += (int)$channel->compare_total_estimation ?? 0;
                        $summary_quotation += (int)$channel->total_quotation ?? 0;
                        $summary_compare_quotation += (int)$channel->compare_total_quotation ?? 0;
                        $summary_deals += (int)$channel->total_deals ?? 0;
                        $summary_total_deals_transaction += (int)$channel->total_deals_transaction ?? 0;
                        $summary_compare_deals += (int)$channel->compare_total_deals ?? 0;
                        // $summary_interior_design += (int)$channel->interior_design ?? 0;
                        // $summary_interior_design_total_transaction += (int)$channel->interior_design_total_transaction ?? 0;
                    }

                    $data['details'][] = [
                        'id' => $sl->id,
                        'name' => $sl->name,
                        'new_leads' => [
                            'value' => $summary_new_leads,
                            'compare' => $summary_compare_new_leads,
                            'target_leads' => (int)$sl->target_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => $summary_active_leads,
                            'compare' => $summary_compare_active_leads,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => $summary_total_activities,
                                'compare' => $summary_compare_total_activities,
                                'target_activities' => (int)$sl->target_activities ?? 0,
                            ],
                            'hot_activities' => $summary_hot_activities,
                            'warm_activities' => $summary_warm_activities,
                            'cold_activities' => $summary_cold_activities,
                        ],
                        'estimation' => [
                            'value' => $summary_estimation,
                            'compare' => $summary_compare_estimation,
                        ],
                        'quotation' => [
                            'value' => $summary_quotation,
                            'compare' => $summary_compare_quotation,
                        ],
                        'deals' => [
                            'value' => potongPPN($summary_deals),
                            'compare' => potongPPN($summary_compare_deals),
                            'total_transaction' => $summary_total_deals_transaction,
                            'target_deals' => (int)$sl->target_deals ?? 0,
                        ],
                        // 'interior_design' => [
                        //     'value' => potongPPN($summary_interior_design),
                        //     'total_transaction' => $summary_interior_design_total_transaction,
                        // ],
                        // 'retail' => [
                        //     'value' => potongPPN($summary_deals - $summary_interior_design),
                        //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                        // ],
                    ];
                }

                // ini belum bener totalnya
                $data['summary'] = [
                    'new_leads' => [
                        'value' => $summary_new_leads,
                        'compare' => $summary_compare_new_leads,
                    ],
                    'active_leads' => [
                        'value' => $summary_active_leads,
                        'compare' => $summary_compare_active_leads,
                    ],
                    'follow_up' => [
                        'total_activities' => [
                            'value' => $summary_total_activities,
                            'compare' => $summary_compare_total_activities,
                            'target_activities' => $summary_target_activities,
                        ],
                        'hot_activities' => $summary_hot_activities,
                        'warm_activities' => $summary_warm_activities,
                        'cold_activities' => $summary_cold_activities,
                    ],
                    'estimation' => [
                        'value' => $summary_estimation,
                        'compare' => $summary_compare_estimation,
                    ],
                    'quotation' => [
                        'value' => $summary_quotation,
                        'compare' => $summary_compare_quotation,
                    ],
                    'deals' => [
                        'value' => potongPPN($summary_deals),
                        'compare' => potongPPN($summary_compare_deals),
                        'total_transaction' => $summary_total_deals_transaction,
                    ],
                    // 'interior_design' => [
                    //     'value' => potongPPN($summary_interior_design),
                    //     'total_transaction' => $summary_interior_design_total_transaction,
                    // ],
                    // 'retail' => [
                    //     'value' => potongPPN($summary_deals - $summary_interior_design),
                    //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                    // ],
                ];

                return response()->json(array_merge($data, $infoDate));
            }

            $query = Channel::selectRaw('id,name')
                ->whereIn('id', $user->channels->pluck('id')->all())
                ->with(['sales' => function ($q) use ($channelId, $startTargetDate, $endTargetDate, $startDate, $endDate, $startDateCompare, $endDateCompare) {

                    $q->selectRaw("(SELECT SUM(target) FROM new_targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(start_date) >= '" . $startTargetDate . "' AND DATE(end_date) <= '" . $endTargetDate . "') as target_leads");

                    $q->selectRaw("(SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=7 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_activities");

                    // if ($startDate->month == $endDate->month) {
                    $q->selectRaw("users.id, users.name, users.channel_id, (SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_deals");
                    // }
                    if (request()->name) {
                        $q->where('name', 'like', '%' . request()->name . '%');
                    }
                    // $q->withCount(['leads as total_leads' => function ($q) use ($startDate, $endDate) {
                    //     $q->whereHas('customer', fn ($customer) => $customer->whereCreatedAtRange($startDate, $endDate));

                    //     if (request()->product_brand_id) {
                    //         $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                    //             $q2->where('product_brand_id', request()->product_brand_id);
                    //         });
                    //     }
                    // }])
                    $q->selectRaw("
                    id,name,type,channel_id,
                    (
                        SELECT
                            count(DISTINCT customer_id)
                        from
                            leads
                            JOIN customers on customers.id = leads.customer_id
                        WHERE
                            date(customers.created_at) >= '" . $startDate . "'
                            and date(customers.created_at) <= '" . $endDate . "'
                            and user_id = users.id
                            " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                        ) as total_leads
                    ")
                        ->withCount(['leads as compare_total_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                            if (request()->product_brand_id) {
                                $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as active_leads' => function ($q) use ($startDate, $endDate) {
                            $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                            if (request()->product_brand_id) {
                                $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                            if (request()->product_brand_id) {
                                $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['userActivities as total_activities' => function ($q) use ($startDate, $endDate) {
                            // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                            $q->whereCreatedAtRange($startDate, $endDate);
                            // });

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['userActivities as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                            // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                            $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                            // });

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                            if ($channelId) $q->where('channel_id', $channelId);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withSum([
                            'activityBrandValues as total_estimation' => function ($q) use ($startDate, $endDate) {
                                $q->whereCreatedAtRange($startDate, $endDate);
                                // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                                if (request()->product_brand_id) {
                                    $q->where('product_brand_id', request()->product_brand_id);
                                }
                            }
                        ], 'estimated_value')
                        ->withSum([
                            'activityBrandValues as compare_total_estimation' => function ($q) use ($startDateCompare, $endDateCompare) {
                                $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                                // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                                if (request()->product_brand_id) {
                                    $q->where('product_brand_id', request()->product_brand_id);
                                }
                            }
                        ], 'estimated_value')
                        ->withSum(['userOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                            $q->whereCreatedAtRange($startDate, $endDate);
                            // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                            $q->whereDoesntHave('orderPayments');
                            $q->whereNotIn('status', [5, 6]);
                        }], 'total_price')
                        ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereDoesntHave('orderPayments');
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        }], 'total_price')
                        ->withCount([
                            'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withSum([
                            'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                                $q->whereDeal($startDate, $endDate);
                            }
                        ], 'total_price')
                        ->withSum([
                            'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                                $q->whereDeal($startDateCompare, $endDateCompare);
                            }
                        ], 'total_price');
                    // ->withSum([
                    //     'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                    //         $q->whereDeal($startDate, $endDate)
                    //             ->whereNotNull('interior_design_id');
                    //         if ($channelId) $q->where('channel_id', $channelId);
                    //     }
                    // ], 'total_price')
                    // ->withCount([
                    //     'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                    //         $q->whereDeal($startDate, $endDate)
                    //             ->whereNotNull('interior_design_id');
                    //         if ($channelId) $q->where('channel_id', $channelId);
                    //     }
                    // ], 'total_price');
                }]);

            if ($channelId) {
                $query = $query->where('id', $channelId);
            }

            $result = $query->get();

            $data = [];
            $summary_new_leads = 0;
            $summary_compare_new_leads = 0;
            $summary_active_leads = 0;
            $summary_compare_active_leads = 0;
            $summary_total_activities = 0;
            $summary_compare_total_activities = 0;
            $summary_hot_activities = 0;
            $summary_warm_activities = 0;
            $summary_cold_activities = 0;
            $summary_estimation = 0;
            $summary_compare_estimation = 0;
            $summary_quotation = 0;
            $summary_compare_quotation = 0;
            $summary_deals = 0;
            $summary_total_deals_transaction = 0;
            $summary_compare_deals = 0;
            // $summary_interior_design = 0;
            // $summary_interior_design_total_transaction = 0;
            foreach ($result as $channel) {
                foreach ($channel->sales as $sales) {
                    $pbs = $this->getPbs($sales, $startDate, $endDate, $startDateCompare, $endDateCompare, $channelId);

                    $data['details'][] = [
                        'id' => $sales->id,
                        'name' => $sales->name,
                        'new_leads' => [
                            'value' => (int)$sales->total_leads ?? 0,
                            'compare' => (int)$sales->compare_total_leads ?? 0,
                            'target_leads' => (int)$sales->target_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => (int)$sales->active_leads ?? 0,
                            'compare' => (int)$sales->compare_active_leads ?? 0,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => (int)$sales->total_activities ?? 0,
                                'compare' => (int)$sales->compare_total_activities ?? 0,
                                'target_activities' => (int)$sales->target_activities ?? 0,
                            ],
                            'hot_activities' => (int)$sales->hot_activities ?? 0,
                            'warm_activities' => (int)$sales->warm_activities ?? 0,
                            'cold_activities' => (int)$sales->cold_activities ?? 0,
                        ],
                        'estimation' => [
                            'value' => $pbs['estimated_value'],
                            'compare' => $pbs['compare_estimated_value'],
                            // 'value' => (int)$sales->total_estimation ?? 0,
                            // 'compare' => (int)$sales->compare_total_estimation ?? 0,
                        ],
                        'quotation' => [
                            'value' => (int) $sales->total_quotation ?? 0,
                            'compare' => (int) $sales->compare_total_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => potongPPN($sales->total_deals),
                            'compare' => potongPPN($sales->compare_total_deals),
                            'total_transaction' => $sales->total_deals_transaction,
                            'target_deals' => (int)$sales->target_deals ?? 0,
                        ],
                        // 'interior_design' => [
                        //     'value' => potongPPN($sales->interior_design),
                        //     'total_transaction' => (int)$sales->interior_design_total_transaction ?? 0,
                        // ],
                        // 'retail' => [
                        //     'value' => potongPPN($sales->total_deals - $sales->interior_design),
                        //     'total_transaction' => (int)($sales->total_deals_transaction - $sales->interior_design_total_transaction) ?? 0,
                        // ],
                    ];

                    $summary_new_leads += (int)$sales->total_leads ?? 0;
                    $summary_compare_new_leads += (int)$sales->compare_total_leads ?? 0;
                    $summary_active_leads += (int)$sales->active_leads ?? 0;
                    $summary_compare_active_leads += (int)$sales->compare_active_leads ?? 0;
                    $summary_total_activities += (int)$sales->total_activities ?? 0;
                    $summary_compare_total_activities += (int)$sales->compare_total_activities ?? 0;
                    $summary_hot_activities += (int)$sales->hot_activities ?? 0;
                    $summary_warm_activities += (int)$sales->warm_activities ?? 0;
                    $summary_cold_activities += (int)$sales->cold_activities ?? 0;
                    $summary_estimation += $pbs['estimated_value'];
                    $summary_compare_estimation += $pbs['compare_estimated_value'];
                    // $summary_estimation += (int)$sales->total_estimation ?? 0;
                    // $summary_compare_estimation += (int)$sales->compare_total_estimation ?? 0;
                    $summary_quotation += (int)$sales->total_quotation ?? 0;
                    $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
                    $summary_deals += (int)$sales->total_deals ?? 0;
                    $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
                    $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
                    // $summary_interior_design += (int)$sales->interior_design ?? 0;
                    // $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
                }
            }

            $data['summary'] = [
                'new_leads' => [
                    'value' => $summary_new_leads,
                    'compare' => $summary_compare_new_leads,
                ],
                'active_leads' => [
                    'value' => $summary_active_leads,
                    'compare' => $summary_compare_active_leads,
                ],
                'follow_up' => [
                    'total_activities' => [
                        'value' => $summary_total_activities,
                        'compare' => $summary_compare_total_activities,
                    ],
                    'hot_activities' => $summary_hot_activities,
                    'warm_activities' => $summary_warm_activities,
                    'cold_activities' => $summary_cold_activities,
                ],
                'estimation' => [
                    'value' => $summary_estimation,
                    'compare' => $summary_compare_estimation,
                ],
                'quotation' => [
                    'value' => $summary_quotation,
                    'compare' => $summary_compare_quotation,
                ],
                'deals' => [
                    'value' => potongPPN($summary_deals),
                    'compare' => potongPPN($summary_compare_deals),
                    'total_transaction' => $summary_total_deals_transaction,
                ],
                // 'interior_design' => [
                //     'value' => potongPPN($summary_interior_design),
                //     'total_transaction' => $summary_interior_design_total_transaction,
                // ],
                // 'retail' => [
                //     'value' => potongPPN($summary_deals - $summary_interior_design),
                //     'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                // ],
            ];

            return response()->json(array_merge($data, $infoDate));
        }

        // else sales

        $target_leads = DB::table('new_targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::LEAD)->whereDate('start_date', '>=', $startTargetDate)->whereDate('end_date', '<=', $endTargetDate)->first()?->target ?? 0;

        $target_activities = DB::table('targets')->selectRaw('SUM(target) as target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', 7)->whereDate('created_at', '>=', $startTargetDate)->whereDate('created_at', '<=', $endTargetDate)->first()?->target ?? 0;

        $query = User::selectRaw('id, name, type');
        // if ($startDate->month == $endDate->month) {
        $query = $query->selectRaw("users.id, users.name, users.channel_id, (SELECT SUM(target) FROM targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(created_at) >= '" . $startTargetDate . "' AND DATE(created_at) <= '" . $endTargetDate . "') as target_deals");
        // }

        $query = $query->selectRaw("(
                        SELECT
                            count(DISTINCT customer_id)
                        from
                            leads
                            JOIN customers on customers.id = leads.customer_id
                        WHERE
                            date(customers.created_at) >= '" . $startDate . "'
                            and date(customers.created_at) <= '" . $endDate . "'
                            and user_id = users.id
                            " . ($channelId ? " and channel_id='" . $channelId . "'" : null) . "
                        ) as total_leads
                    ")
            ->withCount(['leads as compare_total_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                $q->whereCreatedAtRange($startDateCompare, $endDateCompare);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as active_leads' => function ($q) use ($startDate, $endDate) {
                $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as compare_active_leads' => function ($q) use ($startDateCompare, $endDateCompare) {
                $q->whereNotIn('status', [LeadStatus::EXPIRED])->whereNotIn('type', [LeadType::DROP]);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['userActivities as total_activities' => function ($q) use ($startDate, $endDate) {
                // $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate);
                // });

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['userActivities as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                // $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                // });

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal())->whereHas('leadActivities', fn ($q2) => $q2->where('status', 1)->whereCreatedAtRange($startDate, $endDate));

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 2)->whereCreatedAtRange($startDate, $endDate));

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', 3)->whereCreatedAtRange($startDate, $endDate));

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withSum([
                'activityBrandValues as total_estimation' => function ($q) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);
                    // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                    if (request()->product_brand_id) {
                        $q->where('product_brand_id', request()->product_brand_id);
                    }
                }
            ], 'estimated_value')
            ->withSum([
                'activityBrandValues as compare_total_estimation' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

                    if (request()->product_brand_id) {
                        $q->where('product_brand_id', request()->product_brand_id);
                    }
                }
            ], 'estimated_value')
            ->withSum(['userOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate);
                // $q->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
                $q->whereDoesntHave('orderPayments');
                $q->whereNotIn('status', [5, 6]);
            }], 'total_price')
            ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                $q->whereDoesntHave('orderPayments');
                $q->whereNotIn('status', [5, 6]);
                $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
            }], 'total_price')
            ->withCount([
                'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereDeal($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                    $q->whereDeal($startDate, $endDate);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereDeal($startDateCompare, $endDateCompare);
                }
            ], 'total_price');
        // ->withSum([
        //     'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
        //         $q->whereDeal($startDate, $endDate)
        //             ->whereNotNull('interior_design_id');
        //         if ($channelId) $q->where('channel_id', $channelId);
        //     }
        // ], 'total_price')
        // ->withCount([
        //     'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
        //         $q->whereDeal($startDate, $endDate)
        //             ->whereNotNull('interior_design_id');
        //         if ($channelId) $q->where('channel_id', $channelId);
        //     }
        // ], 'total_price');

        $result = $query->where('id', $user->id)->first();
        $pbs = $this->getPbs($result, $startDate, $endDate, $startDateCompare, $endDateCompare, $channelId);

        $data['details'][] = [
            'id' => $result->id,
            'name' => $result->name,
            'new_leads' => [
                'value' => (int)$result->total_leads ?? 0,
                'compare' => (int)$result->compare_total_leads ?? 0,
                'target_leads' => (int)$target_leads ?? 0,
            ],
            'active_leads' => [
                'value' => (int)$result->active_leads ?? 0,
                'compare' => (int)$result->compare_active_leads ?? 0,
            ],
            'follow_up' => [
                'total_activities' => [
                    'value' => (int)$result->total_activities ?? 0,
                    'compare' => (int)$result->compare_total_activities ?? 0,
                    'target_activities' => (int)$target_activities ?? 0,
                ],
                'hot_activities' => (int)$result->hot_activities ?? 0,
                'warm_activities' => (int)$result->warm_activities ?? 0,
                'cold_activities' => (int)$result->cold_activities ?? 0,
            ],
            'estimation' => [
                'value' => $pbs['estimated_value'],
                'compare' => $pbs['compare_estimated_value'],
            ],
            'quotation' => [
                'value' => (int) $result->total_quotation ?? 0,
                'compare' => (int) $result->compare_total_quotation ?? 0,
            ],
            'deals' => [
                'value' => potongPPN($result->total_deals),
                'compare' => potongPPN($result->compare_total_deals),
                'total_transaction' => (int)$result->total_deals_transaction ?? 0,
                'target_deals' => (int)$result->target_deals ?? 0,
            ],
            // 'interior_design' => [
            //     'value' => potongPPN($result->interior_design),
            //     'total_transaction' => (int)$result->interior_design_total_transaction ?? 0,
            // ],
            // 'retail' => [
            //     'value' => potongPPN($result->total_deals - $result->interior_design),
            //     'total_transaction' => (int)($result->total_deals_transaction - $result->interior_design_total_transaction) ?? 0,
            // ],
        ];

        $data['summary'] = $data['details'][0];

        return response()->json(array_merge($data, $infoDate));
    }

    public function invoice(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $user = user();

        $query = Order::selectRaw("
        orders.id,
        activities.id as activity_id,
        orders.invoice_number,
        orders.total_price,
        orders.created_at,
        IF(customers.last_name IS NOT NULL, CONCAT(customers.first_name, ' ', customers.last_name),
        customers.first_name) as customer,
        users.name as sales,
        channels.name as channel
        ")
            ->join('activities', 'activities.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('channels', 'channels.id', '=', 'orders.channel_id');

        if ($request->invoice_type == 'deals') {
            $query = $query->whereDeal($startDate, $endDate);
            // $query = $query->join('payments', fn($join) => $join->on('orders.id', '=', 'payments.order_id')->where('payments.status', '<>', 2))
            // $query = $query->whereHas('orderPayments', fn ($q2) => $q2->where('status', '<>', 2))
            //     ->whereNotIn('orders.payment_status', [5])
            //     ->whereDealAtRange($startDate, $endDate);
        } elseif ($request->invoice_type == 'settlement') {
            $query = $query->whereIn('orders.payment_status', [3, 4])
                ->whereDealAtRange($startDate, $endDate);
        } elseif ($request->invoice_type == 'retail') {
            // $query = $query->join('payments', fn($join) => $join->on('orders.id', '=', 'payments.order_id')->where('payments.status', '<>', 2))
            $query = $query->whereHas('orderPayments', fn ($q2) => $q2->where('status', '<>', 2))
                ->whereNotIn('orders.payment_status', [5])
                ->whereDealAtRange($startDate, $endDate);
            // ->whereNull('orders.interior_design_id');
        } else {
            // quotation
            $query = $query->whereCreatedAtRange($startDate, $endDate)
                ->whereDoesntHave('orderPayments');
            // ->where(fn ($q2) => $q2->whereDoesntHave('orderPayments')->orWhereHas('orderPayments', fn ($q3) => $q3->where('status', 0)));
        }

        if ($request->payment_type == 'none') {
            $query = $query->where('orders.payment_status', OrderPaymentStatus::NONE);
        } elseif ($request->payment_type == 'partial') {
            $query = $query->where('orders.payment_status', OrderPaymentStatus::PARTIAL)
                ->whereDealAtRange($startDate, $endDate);
        } elseif ($request->payment_type == 'down_payment') {
            $query = $query->where('orders.payment_status', OrderPaymentStatus::DOWN_PAYMENT)
                ->whereDealAtRange($startDate, $endDate);
        } elseif ($request->payment_type == 'overpayment') {
            $query = $query->where('orders.payment_status', OrderPaymentStatus::OVERPAYMENT)
                ->whereDealAtRange($startDate, $endDate);
        } elseif ($request->payment_type == 'settlement') {
            $query = $query->where('orders.payment_status', OrderPaymentStatus::SETTLEMENT)
                ->whereDealAtRange($startDate, $endDate);
        } elseif ($request->payment_type == 'deals') {
            $query = $query->whereDeal($startDate, $endDate);
            // $query = $query->whereHas('orderPayments', fn ($q2) => $q2->where('status', '<>', 2))
            //     ->whereNotIn('orders.payment_status', [5])
            //     ->whereDealAtRange($startDate, $endDate);
        } else {
            // quotation
            $query = $query->whereNotIn('orders.payment_status', [5]);
        }

        // $userType = null;
        // if ($user->is_director) {
        //     $userType = 'director';
        // } else if ($user->is_supervisor) {
        //     if ($user->supervisor_type_id == 1) {
        //         $userType = 'sl';
        //     } else if ($user->supervisor_type_id == 2) {
        //         $userType = 'bum';
        //     } else if ($user->supervisor_type_id == 3) {
        //         $userType = 'hs';
        //     }
        // } else if ($user->is_sales) {
        //     $userType = 'sales';
        // }

        // if (in_array($userType, ['director', 'hs'])) {
        //     $companyIds = $user->company_ids ?? $user->companies->pluck('id')->all();
        //     $query = $query->whereIn('company_id', $companyIds);
        // } elseif (in_array($userType, ['bum', 'sl'])) {
        //     $query = $query->whereIn('channel_id', $user->channels->pluck('id')->all());
        // } else {
        //     $query = $query->where('user_id', $user->id);
        // }

        $userType = $request->user_type ?? null;
        $id = $request->id ?? null;

        if ($userType && $id) {
            $user = match ($userType) {
                'bum' => User::findOrFail($id),
                'store' => Channel::findOrFail($id),
                'store_leader' => User::findOrFail($id),
                'sales' => User::findOrFail($id),
                default => $user,
            };
        }

        if ($user instanceof Channel) {
            $query = $query->where('orders.channel_id', $user->id);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            // $companyIds = $user->company_ids ?? $user->companies->pluck('id')->all();
            // $query = $query->whereIn('orders.company_id', $companyIds);
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $query = $query->whereIn('orders.channel_id', $user->channels->pluck('id')->all());
        } else {
            // sales
            $query = $query->where('orders.user_id', $user->id);
        }

        // if ($request->company_id) $query = $query->where('orders.company_id', $request->company_id);
        if ($request->channel_id) $query = $query->where('orders.channel_id', $request->channel_id);
        if ($request->user_id) $query = $query = $query->where('orders.user_id', $request->user_id);

        if ($request->product_brand_id) {
            $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
        }

        if ($request->search_type && $request->name) {
            $searchType = $request->search_type;
            $name = $request->name;

            switch ($searchType) {
                case 'customer':
                    $query = $query->whereHas('customer', fn ($q) => $q->where('first_name', 'LIKE', "%$name%")->orWhere('last_name', 'LIKE', "%$name%"));
                    break;
                case 'sales':
                    $query = $query->whereHas('user', fn ($q) => $q->where('name', 'LIKE', "%$name%"));
                    break;
                case 'invoice_number':
                    $query = $query->where("orders.invoice_number", 'LIKE', "%$name%");
                    break;
                case 'channel':
                    $query = $query->whereHas('channel', fn ($q) => $q->where('name', 'LIKE', "%$name%"));
                    break;

                default:

                    break;
            }
        }

        if ($request->payment_status) {
            $paymentStatus = match ($request->payment_status) {
                'NONE' => OrderPaymentStatus::NONE,
                'PARTIAL' => OrderPaymentStatus::PARTIAL,
                'SETTLEMENT' => OrderPaymentStatus::SETTLEMENT,
                'OVERPAYMENT' => OrderPaymentStatus::OVERPAYMENT,
                'REFUNDED' => OrderPaymentStatus::REFUNDED,
                'DOWN_PAYMENT' => OrderPaymentStatus::DOWN_PAYMENT,
                default => null,
            };

            if (!is_null($paymentStatus)) $query = $query->where('orders.payment_status', $paymentStatus);
        }

        $result = $query->whereNotIn('orders.status', [5, 6])->orderBy('orders.id', 'desc')->get();

        return response()->json($result);
    }

    public function leads(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if ((request()->has('start_date') && request()->start_date != '') && (request()->has('end_date') && request()->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', request()->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', request()->end_date)->endOfDay();
        }

        $user = user();

        $activityStatus = match ($request->status) {
            'HOT' => ActivityStatus::HOT,
            'WARM' => ActivityStatus::WARM,
            'COLD' => ActivityStatus::COLD,
            'NONE' => 'NONE',
            default => null,
        };

        $query = Lead::with(['latestActivity' => function ($q) use ($activityStatus) {
                $q->selectRaw("
                    lead_id,
                    (
                        CASE
                            WHEN activities.status = 1 THEN 'HOT'
                            WHEN activities.status = 2 THEN 'WARM'
                            WHEN activities.status = 3 THEN 'COLD'
                            WHEN activities.status = 4 THEN 'CLOSED'
                            ELSE 'No follow up'
                        END
                    ) as latest_status
                ");

                if ($activityStatus) $q->where('status', $activityStatus);
            }])
            ->selectRaw("
            leads.id,
            leads.created_at,
            leads.label,
            IF(
                customers.last_name IS NOT NULL,
                CONCAT(customers.first_name, ' ', customers.last_name),
                customers.first_name
            ) as customer,
            customers.phone,
            customers.email,
            users.name as sales,
            channels.name as channel
            ")
            ->join('customers', 'customers.id', '=', 'leads.customer_id')
            ->join('users', 'users.id', '=', 'leads.user_id')
            ->join('channels', 'channels.id', '=', 'leads.channel_id')
            ->whereNotIn('leads.status', [LeadStatus::EXPIRED])->whereNotIn('leads.type', [LeadType::DROP]);


        $isActive = $request->is_active ?? null;
        if ($isActive && $isActive == true) {
            $query = $query->groupBy('leads.id');
        } else {
            $query = $query->whereCreatedAtRange($startDate, $endDate)
                ->whereHas('customer', fn ($customer) => $customer->whereCreatedAtRange($startDate, $endDate))
                ->groupBy('leads.customer_id');
        }

        $userType = $request->user_type ?? null;
        $id = $request->id ?? null;

        if ($userType && $id) {
            $user = match ($userType) {
                'bum' => User::findOrFail($id),
                'store' => Channel::findOrFail($id),
                'store_leader' => User::findOrFail($id),
                'sales' => User::findOrFail($id),
                default => $user,
            };
        }

        if ($user instanceof Channel) {
            $query = $query->where('leads.channel_id', $user->id);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            // $companyIds = $user->company_ids ?? $user->company_id;
            // $query = $query->whereIn('leads.channel_id', Channel::whereIn('company_id', $companyIds)->pluck('id')->all());
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $query = $query->whereIn('leads.channel_id', $user->channels->pluck('id')->all());
        } else {
            // sales
            $query = $query->where('leads.user_id', $user->id);
        }

        // if ($companyId = $request->company_id) $query = $query->where('leads.channel_id', Channel::where('company_id', $companyId)->pluck('id')->all());
        if ($request->channel_id) $query = $query->where('leads.channel_id', $request->channel_id);
        if ($name = $request->name) $query = $query->whereHas('customer', fn ($q) => $q->where('first_name', 'LIKE', "%$name%")->orWhere('last_name', 'LIKE', "%$name%"));

        if ($request->product_brand_id) {
            $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
        }

        if (!is_null($activityStatus) && $activityStatus != 'NONE') {
            $query = $query->whereHas('latestActivity', fn ($q) => $q->where('status', $activityStatus));
        } elseif ($activityStatus == 'NONE') {
            $query = $query->whereDoesntHave('leadActivities');
        }

        $result = $query->orderBy('leads.id', 'desc')->simplePaginate($request->perPage ?? 20);

        return response()->json($result);
    }

    public function interiorDesigns(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if ((request()->has('start_date') && request()->start_date != '') && (request()->has('end_date') && request()->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', request()->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', request()->end_date)->endOfDay();
        }

        $user = user();

        $query = Order::selectRaw("
        interior_designs.id as id,
        SUM(orders.total_price) as revenue,
        interior_designs.name as interior_design
        ")
            ->join('interior_designs', 'interior_designs.id', '=', 'orders.interior_design_id')
            ->whereDeal($startDate, $endDate)
            ->whereNotNull('orders.interior_design_id');

        $userType = $request->user_type ?? null;
        $id = $request->id ?? null;

        if ($userType && $id) {
            $user = match ($userType) {
                'bum' => User::findOrFail($id),
                'store' => Channel::findOrFail($id),
                'store_leader' => User::findOrFail($id),
                'sales' => User::findOrFail($id),
                default => $user,
            };
        }

        if ($user instanceof Channel) {
            $query = $query->where('channel_id', $user->id);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            // $companyIds = $user->company_ids ?? $user->companies->pluck('id')->all();
            // $query = $query->whereIn('company_id', $companyIds);
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $query = $query->whereIn('channel_id', $user->channels->pluck('id')->all());
        } else {
            // sales
            $query = $query->where('user_id', $user->id);
        }

        // if ($request->company_id) $query = $query->where('company_id', $request->company_id);
        if ($request->channel_id) $query = $query->where('channel_id', $request->channel_id);
        if ($request->user_id) $query = $query = $query->where('user_id', $request->user_id);

        if ($request->product_brand_id) {
            $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
        }

        $result = $query->groupBy('orders.interior_design_id')->orderBy('orders.id', 'desc')->get();

        return response()->json($result);
    }

    public function interiorDesignDetails(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if ((request()->has('start_date') && request()->start_date != '') && (request()->has('end_date') && request()->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', request()->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', request()->end_date)->endOfDay();
        }

        $query = Order::selectRaw("
        orders.id,
        activities.id as activity_id,
        orders.invoice_number,
        orders.total_price,
        users.name as sales,
        channels.name as channel,
        orders.created_at
        ")
            ->join('activities', 'activities.order_id', '=', 'orders.id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('channels', 'channels.id', '=', 'orders.channel_id')
            ->where('orders.interior_design_id', $request->interior_design_id)
            ->whereDeal($startDate, $endDate)
            ->whereNotNull('orders.interior_design_id');

        // if ($request->company_id) $query = $query->where('company_id', $request->company_id);
        if ($request->channel_id) $query = $query->where('channel_id', $request->channel_id);
        if ($request->user_id) $query = $query = $query->where('user_id', $request->user_id);

        if ($request->product_brand_id) {
            $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
        }

        $result = $query->orderBy('orders.id', 'desc')->get();

        return response()->json($result);
    }

    public function brandDetails(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $user = user();
        // dump($user);

        $userType = $request->user_type ?? null;
        $id = $request->id ?? $user->id;

        if ($userType && $id) {
            $user = match ($userType) {
                'bum' => User::findOrFail($id),
                'store' => Channel::findOrFail($id),
                'store_leader' => User::findOrFail($id),
                'sales' => User::findOrFail($id),
                'director' => User::findOrFail($id),
                default => $user,
            };
        }

        // if ($request->company_id) {
        //     $companyId = $request->company_id;
        // } else {
        //     $companyId = $user->company_id;
        // }

        $filterChannelId = $request->channel_id ?? null;

        // $companyChannelIds = \App\Models\Channel::where('company_id', $companyId)->pluck('id')?->all() ?? [];

        if ($user instanceof Channel) {
            $target_brands = DB::table('new_targets')->where('model_type', 'channel')->where('model_id', $user->id)->where('type', NewTargetType::PRODUCT_BRAND)->whereDate('start_date', '>=', $startDate)->whereDate('end_date', '<=', $endDate)->pluck('target', 'target_id');
            // $salesIds = User::where('channel_id', $user->id)->where('type', 2)->pluck('id')->all();
            $query = ProductBrand::selectRaw('id, name as product_brand, (SELECT name FROM brand_categories WHERE id=product_brands.brand_category_id) as brand_category')
                ->with(['activityBrandValues' => function ($q) use ($id, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        // ->whereIn('user_id', $salesIds)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');
                    $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $id));
                }])
                ->with(['activityBrandValuesDeals' => function ($q) use ($id, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        // ->whereIn('user_id', $salesIds)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)->where('channel_id', $id)
                    );
                }]);
                // ->where('company_id', $user->company_id);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            // $target_brands = DB::table('new_targets')->where('model_type', 'company')->where('model_id', $user->company_id)->where('type', NewTargetType::PRODUCT_BRAND)->whereDate('start_date', '>=', $startDate)->whereDate('end_date', '<=', $endDate)->pluck('target', 'target_id');
            $target_brands = DB::table('new_targets')->where('type', NewTargetType::PRODUCT_BRAND)->whereDate('start_date', '>=', $startDate)->whereDate('end_date', '<=', $endDate)->pluck('target', 'target_id');

            // $companyIds = [$companyId] ?? $user->companies->pluck('id')->all() ?? [];

            $query = ProductBrand::selectRaw('id, name as product_brand, (SELECT name FROM brand_categories WHERE id=product_brands.brand_category_id) as brand_category')
                ->with(['activityBrandValues' => function ($q) use ($filterChannelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        // ->where('user_id', $id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');
                    // $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', Channel::whereIn('company_id', $companyIds)->pluck('id')->all()));
                    $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', Channel::pluck('id')->all()));
                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }])
                ->with(['activityBrandValuesDeals' => function ($q) use ($filterChannelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        // ->where('user_id', $id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas(
                        'order',
                        // fn ($q2) => $q2->whereDeal($startDate, $endDate)->whereIn('company_id', $companyIds)
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)
                    );
                    if ($filterChannelId) $q->whereHas('order', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }]);
                // ->whereIn('company_id', $companyIds);
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $target_brands = DB::table('new_targets')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::PRODUCT_BRAND)->whereDate('start_date', '>=', $startDate)->whereDate('end_date', '<=', $endDate)->pluck('target', 'target_id');

            $channelIds = $user->channels->pluck('id')->all();

            $query = ProductBrand::selectRaw('id, name as product_brand, (SELECT name FROM brand_categories WHERE id=product_brands.brand_category_id) as brand_category')
                ->with(['activityBrandValues' => function ($q) use ($filterChannelId, $channelIds, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        // ->where('user_id', $id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');
                    $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', $channelIds));
                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }])
                ->with(['activityBrandValuesDeals' => function ($q) use ($filterChannelId, $channelIds, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        // ->where('user_id', $id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)->whereIn('channel_id', $channelIds)
                    );
                    if ($filterChannelId) $q->whereHas('order', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }]);
                // ->where('company_id', $companyId);
        } else {
            $target_brands = DB::table('new_targets')->where('model_type', 'user')->where('model_id', $user->id)->where('type', NewTargetType::PRODUCT_BRAND)->whereDate('start_date', '>=', $startDate)->whereDate('end_date', '<=', $endDate)->pluck('target', 'target_id');

            $query = ProductBrand::selectRaw('id, name as product_brand, (SELECT name FROM brand_categories WHERE id=product_brands.brand_category_id) as brand_category')
                ->with(['activityBrandValues' => function ($q) use ($id, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        ->where('user_id', $id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');
                }])
                ->with(['activityBrandValuesDeals' => function ($q) use ($id, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        ->where('user_id', $id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)
                    );
                }]);
                // ->where('company_id', $user->company_id);
        }

        // if (count($companyChannelIds) > 0) {
        //     $query = $query->where('product_brands.company_id', $companyId);
        // }

        // if ($filterChannelId = $request->channel_id) {
        //     $cid = Channel::find($filterChannelId)?->company_id ?? null;
        //     if ($cid) $query = $query->where('product_brands.company_id', $cid);
        // }
        // if ($userId = $request->user_id) $query = $query->where('product_brands.user_id', $userId);

        if ($productBrandId = $request->product_brand_id) {
            $query = $query->where('product_brands.product_brand_id', $productBrandId);
        }

        $result = $query
            ->orderBy('product_brands.id')
            ->get();

        $data = [];
        foreach ($result as $productBrand) {
            $activityBrandValues = $productBrand->activityBrandValues?->unique('lead_id');
            $activityBrandValuesDeals = $productBrand->activityBrandValuesDeals?->unique('lead_id');

            $data[$productBrand->id] = [
                'id' => $productBrand->id,
                'product_brand' => $productBrand->product_brand,
                'brand_category' => $productBrand->brand_category ?? 'No Brand Category',
            ];
            $data[$productBrand->id]['estimated_value'] = $activityBrandValues?->sum('estimated_value');
            $data[$productBrand->id]['quotation'] = $activityBrandValues?->sum('total_order_value');
            $data[$productBrand->id]['order_value'] = $activityBrandValuesDeals?->sum('total_order_value');
            $data[$productBrand->id]['target'] = isset($target_brands) && count($target_brands) > 0 ? (int)($target_brands[$productBrand->id] ?? 0) : 0;
        }

        return response()->json(array_values($data));
    }

    public function getPbs($user, $startDate, $endDate, $startDateCompare, $endDateCompare, $filterChannelId = null, $companyId = null)
    {
        $query = ProductBrand::selectRaw('id');
        if ($user instanceof Channel) {
            $query = $query->with(['activityBrandValues' => function ($q) use ($user, $filterChannelId, $startDate, $endDate) {
                $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $user->id));
                $q->whereCreatedAtRange($startDate, $endDate)
                    // ->where('user_id', $user->id)
                    ->orderBy('lead_id', 'desc')
                    ->orderBy('activity_id', 'desc');
                if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            }])
                ->with(['activityBrandValuesDeals' => function ($q) use ($user, $filterChannelId, $startDateCompare, $endDateCompare) {
                    $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $user->id));
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
                        // ->where('user_id', $user->id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');
                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }]);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            // $companyIds = [$companyId] ?? $user->companies->pluck('id')->all() ?? [];

            $query = $query->with(['activityBrandValues' => function ($q) use ($filterChannelId, $startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate)
                    // ->where('user_id', $id)
                    ->orderBy('lead_id', 'desc')
                    ->orderBy('activity_id', 'desc');
                // $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', Channel::whereIn('company_id', $companyIds)->pluck('id')->all()));
                $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', Channel::pluck('id')->all()));
                if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            }])
                ->with(['activityBrandValuesDeals' => function ($q) use ($filterChannelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        // ->where('user_id', $id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas(
                        'order',
                        // fn ($q2) => $q2->whereDeal($startDate, $endDate)->whereIn('company_id', $companyIds)
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)
                    );
                    if ($filterChannelId) $q->whereHas('order', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }]);
                // ->whereIn('company_id', $companyIds);
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $channelIds = $user->channels->pluck('id')->all();

            $query = $query->with(['activityBrandValues' => function ($q) use ($filterChannelId, $channelIds, $startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate)
                    // ->where('user_id', $id)
                    ->orderBy('lead_id', 'desc')
                    ->orderBy('activity_id', 'desc');
                $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', $channelIds));
                if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            }])
                ->with(['activityBrandValuesDeals' => function ($q) use ($filterChannelId, $channelIds, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate)
                        // ->where('user_id', $id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');

                    $q->whereHas(
                        'order',
                        fn ($q2) => $q2->whereDeal($startDate, $endDate)->whereIn('channel_id', $channelIds)
                    );
                    if ($filterChannelId) $q->whereHas('order', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }]);
                // ->where('company_id', $companyId);
        } else {
            $query = $query->with(['activityBrandValues' => function ($q) use ($user, $filterChannelId, $startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate)
                    ->where('user_id', $user->id)
                    ->orderBy('lead_id', 'desc')
                    ->orderBy('activity_id', 'desc');
                if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
            }])
                ->with(['activityBrandValuesDeals' => function ($q) use ($user, $filterChannelId, $startDateCompare, $endDateCompare) {
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare)
                        ->where('user_id', $user->id)
                        ->orderBy('lead_id', 'desc')
                        ->orderBy('activity_id', 'desc');
                    if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
                }]);
        }

        $estimated_value = 0;
        $compare_estimated_value = 0;
        foreach ($query->get() as $productBrand) {
            $activityBrandValues = $productBrand->activityBrandValues?->unique('lead_id');
            $activityBrandValuesDeals = $productBrand->activityBrandValuesDeals?->unique('lead_id');

            $estimated_value += $activityBrandValues?->sum('estimated_value');
            $compare_estimated_value += $activityBrandValuesDeals?->sum('estimated_value');
        }

        return [
            'estimated_value' => $estimated_value ?? 0,
            'compare_estimated_value' => $compare_estimated_value ?? 0
        ];
    }

    // public function brandDetails(Request $request)
    // {
    //     $startDate = Carbon::now()->startOfMonth();
    //     $endDate = Carbon::now()->endOfMonth();
    //     if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
    //         $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
    //         $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
    //     }

    //     $startDate = date('Y-m-d', strtotime($startDate));
    //     $endDate = date('Y-m-d', strtotime($endDate));

    //     $user = user();

    //     $userType = $request->user_type ?? null;
    //     $id = $request->id ?? $user->id;

    //     if ($userType && $id) {
    //         $user = match ($userType) {
    //             'bum' => User::findOrFail($id),
    //             'store' => Channel::findOrFail($id),
    //             'sales' => User::findOrFail($id),
    //             'director' => User::findOrFail($id),
    //             default => $user,
    //         };
    //     }

    //     if ($request->company_id) {
    //         $companyId = $request->company_id;
    //     } else {
    //         $companyId = $user->company_id;
    //     }

    //     $filterChannelId = $request->channel_id ?? null;

    //     // $companyChannelIds = \App\Models\Channel::where('company_id', $companyId)->pluck('id')?->all() ?? [];

    //     if ($user instanceof Channel) {
    //         $query = ProductBrand::selectRaw('id, name as product_brand, (SELECT name FROM brand_categories WHERE id=product_brands.brand_category_id) as brand_category')
    //             ->with(['activityBrandValues' => function ($q) use ($id, $startDate, $endDate) {
    //                 $q->whereCreatedAtRange($startDate, $endDate)
    //                     // ->where('user_id', $id)
    //                     ->orderBy('lead_id', 'desc')
    //                     ->orderBy('activity_id', 'desc');
    //                 $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $id));
    //             }])
    //             ->with(['activityBrandValuesDeals' => function ($q) use ($id, $startDate, $endDate) {
    //                 $q->whereCreatedAtRange($startDate, $endDate)
    //                     // ->where('user_id', $id)
    //                     ->orderBy('lead_id', 'desc')
    //                     ->orderBy('activity_id', 'desc');

    //                 $q->whereHas('order', fn ($q2) => $q2->whereNotIn('status', [5, 6])->whereIn('payment_status', [2, 3, 4, 6])->whereDealAtRange($startDate, $endDate)->where('channel_id', $id));
    //             }])
    //             ->where('company_id', $user->company_id);
    //     } elseif ($user->type->is(UserType::DIRECTOR)) {
    //         $companyIds = [$companyId] ?? $user->companies->pluck('id')->all() ?? [];

    //         $query = ProductBrand::selectRaw('id, name as product_brand, (SELECT name FROM brand_categories WHERE id=product_brands.brand_category_id) as brand_category')
    //             ->with(['activityBrandValues' => function ($q) use ($filterChannelId, $companyIds, $startDate, $endDate) {
    //                 $q->whereCreatedAtRange($startDate, $endDate)
    //                     // ->where('user_id', $id)
    //                     ->orderBy('lead_id', 'desc')
    //                     ->orderBy('activity_id', 'desc');
    //                 $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', Channel::whereIn('company_id', $companyIds)->pluck('id')->all()));
    //                 if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
    //             }])
    //             ->with(['activityBrandValuesDeals' => function ($q) use ($filterChannelId, $companyIds, $startDate, $endDate) {
    //                 $q->whereCreatedAtRange($startDate, $endDate)
    //                     // ->where('user_id', $id)
    //                     ->orderBy('lead_id', 'desc')
    //                     ->orderBy('activity_id', 'desc');

    //                 $q->whereHas('order', fn ($q2) => $q2->whereNotIn('status', [5, 6])->whereIn('payment_status', [2, 3, 4, 6])->whereDealAtRange($startDate, $endDate)->whereIn('company_id', $companyIds));
    //                 if ($filterChannelId) $q->whereHas('order', fn ($q2) => $q2->where('channel_id', $filterChannelId));
    //             }])
    //             ->whereIn('company_id', $companyIds);
    //     } elseif ($user->type->is(UserType::SUPERVISOR)) {
    //         $channelIds = $user->channels->pluck('id')->all();

    //         $query = ProductBrand::selectRaw('id, name as product_brand, (SELECT name FROM brand_categories WHERE id=product_brands.brand_category_id) as brand_category')
    //             ->with(['activityBrandValues' => function ($q) use ($filterChannelId, $channelIds, $startDate, $endDate) {
    //                 $q->whereCreatedAtRange($startDate, $endDate)
    //                     // ->where('user_id', $id)
    //                     ->orderBy('lead_id', 'desc')
    //                     ->orderBy('activity_id', 'desc');
    //                 $q->whereHas('lead', fn ($q2) => $q2->whereIn('channel_id', $channelIds));
    //                 if ($filterChannelId) $q->whereHas('lead', fn ($q2) => $q2->where('channel_id', $filterChannelId));
    //             }])
    //             ->with(['activityBrandValuesDeals' => function ($q) use ($filterChannelId, $channelIds, $startDate, $endDate) {
    //                 $q->whereCreatedAtRange($startDate, $endDate)
    //                     // ->where('user_id', $id)
    //                     ->orderBy('lead_id', 'desc')
    //                     ->orderBy('activity_id', 'desc');

    //                 $q->whereHas('order', fn ($q2) => $q2->whereNotIn('status', [5, 6])->whereIn('payment_status', [2, 3, 4, 6])->whereDealAtRange($startDate, $endDate)->whereIn('channel_id', $channelIds));
    //                 if ($filterChannelId) $q->whereHas('order', fn ($q2) => $q2->where('channel_id', $filterChannelId));
    //             }])
    //             ->where('company_id', $companyId);
    //     } else {
    //         $query = ProductBrand::selectRaw('id, name as product_brand, (SELECT name FROM brand_categories WHERE id=product_brands.brand_category_id) as brand_category')
    //             ->with(['activityBrandValues' => function ($q) use ($id, $startDate, $endDate) {
    //                 $q->whereCreatedAtRange($startDate, $endDate)
    //                     ->where('user_id', $id)
    //                     ->orderBy('lead_id', 'desc')
    //                     ->orderBy('activity_id', 'desc');
    //             }])
    //             ->with(['activityBrandValuesDeals' => function ($q) use ($id, $startDate, $endDate) {
    //                 $q->whereCreatedAtRange($startDate, $endDate)
    //                     ->where('user_id', $id)
    //                     ->orderBy('lead_id', 'desc')
    //                     ->orderBy('activity_id', 'desc');

    //                 $q->whereHas('order', fn ($q2) => $q2->whereNotIn('status', [5, 6])->whereIn('payment_status', [2, 3, 4, 6])->whereDealAtRange($startDate, $endDate));
    //             }])
    //             ->where('company_id', $user->company_id);
    //     }

    //     // if (count($companyChannelIds) > 0) {
    //     //     $query = $query->where('product_brands.company_id', $companyId);
    //     // }

    //     // if ($filterChannelId = $request->channel_id) {
    //     //     $cid = Channel::find($filterChannelId)?->company_id ?? null;
    //     //     if ($cid) $query = $query->where('product_brands.company_id', $cid);
    //     // }
    //     // if ($userId = $request->user_id) $query = $query->where('product_brands.user_id', $userId);

    //     if ($productBrandId = $request->product_brand_id) {
    //         $query = $query->where('product_brands.product_brand_id', $productBrandId);
    //     }

    //     $result = $query
    //         ->orderBy('product_brands.id')
    //         ->get();

    //     $data = [];
    //     foreach ($result as $productBrand) {
    //         $activityBrandValues = $productBrand->activityBrandValues?->unique('lead_id');
    //         $activityBrandValuesDeals = $productBrand->activityBrandValuesDeals?->unique('lead_id');

    //         $data[$productBrand->id] = [
    //             'id' => $productBrand->id,
    //             'product_brand' => $productBrand->product_brand,
    //             'brand_category' => $productBrand->brand_category ?? 'No Brand Category',
    //         ];
    //         $data[$productBrand->id]['estimated_value'] = $activityBrandValues?->sum('estimated_value');
    //         $data[$productBrand->id]['quotation'] = $activityBrandValues?->sum('total_order_value');
    //         $data[$productBrand->id]['order_value'] = $activityBrandValuesDeals?->sum('total_order_value');
    //     }

    //     return response()->json(array_values($data));
    // }
}
