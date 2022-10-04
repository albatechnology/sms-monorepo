<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\ActivityStatus;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\OrderPaymentStatus;
use App\Enums\UserType;
use App\Models\ActivityBrandValue;
use App\Models\Channel;
use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewReportControllerCompare extends BaseApiController
{
    public function test(Request $request)
    {
        $startDate = Carbon::now();
        $endDateCompare = Carbon::now()->subDay();

        $endDate = $startDate;
        $startDateCompare = $endDateCompare;


        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDateCompare = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay()->subDay();

            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();

            $diff = $startDate->diffInDays($endDate);
            $startDateCompare = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay()->subDays($diff + 1);
        }

        $query = User::where('type', UserType::SUPERVISOR)
            ->where('supervisor_type_id', 2)
            ->where('company_id', 1)
            ->with(['channels' => function ($channel) use ($startDate, $endDate, $startDateCompare, $endDateCompare) {
                $channel->where('company_id', 1)
                    ->with(['sales' => function ($q) use ($startDate, $endDate, $startDateCompare, $endDateCompare) {
                        $q->withSum([
                            'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                            }
                        ], 'total_price')
                            ->withSum([
                                'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                                    $q->whereNotIn('status', [5, 6]);
                                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                                    $q->whereDealAtRange($startDateCompare, $endDateCompare);
                                }
                            ], 'total_price');
                    }]);
            }]);


        $result = $query->get();

        $total_deals = 0;
        $compare_total_deals = 0;
        foreach ($result as $bum) {
            foreach ($bum->channels as $channel) {
                foreach ($channel->sales as $sales) {
                    $total_deals += $sales->total_deals;
                    $compare_total_deals += $sales->compare_total_deals;
                }
            }
        }
        return response()->json([
            'total_deals' => $total_deals,
            'compare_total_deals' => $compare_total_deals,
            'result' => $result,
        ]);
    }

    public function index(Request $request)
    {
        $startDate = Carbon::now();
        $endDateCompare = Carbon::now()->subDay();

        $endDate = $startDate;
        $startDateCompare = $endDateCompare;

        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDateCompare = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay()->subDay();

            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();

            $diff = $startDate->diffInDays($endDate);
            $startDateCompare = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay()->subDays($diff + 1);
        }

        $user = user();

        $target_deals = 0;

        $userType = null;

        $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($user->is_director || $user->is_digital_marketing) {
            $userType = 'director';
            $target_deals = DB::table('targets')->select('target')->where('model_type', 'company')->where('model_id', $companyId)->where('type', 0)->whereDate('created_at', '>=', $startDate->startOfMonth())->whereDate('created_at', '<=', $startDate->endOfMonth())->first()?->target ?? 0;
        } else if ($user->is_supervisor) {
            if ($user->supervisor_type_id == 1) {
                $userType = 'sl';
            } else if ($user->supervisor_type_id == 2) {
                $userType = 'bum';
            } else if ($user->supervisor_type_id == 3) {
                $userType = 'hs';
            }

            $target_deals = DB::table('targets')->select('target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', 0)->whereDate('created_at', '>=', $startDate->startOfMonth())->whereDate('created_at', '<=', $startDate->endOfMonth())->first()?->target ?? 0;
        } else if ($user->is_sales) {
            $userType = 'sales';

            $target_deals = DB::table('targets')->select('target')->where('model_type', 'user')->where('model_id', $user->id)->where('type', 0)->whereDate('created_at', '>=', $startDate->startOfMonth())->whereDate('created_at', '<=', $startDate->endOfMonth())->first()?->target ?? 0;
        }

        if (in_array($userType, ['director', 'hs'])) {
            $query = User::selectRaw("id, name")
                ->where('company_id', $companyId)
                ->where('type', 2)
                ->withCount(['leads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
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
                ->withCount(['leads as total_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                        $q2->whereCreatedAtRange($startDate, $endDate);
                    });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_total_activities' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                        $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                        $q2->where('status', 1);
                        $q2->whereCreatedAtRange($startDate, $endDate);
                    });

                    if ($channelId) $q->where('channel_id', $channelId);

                    $q->where('status', 1);
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('status', 2);
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('status', 3);
                    $q->whereCreatedAtRange($startDate, $endDate);

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
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDateCompare, $endDateCompare);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        $q->whereNotNull('interior_design_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withCount([
                    'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        $q->whereNotNull('interior_design_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

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
            $summary_interior_design = 0;
            $summary_interior_design_total_transaction = 0;

            $data = [];
            foreach ($result as $sales) {
                $summary_new_leads += (int)$sales->total_leads ?? 0;
                $summary_compare_new_leads += (int)$sales->compare_total_leads ?? 0;
                $summary_active_leads += (int)$sales->active_leads ?? 0;
                $summary_compare_active_leads += (int)$sales->compare_active_leads ?? 0;
                $summary_total_activities += (int)$sales->total_activities ?? 0;
                $summary_compare_total_activities += (int)$sales->compare_total_activities ?? 0;
                $summary_hot_activities += (int)$sales->hot_activities ?? 0;
                $summary_warm_activities += (int)$sales->warm_activities ?? 0;
                $summary_cold_activities += (int)$sales->cold_activities ?? 0;
                $summary_estimation += (int)$sales->total_estimation ?? 0;
                $summary_compare_estimation += (int)$sales->compare_total_estimation ?? 0;
                $summary_quotation += (int)$sales->total_quotation ?? 0;
                $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
                $summary_deals += (int)$sales->total_deals ?? 0;
                $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
                $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
                $summary_interior_design += (int)$sales->interior_design ?? 0;
                $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
            }

            $data = [
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
                    'value' => $summary_deals,
                    'compare' => $summary_compare_deals,
                    'total_transaction' => $summary_total_deals_transaction,
                    'target_deals' => $target_deals,
                ],
                'interior_design' => [
                    'value' => $summary_interior_design,
                    'total_transaction' => $summary_interior_design_total_transaction,
                ],
                'retail' => [
                    'value' => $summary_deals - $summary_interior_design,
                    'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                ],
            ];

            return response()->json($data);
        } else if (in_array($userType, ['sl', 'bum'])) {
            $query = User::selectRaw('id, name')
                ->whereIn('channel_id', $user->channels->pluck('id')->all())
                ->where('type', 2)
                ->withCount(['leads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
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
                ->withCount(['leads as total_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                        $q2->whereCreatedAtRange($startDate, $endDate);
                    });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_total_activities' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                        $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    });

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                        $q2->where('status', 1);
                        $q2->whereCreatedAtRange($startDate, $endDate);
                    });

                    if ($channelId) $q->where('channel_id', $channelId);

                    $q->where('status', 1);
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('status', 2);
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if ($channelId) $q->where('channel_id', $channelId);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->where('status', 3);
                    $q->whereCreatedAtRange($startDate, $endDate);

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
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    if ($channelId) $q->where('channel_id', $channelId);
                }], 'total_price')
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDateCompare, $endDateCompare);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        $q->whereNotNull('interior_design_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withCount([
                    'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        $q->whereNotNull('interior_design_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

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
            $summary_interior_design = 0;
            $summary_interior_design_total_transaction = 0;

            $data = [];
            foreach ($result as $sales) {
                $summary_new_leads += (int)$sales->total_leads ?? 0;
                $summary_compare_new_leads += (int)$sales->compare_total_leads ?? 0;
                $summary_active_leads += (int)$sales->active_leads ?? 0;
                $summary_compare_active_leads += (int)$sales->compare_active_leads ?? 0;
                $summary_total_activities += (int)$sales->total_activities ?? 0;
                $summary_compare_total_activities += (int)$sales->compare_total_activities ?? 0;
                $summary_hot_activities += (int)$sales->hot_activities ?? 0;
                $summary_warm_activities += (int)$sales->warm_activities ?? 0;
                $summary_cold_activities += (int)$sales->cold_activities ?? 0;
                $summary_estimation += (int)$sales->total_estimation ?? 0;
                $summary_compare_estimation += (int)$sales->compare_total_estimation ?? 0;
                $summary_quotation += (int)$sales->total_quotation ?? 0;
                $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
                $summary_deals += (int)$sales->total_deals ?? 0;
                $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
                $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
                $summary_interior_design += (int)$sales->interior_design ?? 0;
                $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
            }

            $data = [
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
                    'value' => $summary_deals,
                    'compare' => $summary_compare_deals,
                    'total_transaction' => $summary_total_deals_transaction,
                    'target_deals' => $target_deals,
                ],
                'interior_design' => [
                    'value' => $summary_interior_design,
                    'total_transaction' => $summary_interior_design_total_transaction,
                ],
                'retail' => [
                    'value' => $summary_deals - $summary_interior_design,
                    'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                ],
            ];

            return response()->json($data);
        }

        // else sales
        $query = User::where('id', $user->id)
            ->withCount(['leads as total_leads' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate);

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
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
            ->withCount(['leads as total_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q2->whereCreatedAtRange($startDate, $endDate);
                });

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as compare_total_activities' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                });

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as hot_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q2->where('status', 1);
                    $q2->whereCreatedAtRange($startDate, $endDate);
                });

                if ($channelId) $q->where('channel_id', $channelId);

                $q->where('status', 1);
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['userActivities as warm_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->where('status', 2);
                $q->whereCreatedAtRange($startDate, $endDate);

                if ($channelId) $q->where('channel_id', $channelId);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['userActivities as cold_activities' => function ($q) use ($channelId, $startDate, $endDate) {
                $q->where('status', 3);
                $q->whereCreatedAtRange($startDate, $endDate);

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
                $q->whereNotIn('status', [5, 6]);
                $q->whereCreatedAtRange($startDate, $endDate);
                if ($channelId) $q->where('channel_id', $channelId);
            }], 'total_price')
            ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                $q->whereNotIn('status', [5, 6]);
                $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                if ($channelId) $q->where('channel_id', $channelId);
            }], 'total_price')
            ->withCount([
                'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as total_deals' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as compare_total_deals' => function ($q) use ($channelId, $startDateCompare, $endDateCompare) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDateCompare, $endDateCompare);
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                    $q->whereNotNull('interior_design_id');
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withCount([
                'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                    $q->whereNotNull('interior_design_id');
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price');

        $result = $query->first();

        $data = [
            'new_leads' => [
                'value' => (int)$result->total_leads ?? 0,
                'compare' => (int)$result->compare_total_leads ?? 0,
            ],
            'active_leads' => [
                'value' => (int)$result->active_leads ?? 0,
                'compare' => (int)$result->compare_active_leads ?? 0,
            ],
            'follow_up' => [
                'total_activities' => [
                    'value' => (int)$result->total_activities ?? 0,
                    'compare' => (int)$result->compare_total_activities ?? 0,
                ],
                'hot_activities' => (int)$result->hot_activities ?? 0,
                'warm_activities' => (int)$result->warm_activities ?? 0,
                'cold_activities' => (int)$result->cold_activities ?? 0,
            ],
            'estimation' => [
                'value' => (int)$result->total_estimation ?? 0,
                'compare' => (int)$result->compare_total_estimation ?? 0,
            ],
            'quotation' => [
                'value' => (int)$result->total_quotation ?? 0,
                'compare' => (int)$result->compare_total_quotation ?? 0,
            ],
            'deals' => [
                'value' => (int)$result->total_deals ?? 0,
                'compare' => (int)$result->compare_total_deals ?? 0,
                'total_transaction' => $result->total_deals_transaction,
                'target_deals' => $target_deals,
            ],
            'interior_design' => [
                'value' => (int)$result->interior_design ?? 0,
                'total_transaction' => (int)$result->interior_design_total_transaction ?? 0,
            ],
            'retail' => [
                'value' => (int)($result->total_deals - $result->interior_design) ?? 0,
                'total_transaction' => (int)($result->total_deals_transaction - $result->interior_design_total_transaction) ?? 0,
            ],
        ];

        return response()->json($data);
    }

    public function details(Request $request)
    {
        $startDate = Carbon::now();
        $endDateCompare = Carbon::now()->subDay();

        $endDate = $startDate;
        $startDateCompare = $endDateCompare;


        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDateCompare = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay()->subDay();

            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();

            $diff = $startDate->diffInDays($endDate);
            $startDateCompare = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay()->subDays($diff + 1);
        }

        $user = user();
        $userType = null;

        $companyId = $request->company_id ?? $user->company_id;
        $channelId = $request->channel_id ?? null;

        if ($user->is_director || $user->is_digital_marketing) {
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
                $query = User::selectRaw('id, name')
                    ->where('type', UserType::SUPERVISOR)
                    ->where('supervisor_type_id', 2)
                    ->where('company_id', $companyId);
                if ($startDate->month == $endDate->month) {
                    $query = $query->selectRaw("(SELECT target FROM targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(created_at) >= '" . $startDate->startOfMonth() . "' AND DATE(created_at) <= '" . $startDate->endOfMonth() . "') as target_deals");
                }
                $query = $query->with(['channels' => function ($channel) use ($companyId, $channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $channel->where('company_id', $companyId)
                        ->with(['sales' => function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                            $q->withCount(['leads as total_leads' => function ($q) use ($startDate, $endDate) {
                                $q->whereCreatedAtRange($startDate, $endDate);

                                if (request()->product_brand_id) {
                                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                        $q2->where('product_brand_id', request()->product_brand_id);
                                    });
                                }
                            }])
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
                                ->withCount(['leads as total_activities' => function ($q) use ($startDate, $endDate) {
                                    $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                                        $q2->whereCreatedAtRange($startDate, $endDate);
                                    });

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['leads as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                                    $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                                        $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                                    });

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['leads as hot_activities' => function ($q) use ($startDate, $endDate) {
                                    $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                                        $q2->where('status', 1);
                                        $q2->whereCreatedAtRange($startDate, $endDate);
                                    });

                                    $q->where('status', 1);
                                    $q->whereCreatedAtRange($startDate, $endDate);

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['userActivities as warm_activities' => function ($q) use ($startDate, $endDate) {
                                    $q->where('status', 2);
                                    $q->whereCreatedAtRange($startDate, $endDate);

                                    if (request()->product_brand_id) {
                                        $q->whereHas('activityBrandValues', function ($q2) {
                                            $q2->where('product_brand_id', request()->product_brand_id);
                                        });
                                    }
                                }])
                                ->withCount(['userActivities as cold_activities' => function ($q) use ($startDate, $endDate) {
                                    $q->where('status', 3);
                                    $q->whereCreatedAtRange($startDate, $endDate);

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
                                    $q->whereNotIn('status', [5, 6]);
                                    $q->whereCreatedAtRange($startDate, $endDate);
                                }], 'total_price')
                                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                                    $q->whereNotIn('status', [5, 6]);
                                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                                }], 'total_price')
                                ->withCount([
                                    'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                        $q->whereNotIn('status', [5, 6]);
                                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                                        $q->whereDealAtRange($startDate, $endDate);
                                        if ($channelId) $q->where('channel_id', $channelId);
                                    }
                                ], 'total_price')
                                ->withSum([
                                    'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                                        $q->whereNotIn('status', [5, 6]);
                                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                                        $q->whereDealAtRange($startDate, $endDate);
                                    }
                                ], 'total_price')
                                ->withSum([
                                    'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                                        $q->whereNotIn('status', [5, 6]);
                                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                                        $q->whereDealAtRange($startDateCompare, $endDateCompare);
                                    }
                                ], 'total_price')
                                ->withSum([
                                    'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                                        $q->whereNotIn('status', [5, 6]);
                                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                                        $q->whereDealAtRange($startDate, $endDate);
                                        $q->whereNotNull('interior_design_id');
                                        if ($channelId) $q->where('channel_id', $channelId);
                                    }
                                ], 'total_price')
                                ->withCount([
                                    'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                        $q->whereNotIn('status', [5, 6]);
                                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                                        $q->whereDealAtRange($startDate, $endDate);
                                        $q->whereNotNull('interior_design_id');
                                        if ($channelId) $q->where('channel_id', $channelId);
                                    }
                                ], 'total_price');
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
                $summary_interior_design = 0;
                $summary_interior_design_total_transaction = 0;

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
                    $channel_interior_design = 0;
                    $channel_interior_design_total_transaction = 0;
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
                        $sales_interior_design = 0;
                        $sales_interior_design_total_transaction = 0;
                        foreach ($channel->sales as $sales) {
                            $sales_new_leads += $sales->total_leads ?? 0;
                            $sales_compare_new_leads += $sales->compare_total_leads ?? 0;
                            $sales_active_leads += $sales->active_leads ?? 0;
                            $sales_compare_active_leads += $sales->compare_active_leads ?? 0;
                            $sales_total_activities += $sales->total_activities ?? 0;
                            $sales_compare_total_activities += $sales->compare_total_activities ?? 0;
                            $sales_hot_activities += $sales->hot_activities ?? 0;
                            $sales_warm_activities += $sales->warm_activities ?? 0;
                            $sales_cold_activities += $sales->cold_activities ?? 0;
                            $sales_estimation += $sales->total_estimation ?? 0;
                            $sales_compare_estimation += $sales->compare_total_estimation ?? 0;
                            $sales_quotation += $sales->total_quotation ?? 0;
                            $sales_compare_quotation += $sales->compare_total_quotation ?? 0;
                            $sales_deals += $sales->total_deals ?? 0;
                            $sales_total_deals_transaction += $sales->total_deals_transaction ?? 0;
                            $sales_compare_deals += $sales->compare_total_deals ?? 0;
                            $sales_interior_design += $sales->interior_design ?? 0;
                            $sales_interior_design_total_transaction += $sales->interior_design_total_transaction ?? 0;
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
                        $channel_interior_design += $sales_interior_design ?? 0;
                        $channel_interior_design_total_transaction += $sales_interior_design_total_transaction ?? 0;
                    }

                    $data['details'][] = [
                        'id' => $bum->id,
                        'name' => $bum->name,
                        'new_leads' => [
                            'value' => (int)$channel_new_leads ?? 0,
                            'compare' => (int)$channel_compare_new_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => (int)$channel_active_leads ?? 0,
                            'compare' => (int)$channel_compare_active_leads ?? 0,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => (int)$channel_total_activities ?? 0,
                                'compare' => (int)$channel_compare_total_activities ?? 0,
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
                            'value' => (int)$channel_quotation ?? 0,
                            'compare' => (int)$channel_compare_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => (int)$channel_deals ?? 0,
                            'compare' => (int)$channel_compare_deals ?? 0,
                            'total_transaction' => $channel_total_deals_transaction,
                            'target_deals' => (int)$bum->target_deals ?? 0,
                        ],
                        'interior_design' => [
                            'value' => $channel_interior_design,
                            'total_transaction' => $channel_interior_design_total_transaction,
                        ],
                        'retail' => [
                            'value' => $channel_deals - $channel_interior_design,
                            'total_transaction' => $channel_total_deals_transaction - $channel_interior_design_total_transaction,
                        ],
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
                    $summary_interior_design += $channel_interior_design ?? 0;
                    $summary_interior_design_total_transaction += $channel_interior_design_total_transaction ?? 0;
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
                        'value' => $summary_deals,
                        'compare' => $summary_compare_deals,
                        'total_transaction' => $summary_total_deals_transaction,
                    ],
                    'interior_design' => [
                        'value' => $summary_interior_design,
                        'total_transaction' => $summary_interior_design_total_transaction,
                    ],
                    'retail' => [
                        'value' => $summary_deals - $summary_interior_design,
                        'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                    ],
                ];

                return response()->json($data);
            } elseif ($request->user_type == 'store') {
                $query = Channel::selectRaw('id, name')->where('company_id', $companyId);
                if ($startDate->month == $endDate->month) {
                    $query = $query->selectRaw("(SELECT target FROM targets WHERE model_type='channel' AND model_id=channels.id AND type=0 AND DATE(created_at) >= '" . $startDate->startOfMonth() . "' AND DATE(created_at) <= '" . $startDate->endOfMonth() . "') as target_deals");
                }
                $query = $query->with(['sales' => function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $q->withCount(['leads as total_leads' => function ($q) use ($startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
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
                        ->withCount(['leads as total_activities' => function ($q) use ($startDate, $endDate) {
                            $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                                $q2->whereCreatedAtRange($startDate, $endDate);
                            });

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                                $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                            });

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as hot_activities' => function ($q) use ($startDate, $endDate) {
                            $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                                $q2->where('status', 1);
                                $q2->whereCreatedAtRange($startDate, $endDate);
                            });

                            $q->where('status', 1);
                            $q->whereCreatedAtRange($startDate, $endDate);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['userActivities as warm_activities' => function ($q) use ($startDate, $endDate) {
                            $q->where('status', 2);
                            $q->whereCreatedAtRange($startDate, $endDate);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['userActivities as cold_activities' => function ($q) use ($startDate, $endDate) {
                            $q->where('status', 3);
                            $q->whereCreatedAtRange($startDate, $endDate);

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
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereCreatedAtRange($startDate, $endDate);
                        }], 'total_price')
                        ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        }], 'total_price')
                        ->withCount([
                            'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withSum([
                            'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                            }
                        ], 'total_price')
                        ->withSum([
                            'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDateCompare, $endDateCompare);
                            }
                        ], 'total_price')
                        ->withSum([
                            'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                                $q->whereNotNull('interior_design_id');
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withCount([
                            'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                                $q->whereNotNull('interior_design_id');
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price');
                }]);

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
                $summary_interior_design = 0;
                $summary_interior_design_total_transaction = 0;

                foreach ($result as $channel) {
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
                    $sales_interior_design = 0;
                    $saels_interior_design_total_transaction = 0;
                    foreach ($channel->sales as $sales) {
                        $sales_new_leads += $sales->total_leads ?? 0;
                        $sales_compare_new_leads += $sales->compare_total_leads ?? 0;
                        $sales_active_leads += $sales->active_leads ?? 0;
                        $sales_compare_active_leads += $sales->compare_active_leads ?? 0;
                        $sales_total_activities += $sales->total_activities ?? 0;
                        $sales_compare_total_activities += $sales->compare_total_activities ?? 0;
                        $sales_hot_activities += $sales->hot_activities ?? 0;
                        $sales_warm_activities += $sales->warm_activities ?? 0;
                        $sales_cold_activities += $sales->cold_activities ?? 0;
                        $sales_estimation += $sales->total_estimation ?? 0;
                        $sales_compare_estimation += $sales->compare_total_estimation ?? 0;
                        $sales_quotation += $sales->total_quotation ?? 0;
                        $sales_compare_quotation += $sales->compare_total_quotation ?? 0;
                        $sales_deals += $sales->total_deals ?? 0;
                        $sales_total_deals_transaction += $sales->total_deals_transaction ?? 0;
                        $sales_compare_deals += $sales->compare_total_deals ?? 0;
                        $sales_interior_design += $sales->interior_design ?? 0;
                        $saels_interior_design_total_transaction += $sales->interior_design_total_transaction ?? 0;
                    }

                    $data['details'][] = [
                        'id' => $channel->id,
                        'name' => $channel->name,
                        'new_leads' => [
                            'value' => (int)$sales_new_leads ?? 0,
                            'compare' => (int)$sales_compare_new_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => (int)$sales_active_leads ?? 0,
                            'compare' => (int)$sales_compare_active_leads ?? 0,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => (int)$sales_total_activities ?? 0,
                                'compare' => (int)$sales_compare_total_activities ?? 0,
                            ],
                            'hot_activities' => (int)$sales_hot_activities ?? 0,
                            'warm_activities' => (int)$sales_warm_activities ?? 0,
                            'cold_activities' => (int)$sales_cold_activities ?? 0,
                        ],
                        'estimation' => [
                            'value' => (int)$sales_estimation ?? 0,
                            'compare' => (int)$sales_compare_estimation ?? 0,
                        ],
                        'quotation' => [
                            'value' => (int)$sales_quotation ?? 0,
                            'compare' => (int)$sales_compare_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => (int)$sales_deals ?? 0,
                            'compare' => (int)$sales_compare_deals ?? 0,
                            'total_transaction' => $sales_total_deals_transaction,
                            'target_deals' => $channel->target_deals ?? 0,
                        ],
                        'interior_design' => [
                            'value' => $sales_interior_design,
                            'total_transaction' => $saels_interior_design_total_transaction,
                        ],
                        'retail' => [
                            'value' => $sales_deals - $sales_interior_design,
                            'total_transaction' => $sales_total_deals_transaction - $saels_interior_design_total_transaction,
                        ],
                    ];

                    $summary_new_leads += $sales_new_leads ?? 0;
                    $summary_compare_new_leads += $sales_compare_new_leads ?? 0;
                    $summary_active_leads += $sales_active_leads ?? 0;
                    $summary_compare_active_leads += $sales_compare_active_leads ?? 0;
                    $summary_total_activities += $sales_total_activities ?? 0;
                    $summary_compare_total_activities += $sales_compare_total_activities ?? 0;
                    $summary_hot_activities += $sales_hot_activities ?? 0;
                    $summary_warm_activities += $sales_warm_activities ?? 0;
                    $summary_cold_activities += $sales_cold_activities ?? 0;
                    $summary_estimation += $sales_estimation ?? 0;
                    $summary_compare_estimation += $sales_compare_estimation ?? 0;
                    $summary_quotation += $sales_quotation ?? 0;
                    $summary_compare_quotation += $sales_compare_quotation ?? 0;
                    $summary_deals += $sales_deals ?? 0;
                    $summary_total_deals_transaction += $sales_total_deals_transaction ?? 0;
                    $summary_compare_deals += $sales_compare_deals ?? 0;
                    $summary_interior_design += $sales_interior_design ?? 0;
                    $summary_interior_design_total_transaction += $saels_interior_design_total_transaction ?? 0;
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
                        'value' => $summary_deals,
                        'compare' => $summary_compare_deals,
                        'total_transaction' => $summary_total_deals_transaction,
                    ],
                    'interior_design' => [
                        'value' => $summary_interior_design,
                        'total_transaction' => $summary_interior_design_total_transaction,
                    ],
                    'retail' => [
                        'value' => $summary_deals - $summary_interior_design,
                        'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                    ],
                ];

                return response()->json($data);
            }

            $query = User::selectRaw('id, name');
            if ($startDate->month == $endDate->month) {
                $query = $query->selectRaw("(SELECT target FROM targets WHERE model_type='user' AND model_id=users.id AND type=0 AND DATE(created_at) >= '" . $startDate->startOfMonth() . "' AND DATE(created_at) <= '" . $startDate->endOfMonth() . "') as target_deals");
            }
            $query = $query->where('type', 2)
                ->where('company_id', $companyId)
                ->withCount(['leads as total_leads' => function ($q) use ($startDate, $endDate) {
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
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
                ->withCount(['leads as total_activities' => function ($q) use ($startDate, $endDate) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                        $q2->whereCreatedAtRange($startDate, $endDate);
                    });

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                        $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    });

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['leads as hot_activities' => function ($q) use ($startDate, $endDate) {
                    $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                        $q2->where('status', 1);
                        $q2->whereCreatedAtRange($startDate, $endDate);
                    });

                    $q->where('status', 1);
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as warm_activities' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 2);
                    $q->whereCreatedAtRange($startDate, $endDate);

                    if (request()->product_brand_id) {
                        $q->whereHas('activityBrandValues', function ($q2) {
                            $q2->where('product_brand_id', request()->product_brand_id);
                        });
                    }
                }])
                ->withCount(['userActivities as cold_activities' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 3);
                    $q->whereCreatedAtRange($startDate, $endDate);

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
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDate, $endDate);
                }], 'total_price')
                ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                }], 'total_price')
                ->withCount([
                    'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDateCompare, $endDateCompare);
                    }
                ], 'total_price')
                ->withSum([
                    'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        $q->whereNotNull('interior_design_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price')
                ->withCount([
                    'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereIn('payment_status', [2, 3, 4, 6]);
                        $q->whereDealAtRange($startDate, $endDate);
                        $q->whereNotNull('interior_design_id');
                        if ($channelId) $q->where('channel_id', $channelId);
                    }
                ], 'total_price');

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
            $summary_interior_design = 0;
            $summary_interior_design_total_transaction = 0;

            $data = [];
            foreach ($result as $r) {
                $data['details'][] = [
                    'id' => $r->id,
                    'name' => $r->name,
                    'new_leads' => [
                        'value' => (int)$r->total_leads ?? 0,
                        'compare' => (int)$r->compare_total_leads ?? 0,
                    ],
                    'active_leads' => [
                        'value' => (int)$r->active_leads ?? 0,
                        'compare' => (int)$r->compare_active_leads ?? 0,
                    ],
                    'follow_up' => [
                        'total_activities' => [
                            'value' => (int)$r->total_activities ?? 0,
                            'compare' => (int)$r->compare_total_activities ?? 0,
                        ],
                        'hot_activities' => (int)$r->hot_activities ?? 0,
                        'warm_activities' => (int)$r->warm_activities ?? 0,
                        'cold_activities' => (int)$r->cold_activities ?? 0,
                    ],
                    'estimation' => [
                        'value' => (int)$r->total_estimation ?? 0,
                        'compare' => (int)$r->compare_total_estimation ?? 0,
                    ],
                    'quotation' => [
                        'value' => (int)$r->total_quotation ?? 0,
                        'compare' => (int)$r->compare_total_quotation ?? 0,
                    ],
                    'deals' => [
                        'value' => (int)$r->total_deals ?? 0,
                        'compare' => (int)$r->compare_total_deals ?? 0,
                        'total_transaction' => (int)$r->total_deals_transaction,
                        'target_deals' => (int)$r->target_deals ?? 0,
                    ],
                    'interior_design' => [
                        'value' => (int)$r->interior_design ?? 0,
                        'total_transaction' => (int)$r->interior_design_total_transaction ?? 0,
                    ],
                    'retail' => [
                        'value' => (int)($r->total_deals - $r->interior_design) ?? 0,
                        'total_transaction' => (int)($r->total_deals_transaction - $r->interior_design_total_transaction) ?? 0,
                    ],
                ];

                $summary_new_leads += (int)$r->total_leads ?? 0;
                $summary_compare_new_leads += (int)$r->compare_total_leads ?? 0;
                $summary_active_leads += (int)$r->active_leads ?? 0;
                $summary_compare_active_leads += (int)$r->compare_active_leads ?? 0;
                $summary_total_activities += (int)$r->total_activities ?? 0;
                $summary_compare_total_activities += (int)$r->compare_total_activities ?? 0;
                $summary_hot_activities += (int)$r->hot_activities ?? 0;
                $summary_warm_activities += (int)$r->warm_activities ?? 0;
                $summary_cold_activities += (int)$r->cold_activities ?? 0;
                $summary_estimation += (int)$r->total_estimation ?? 0;
                $summary_compare_estimation += (int)$r->compare_total_estimation ?? 0;
                $summary_quotation += (int)$r->total_quotation ?? 0;
                $summary_compare_quotation += (int)$r->compare_total_quotation ?? 0;
                $summary_deals += (int)$r->total_deals ?? 0;
                $summary_total_deals_transaction += (int)$r->total_deals_transaction ?? 0;
                $summary_compare_deals += (int)$r->compare_total_deals ?? 0;
                $summary_interior_design += (int)$r->interior_design ?? 0;
                $summary_interior_design_total_transaction += (int)$r->interior_design_total_transaction ?? 0;
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
                    'value' => $summary_deals,
                    'compare' => $summary_compare_deals,
                    'total_transaction' => $summary_total_deals_transaction,
                ],
                'interior_design' => [
                    'value' => $summary_interior_design,
                    'total_transaction' => $summary_interior_design_total_transaction,
                ],
                'retail' => [
                    'value' => $summary_deals - $summary_interior_design,
                    'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                ],
            ];

            return response()->json($data);
        } else if (in_array($userType, ['sl', 'bum'])) {
            if ($request->user_type == 'store') {
                $query = Channel::selectRaw('id, name')->whereIn('id', $user->channels->pluck('id')->all());
                if ($startDate->month == $endDate->month) {
                    $query = $query->selectRaw("(SELECT target FROM targets WHERE model_type='channel' AND model_id=channels.id AND type=0 AND DATE(created_at) >= '" . $startDate->startOfMonth() . "' AND DATE(created_at) <= '" . $startDate->endOfMonth() . "') as target_deals");
                }
                $query = $query->with(['sales' => function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                    $q->withCount(['leads as total_leads' => function ($q) use ($startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
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
                        ->withCount(['leads as total_activities' => function ($q) use ($startDate, $endDate) {
                            $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                                $q2->whereCreatedAtRange($startDate, $endDate);
                            });

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                                $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                            });

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['leads as hot_activities' => function ($q) use ($startDate, $endDate) {
                            $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                                $q2->where('status', 1);
                                $q2->whereCreatedAtRange($startDate, $endDate);
                            });

                            $q->where('status', 1);
                            $q->whereCreatedAtRange($startDate, $endDate);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['userActivities as warm_activities' => function ($q) use ($startDate, $endDate) {
                            $q->where('status', 2);
                            $q->whereCreatedAtRange($startDate, $endDate);

                            if (request()->product_brand_id) {
                                $q->whereHas('activityBrandValues', function ($q2) {
                                    $q2->where('product_brand_id', request()->product_brand_id);
                                });
                            }
                        }])
                        ->withCount(['userActivities as cold_activities' => function ($q) use ($startDate, $endDate) {
                            $q->where('status', 3);
                            $q->whereCreatedAtRange($startDate, $endDate);

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
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereCreatedAtRange($startDate, $endDate);
                        }], 'total_price')
                        ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        }], 'total_price')
                        ->withCount([
                            'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withSum([
                            'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                            }
                        ], 'total_price')
                        ->withSum([
                            'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDateCompare, $endDateCompare);
                            }
                        ], 'total_price')
                        ->withSum([
                            'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                                $q->whereNotNull('interior_design_id');
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price')
                        ->withCount([
                            'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                                $q->whereNotIn('status', [5, 6]);
                                $q->whereIn('payment_status', [2, 3, 4, 6]);
                                $q->whereDealAtRange($startDate, $endDate);
                                $q->whereNotNull('interior_design_id');
                                if ($channelId) $q->where('channel_id', $channelId);
                            }
                        ], 'total_price');
                }]);

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
                $summary_interior_design = 0;
                $summary_total_deals_transaction = 0;
                $summary_interior_design_total_transaction = 0;

                foreach ($result as $channel) {

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
                    $sales_interior_design = 0;
                    $sales_interior_design_total_transaction = 0;
                    foreach ($channel->sales as $sales) {
                        $sales_new_leads += $sales->total_leads ?? 0;
                        $sales_compare_new_leads += $sales->compare_total_leads ?? 0;
                        $sales_active_leads += $sales->active_leads ?? 0;
                        $sales_compare_active_leads += $sales->compare_active_leads ?? 0;
                        $sales_total_activities += $sales->total_activities ?? 0;
                        $sales_compare_total_activities += $sales->compare_total_activities ?? 0;
                        $sales_hot_activities += $sales->hot_activities ?? 0;
                        $sales_warm_activities += $sales->warm_activities ?? 0;
                        $sales_cold_activities += $sales->cold_activities ?? 0;
                        $sales_estimation += $sales->total_estimation ?? 0;
                        $sales_compare_estimation += $sales->compare_total_estimation ?? 0;
                        $sales_quotation += $sales->total_quotation ?? 0;
                        $sales_compare_quotation += $sales->compare_total_quotation ?? 0;
                        $sales_deals += $sales->total_deals ?? 0;
                        $sales_total_deals_transaction += $sales->total_deals_transaction ?? 0;
                        $sales_compare_deals += $sales->compare_total_deals ?? 0;
                        $sales_interior_design += $sales->interior_design ?? 0;
                        $sales_interior_design_total_transaction += $sales->interior_design_total_transaction ?? 0;
                    }

                    $data['details'][] = [
                        'id' => $channel->id,
                        'name' => $channel->name,
                        'new_leads' => [
                            'value' => (int)$sales_new_leads ?? 0,
                            'compare' => (int)$sales_compare_new_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => (int)$sales_active_leads ?? 0,
                            'compare' => (int)$sales_compare_active_leads ?? 0,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => (int)$sales_total_activities ?? 0,
                                'compare' => (int)$sales_compare_total_activities ?? 0,
                            ],
                            'hot_activities' => (int)$sales_hot_activities ?? 0,
                            'warm_activities' => (int)$sales_warm_activities ?? 0,
                            'cold_activities' => (int)$sales_cold_activities ?? 0,
                        ],
                        'estimation' => [
                            'value' => (int)$sales_estimation ?? 0,
                            'compare' => (int)$sales_compare_estimation ?? 0,
                        ],
                        'quotation' => [
                            'value' => (int)$sales_quotation ?? 0,
                            'compare' => (int)$sales_compare_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => (int)$sales_deals ?? 0,
                            'compare' => (int)$sales_compare_deals ?? 0,
                            'total_transaction' => $sales_total_deals_transaction,
                            'target_deals' => $channel->target_deals,
                        ],
                        'interior_design' => [
                            'value' => (int)$sales_interior_design ?? 0,
                            'total_transaction' => (int)$sales_interior_design_total_transaction ?? 0,
                        ],
                        'retail' => [
                            'value' => (int)($sales_deals - $sales_interior_design) ?? 0,
                            'total_transaction' => (int)($sales_total_deals_transaction - $sales_interior_design_total_transaction) ?? 0,
                        ],
                    ];

                    $summary_new_leads += $sales_new_leads ?? 0;
                    $summary_compare_new_leads += $sales_compare_new_leads ?? 0;
                    $summary_active_leads += $sales_active_leads ?? 0;
                    $summary_compare_active_leads += $sales_compare_active_leads ?? 0;
                    $summary_total_activities += $sales_total_activities ?? 0;
                    $summary_compare_total_activities += $sales_compare_total_activities ?? 0;
                    $summary_hot_activities += $sales_hot_activities ?? 0;
                    $summary_warm_activities += $sales_warm_activities ?? 0;
                    $summary_cold_activities += $sales_cold_activities ?? 0;
                    $summary_estimation += $sales_estimation ?? 0;
                    $summary_compare_estimation += $sales_compare_estimation ?? 0;
                    $summary_quotation += $sales_quotation ?? 0;
                    $summary_compare_quotation += $sales_compare_quotation ?? 0;
                    $summary_deals += $sales_deals ?? 0;
                    $summary_total_deals_transaction += $sales_total_deals_transaction ?? 0;
                    $summary_compare_deals += $sales_compare_deals ?? 0;
                    $summary_interior_design += $sales_interior_design ?? 0;
                    $summary_interior_design_total_transaction += $sales_interior_design_total_transaction ?? 0;
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
                        'value' => $summary_deals,
                        'compare' => $summary_compare_deals,
                        'total_transaction' => $summary_total_deals_transaction,
                    ],
                    'interior_design' => [
                        'value' => $summary_interior_design,
                        'total_transaction' => $summary_interior_design_total_transaction,
                    ],
                    'retail' => [
                        'value' => $summary_deals - $summary_interior_design,
                        'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                    ],
                ];

                return response()->json($data);
            }

            $query = Channel::selectRaw('id,name');
            if ($startDate->month == $endDate->month) {
                $query = $query->selectRaw("(SELECT target FROM targets WHERE model_type='channel' AND model_id=channels.id AND type=0 AND DATE(created_at) >= '" . $startDate->startOfMonth() . "' AND DATE(created_at) <= '" . $startDate->endOfMonth() . "') as target_deals");
            }
            $query = $query->with(['sales' => function ($q) use ($channelId, $startDate, $endDate, $startDateCompare, $endDateCompare) {
                $q
                    ->withCount(['leads as total_leads' => function ($q) use ($startDate, $endDate) {
                        $q->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
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
                    ->withCount(['leads as total_activities' => function ($q) use ($startDate, $endDate) {
                        $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                            $q2->whereCreatedAtRange($startDate, $endDate);
                        });

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['leads as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                            $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                        });

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['leads as hot_activities' => function ($q) use ($startDate, $endDate) {
                        $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                            $q2->where('status', 1);
                            $q2->whereCreatedAtRange($startDate, $endDate);
                        });

                        $q->where('status', 1);
                        $q->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['userActivities as warm_activities' => function ($q) use ($startDate, $endDate) {
                        $q->where('status', 2);
                        $q->whereCreatedAtRange($startDate, $endDate);

                        if (request()->product_brand_id) {
                            $q->whereHas('activityBrandValues', function ($q2) {
                                $q2->where('product_brand_id', request()->product_brand_id);
                            });
                        }
                    }])
                    ->withCount(['userActivities as cold_activities' => function ($q) use ($startDate, $endDate) {
                        $q->where('status', 3);
                        $q->whereCreatedAtRange($startDate, $endDate);

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
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereCreatedAtRange($startDate, $endDate);
                    }], 'total_price')
                    ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                        $q->whereNotIn('status', [5, 6]);
                        $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
                    }], 'total_price')
                    ->withCount([
                        'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereIn('payment_status', [2, 3, 4, 6]);
                            $q->whereDealAtRange($startDate, $endDate);
                            if ($channelId) $q->where('channel_id', $channelId);
                        }
                    ], 'total_price')
                    ->withSum([
                        'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereIn('payment_status', [2, 3, 4, 6]);
                            $q->whereDealAtRange($startDate, $endDate);
                        }
                    ], 'total_price')
                    ->withSum([
                        'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereIn('payment_status', [2, 3, 4, 6]);
                            $q->whereDealAtRange($startDateCompare, $endDateCompare);
                        }
                    ], 'total_price')
                    ->withSum([
                        'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereIn('payment_status', [2, 3, 4, 6]);
                            $q->whereDealAtRange($startDate, $endDate);
                            $q->whereNotNull('interior_design_id');
                            if ($channelId) $q->where('channel_id', $channelId);
                        }
                    ], 'total_price')
                    ->withCount([
                        'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                            $q->whereNotIn('status', [5, 6]);
                            $q->whereIn('payment_status', [2, 3, 4, 6]);
                            $q->whereDealAtRange($startDate, $endDate);
                            $q->whereNotNull('interior_design_id');
                            if ($channelId) $q->where('channel_id', $channelId);
                        }
                    ], 'total_price');
            }])->whereIn('id', $user->channels->pluck('id')->all());

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
            $summary_interior_design = 0;
            $summary_interior_design_total_transaction = 0;
            foreach ($result as $channel) {
                foreach ($channel->sales as $sales) {
                    $data['details'][] = [
                        'id' => $sales->id,
                        'name' => $sales->name,
                        'new_leads' => [
                            'value' => (int)$sales->total_leads ?? 0,
                            'compare' => (int)$sales->compare_total_leads ?? 0,
                        ],
                        'active_leads' => [
                            'value' => (int)$sales->active_leads ?? 0,
                            'compare' => (int)$sales->compare_active_leads ?? 0,
                        ],
                        'follow_up' => [
                            'total_activities' => [
                                'value' => (int)$sales->total_activities ?? 0,
                                'compare' => (int)$sales->compare_total_activities ?? 0,
                            ],
                            'hot_activities' => (int)$sales->hot_activities ?? 0,
                            'warm_activities' => (int)$sales->warm_activities ?? 0,
                            'cold_activities' => (int)$sales->cold_activities ?? 0,
                        ],
                        'estimation' => [
                            'value' => (int)$sales->total_estimation ?? 0,
                            'compare' => (int)$sales->compare_total_estimation ?? 0,
                        ],
                        'quotation' => [
                            'value' => (int)$sales->total_quotation ?? 0,
                            'compare' => (int)$sales->compare_total_quotation ?? 0,
                        ],
                        'deals' => [
                            'value' => (int)$sales->total_deals ?? 0,
                            'total_transaction' => $sales->total_deals_transaction,
                        ],
                        'interior_design' => [
                            'value' => (int)$sales->interior_design ?? 0,
                            'total_transaction' => (int)$sales->interior_design_total_transaction ?? 0,
                        ],
                        'retail' => [
                            'value' => (int)($sales->total_deals - $sales->interior_design) ?? 0,
                            'total_transaction' => (int)($sales->total_deals_transaction - $sales->interior_design_total_transaction) ?? 0,
                        ],
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
                    $summary_estimation += (int)$sales->total_estimation ?? 0;
                    $summary_compare_estimation += (int)$sales->compare_total_estimation ?? 0;
                    $summary_quotation += (int)$sales->total_quotation ?? 0;
                    $summary_compare_quotation += (int)$sales->compare_total_quotation ?? 0;
                    $summary_deals += (int)$sales->total_deals ?? 0;
                    $summary_total_deals_transaction += (int)$sales->total_deals_transaction ?? 0;
                    $summary_compare_deals += (int)$sales->compare_total_deals ?? 0;
                    $summary_interior_design += (int)$sales->interior_design ?? 0;
                    $summary_interior_design_total_transaction += (int)$sales->interior_design_total_transaction ?? 0;
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
                    'value' => $summary_deals,
                    'compare' => $summary_compare_deals,
                    'total_transaction' => $summary_total_deals_transaction,
                ],
                'interior_design' => [
                    'value' => $summary_interior_design,
                    'total_transaction' => $summary_interior_design_total_transaction,
                ],
                'retail' => [
                    'value' => $summary_deals - $summary_interior_design,
                    'total_transaction' => $summary_total_deals_transaction - $summary_interior_design_total_transaction,
                ],
            ];

            return response()->json($data);
        }

        // else sales
        $query = User::selectRaw('id, name')
            ->withCount(['leads as total_leads' => function ($q) use ($startDate, $endDate) {
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('leadActivities.activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
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
            ->withCount(['leads as total_activities' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q2->whereCreatedAtRange($startDate, $endDate);
                });

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as compare_total_activities' => function ($q) use ($startDateCompare, $endDateCompare) {
                $q->whereHas('leadActivities', function ($q2) use ($startDateCompare, $endDateCompare) {
                    $q2->whereCreatedAtRange($startDateCompare, $endDateCompare);
                });

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['leads as hot_activities' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('leadActivities', function ($q2) use ($startDate, $endDate) {
                    $q2->where('status', 1);
                    $q2->whereCreatedAtRange($startDate, $endDate);
                });

                $q->where('status', 1);
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['userActivities as warm_activities' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 2);
                $q->whereCreatedAtRange($startDate, $endDate);

                if (request()->product_brand_id) {
                    $q->whereHas('activityBrandValues', function ($q2) {
                        $q2->where('product_brand_id', request()->product_brand_id);
                    });
                }
            }])
            ->withCount(['userActivities as cold_activities' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 3);
                $q->whereCreatedAtRange($startDate, $endDate);

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
                $q->whereNotIn('status', [5, 6]);
                $q->whereCreatedAtRange($startDate, $endDate);
            }], 'total_price')
            ->withSum(['userOrders as compare_total_quotation' => function ($q) use ($startDateCompare, $endDateCompare) {
                $q->whereNotIn('status', [5, 6]);
                $q->whereCreatedAtRange($startDateCompare, $endDateCompare);
            }], 'total_price')
            ->withCount([
                'userOrders as total_deals_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as total_deals' => function ($q) use ($startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as compare_total_deals' => function ($q) use ($startDateCompare, $endDateCompare) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDateCompare, $endDateCompare);
                }
            ], 'total_price')
            ->withSum([
                'userOrders as interior_design' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                    $q->whereNotNull('interior_design_id');
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price')
            ->withCount([
                'userOrders as interior_design_total_transaction' => function ($q) use ($channelId, $startDate, $endDate) {
                    $q->whereNotIn('status', [5, 6]);
                    $q->whereIn('payment_status', [2, 3, 4, 6]);
                    $q->whereDealAtRange($startDate, $endDate);
                    $q->whereNotNull('interior_design_id');
                    if ($channelId) $q->where('channel_id', $channelId);
                }
            ], 'total_price');

        $result = $query->where('id', $user->id)->first();

        $data['details'][] = [
            'id' => $result->id,
            'name' => $result->name,
            'new_leads' => [
                'value' => (int)$result->total_leads ?? 0,
                'compare' => (int)$result->compare_total_leads ?? 0,
            ],
            'active_leads' => [
                'value' => (int)$result->active_leads ?? 0,
                'compare' => (int)$result->compare_active_leads ?? 0,
            ],
            'follow_up' => [
                'total_activities' => [
                    'value' => (int)$result->total_activities ?? 0,
                    'compare' => (int)$result->compare_total_activities ?? 0,
                ],
                'hot_activities' => (int)$result->hot_activities ?? 0,
                'warm_activities' => (int)$result->warm_activities ?? 0,
                'cold_activities' => (int)$result->cold_activities ?? 0,
            ],
            'estimation' => [
                'value' => (int)$result->total_estimation ?? 0,
                'compare' => (int)$result->compare_total_estimation ?? 0,
            ],
            'quotation' => [
                'value' => (int)$result->total_quotation ?? 0,
                'compare' => (int)$result->compare_total_quotation ?? 0,
            ],
            'deals' => [
                'value' => (int)$result->total_deals ?? 0,
                'compare' => (int)$result->compare_total_deals ?? 0,
                'total_transaction' => (int)$result->total_deals_transaction ?? 0,
            ],
            'interior_design' => [
                'value' => (int)$result->interior_design ?? 0,
                'total_transaction' => (int)$result->interior_design_total_transaction ?? 0,
            ],
            'retail' => [
                'value' => (int)($result->total_deals - $result->interior_design) ?? 0,
                'total_transaction' => (int)($result->total_deals_transaction - $result->interior_design_total_transaction) ?? 0,
            ],
        ];

        $data['summary'] = $data['details'][0];

        return response()->json($data);
    }

    public function invoice(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = $startDate;
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $user = user();

        $query = Order::selectRaw("
        orders.id,
        orders.invoice_number,
        orders.total_price,
        orders.created_at,
        IF(customers.last_name IS NOT NULL, CONCAT(customers.first_name, ' ', customers.last_name),
        customers.first_name) as customer
        ")
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->whereNotIn('status', [5, 6]);


        if ($request->inovice_type == 'deals') {
            $query = $query->whereIn('orders.payment_status', [2, 3, 4, 6])
                ->whereDealAtRange($startDate, $endDate);
        } elseif ($request->inovice_type == 'settlement') {
            $query = $query->whereIn('orders.payment_status', [3, 4])
                ->whereDealAtRange($startDate, $endDate);
        } else {
            // quotation
            $query = $query->whereCreatedAtRange($startDate, $endDate);
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
            $query = $query->whereIn('orders.payment_status', [2, 3, 4, 6])
                ->whereDealAtRange($startDate, $endDate);
        } else {
            // quotation
            $query = $query->whereIn('orders.payment_status', [1, 2, 3, 4, 6]);
        }

        // $userType = null;
        // if ($user->is_director || $user->is_digital_marketing) {
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
                'sales' => User::findOrFail($id),
                default => $user,
            };
        }

        if ($user instanceof Channel) {
            $query = $query->where('channel_id', $user->id);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            $companyIds = $user->company_ids ?? $user->companies->pluck('id')->all();
            $query = $query->whereIn('company_id', $companyIds);
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $query = $query->whereIn('channel_id', $user->channels->pluck('id')->all());
        } else {
            // sales
            $query = $query->where('user_id', $user->id);
        }

        if ($request->company_id) $query = $query->where('company_id', $request->company_id);
        if ($request->channel_id) $query = $query->where('channel_id', $request->channel_id);
        if ($request->user_id) $query = $query = $query->where('user_id', $request->user_id);

        if ($request->product_brand_id) {
            $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
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

            if (!is_null($paymentStatus)) $query = $query->where('payment_status', $paymentStatus);
        }

        $result = $query->orderBy('orders.id', 'desc')->get();

        return response()->json($result);
    }

    public function leads(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = $startDate;
        if ((request()->has('start_date') && request()->start_date != '') && (request()->has('end_date') && request()->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', request()->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', request()->end_date)->endOfDay();
        }

        $user = user();

        $activityStatus = match ($request->status) {
            'HOT' => ActivityStatus::HOT,
            'WARM' => ActivityStatus::WARM,
            'COLD' => ActivityStatus::COLD,
            default => null,
        };

        $query = Lead::query()
            ->with(['latestActivity' => function ($q) use ($activityStatus) {
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
            customers.email
            ")
            ->join('customers', 'customers.id', '=', 'leads.customer_id')
            ->whereCreatedAtRange($startDate, $endDate);

        // $userType = null;
        // if ($user->is_director || $user->is_digital_marketing) {
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
        //     $query = $query->where('leads.user_id', $user->id);
        // }

        $userType = $request->user_type ?? null;
        $id = $request->id ?? null;

        if ($userType && $id) {
            $user = match ($userType) {
                'bum' => User::findOrFail($id),
                'store' => Channel::findOrFail($id),
                'sales' => User::findOrFail($id),
                default => $user,
            };
        }

        if ($user instanceof Channel) {
            $query = $query->where('channel_id', $user->id);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            $companyIds = $user->company_ids ?? $user->companies->pluck('id')->all();
            $query = $query->whereIn('company_id', $companyIds);
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $query = $query->whereIn('channel_id', $user->channels->pluck('id')->all());
        } else {
            // sales
            $query = $query->where('user_id', $user->id);
        }

        if ($request->company_id) $query = $query->where('company_id', $request->company_id);
        if ($request->channel_id) $query = $query->where('channel_id', $request->channel_id);
        if ($name = $request->name) $query = $query->whereHas('customer', fn ($q) => $q->where('first_name', 'LIKE', "%$name%")->orWhere('last_name', 'LIKE', "%$name%"));

        if ($request->product_brand_id) {
            $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
        }

        if (!is_null($activityStatus)) {
            $query = $query->whereHas('latestActivity', fn ($q) => $q->where('status', $activityStatus));
        }

        $result = $query->orderBy('leads.id', 'desc')->get();

        return response()->json($result);
    }

    public function interiorDesigns(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = $startDate;
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
            ->whereNotIn('status', [5, 6])
            ->whereIn('payment_status', [2, 3, 4, 6])
            ->whereDealAtRange($startDate, $endDate)
            ->whereNotNull('interior_design_id');

        $userType = $request->user_type ?? null;
        $id = $request->id ?? null;

        if ($userType && $id) {
            $user = match ($userType) {
                'bum' => User::findOrFail($id),
                'store' => Channel::findOrFail($id),
                'sales' => User::findOrFail($id),
                default => $user,
            };
        }

        if ($user instanceof Channel) {
            $query = $query->where('channel_id', $user->id);
        } elseif ($user->type->is(UserType::DIRECTOR)) {
            $companyIds = $user->company_ids ?? $user->companies->pluck('id')->all();
            $query = $query->whereIn('company_id', $companyIds);
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $query = $query->whereIn('channel_id', $user->channels->pluck('id')->all());
        } else {
            // sales
            $query = $query->where('user_id', $user->id);
        }

        if ($request->company_id) $query = $query->where('company_id', $request->company_id);
        if ($request->channel_id) $query = $query->where('channel_id', $request->channel_id);
        if ($request->user_id) $query = $query = $query->where('user_id', $request->user_id);

        if ($request->product_brand_id) {
            $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
        }

        $result = $query->groupBy('interior_design_id')->orderBy('orders.id', 'desc')->get();

        return response()->json($result);
    }

    public function interiorDesignDetails(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = $startDate;
        if ((request()->has('start_date') && request()->start_date != '') && (request()->has('end_date') && request()->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', request()->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', request()->end_date)->endOfDay();
        }

        // $user = user();

        $query = Order::selectRaw("
        orders.id,
        orders.invoice_number,
        orders.total_price,
        users.name as sales,
        channels.name as channel,
        orders.created_at
        ")
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('channels', 'channels.id', '=', 'orders.channel_id')
            ->where('interior_design_id', $request->interior_design_id)
            ->whereNotIn('status', [5, 6])
            ->whereIn('payment_status', [2, 3, 4, 6])
            ->whereDealAtRange($startDate, $endDate)
            ->whereNotNull('interior_design_id');

        if ($request->company_id) $query = $query->where('company_id', $request->company_id);
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
        $startDate = Carbon::now();
        $endDate = $startDate;
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $user = user();
        $channelId = $request->channel_id ?? null;

        $userType = $request->user_type ?? null;
        $id = $request->id ?? null;

        if ($userType && $id) {
            $user = match ($userType) {
                'bum' => User::findOrFail($id),
                'store' => Channel::findOrFail($id),
                'sales' => User::findOrFail($id),
                default => $user,
            };
        }

        if ($user instanceof Channel) {
            $extraQuery = "
            (
                SELECT
                    SUM(estimated_value)
                FROM activity_brand_values
                JOIN
                    JOIN leads ON leads.id=activity_brand_values.lead_id
                WHERE
                    activity_brand_values.id = abv.id
                    AND leads.channel_id = " . $id . "
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as estimation,
            (
                SELECT
                    SUM(order_value)
                FROM activity_brand_values
                JOIN
                    JOIN leads ON leads.id=activity_brand_values.lead_id
                WHERE
                    activity_brand_values.id = abv.id
                    AND leads.channel_id = " . $id . "
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as order_value,
            (
                SELECT
                    SUM(order_value)
                FROM activity_brand_values
                JOIN
                    JOIN leads ON leads.id=activity_brand_values.lead_id
                WHERE
                    activity_brand_values.id = abv.id
                    AND leads.channel_id = " . $id . "
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as quotation
            ";
        } elseif ($user->type->is(UserType::SUPERVISOR)) {
            $extraQuery = "
            (
                SELECT
                    SUM(estimated_value)
                FROM activity_brand_values
                JOIN
                    JOIN leads ON leads.id=activity_brand_values.lead_id
                WHERE
                    activity_brand_values.id = abv.id
                    AND leads.channel_id IN (" . $user->channels->pluck('id')->implode(',') . ")
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as estimation,
            (
                SELECT
                    SUM(order_value)
                FROM activity_brand_values
                JOIN
                    JOIN leads ON leads.id=activity_brand_values.lead_id
                WHERE
                    activity_brand_values.id = abv.id
                    AND leads.channel_id IN (" . $user->channels->pluck('id')->implode(',') . ")
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as order_value,
            (
                SELECT
                    SUM(order_value)
                FROM activity_brand_values
                JOIN
                    JOIN leads ON leads.id=activity_brand_values.lead_id
                WHERE
                    activity_brand_values.id = abv.id
                    AND leads.channel_id IN (" . $user->channels->pluck('id')->implode(',') . ")
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as quotation
            ";
        } else {
            $extraQuery = "
            (
                SELECT
                    SUM(estimated_value)
                FROM activity_brand_values
                WHERE
                    activity_brand_values.id = abv.id
                    AND activity_brand_values.user_id = " . $id . "
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as estimation,
            (
                SELECT
                    SUM(order_value)
                FROM activity_brand_values
                WHERE
                    activity_brand_values.id = abv.id
                    AND activity_brand_values.user_id = " . $id . "
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as order_value,
            (
                SELECT
                    SUM(order_value)
                FROM activity_brand_values
                WHERE
                    activity_brand_values.id = abv.id
                    AND activity_brand_values.user_id = " . $id . "
                    AND DATE('activity_brand_values.created_at') >= " . $startDate . "
                    AND DATE('activity_brand_values.created_at') <= " . $endDate . "
            ) as quotation
            ";
        }

        $query = \Illuminate\Support\Facades\DB::table('activity_brand_values as abv')
            ->selectRaw("
            product_brands.id,
            product_brands.name as product_brand,
            brand_categories.name as brand_category,
            " . $extraQuery)
            ->join('product_brands', 'product_brands.id', '=', 'abv.product_brand_id')
            ->join('brand_categories', 'brand_categories.id', '=', 'product_brands.brand_category_id');

        // $query = ActivityBrandValue::selectRaw("
        // product_brands.id,
        // product_brands.name as product_brand,
        // brand_categories.name as brand_category,
        // (
        //     SELECT SUM(estimated_value)
        // )
        // ")
        //     ->join('product_brands', 'product_brands.id', '=', 'activity_brand_values.product_brand_id')
        //     ->join('brand_categories', 'brand_categories.id', '=', 'product_brands.brand_category_id');

        // if ($user instanceof Channel) {
        //     $userType = 'channel';
        // } elseif ($user->type->is(UserType::SUPERVISOR)) {
        //     $userType = 'supervisor';
        // } else {
        //     // sales
        //     $userType = 'sales';
        // }

        // $query = $query->withSum([
        //     'estimated_value as estimation' => function ($q) use ($userType, $id, $startDate, $endDate) {
        //         $q->whereCreatedAtRange($startDate, $endDate);

        //         if ($userType == 'channel') {
        //             $q->whereHas('lead', fn ($q) => $q->where('channel_id', $id));
        //         } elseif ($userType == 'supervisor') {
        //             $q->whereHas('lead', fn ($q) => $q->where('channel_id', $id));
        //         } else {
        //             // sales
        //             $q->where('user_id', $id);
        //         }
        //         // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

        //         // if ($channelId) $q->whereHas('activity', fn ($q2) => $q2->where('channel_id', $channelId));

        //         // if (request()->product_brand_id) {
        //         //     $q->where('product_brand_id', request()->product_brand_id);
        //         // }
        //     }
        // ], 'estimated_value')
        //     ->withSum([
        //         'activityBrandValues as order_value' => function ($q) use ($userType, $id, $startDate, $endDate) {
        //             $q->whereHas('order', fn ($q) => $q->whereNotIn('status', [5, 6])->whereIn('payment_status', [2, 3, 4, 6])->whereCreatedAtRange($startDate, $endDate));

        //             if ($userType == 'channel') {
        //                 $q->whereHas('lead', fn ($q) => $q->where('channel_id', $id));
        //             } elseif ($userType == 'supervisor') {
        //                 $q->whereHas('lead', fn ($q) => $q->where('channel_id', $id));
        //             } else {
        //                 // sales
        //                 $q->where('user_id', $id);
        //             }
        //         }
        //     ], 'order_value')
        //     ->withSum([
        //         'activityBrandValues as quotation' => function ($q) use ($userType, $id, $startDate, $endDate) {
        //             $q->whereHas('order', fn ($q) => $q->whereNotIn('status', [5, 6])->whereCreatedAtRange($startDate, $endDate));
        //         }
        //     ], 'order_value');


        if ($request->company_id) $query = $query->where('company_id', $request->company_id);
        if ($channelId) $query = $query->where('channel_id', $channelId);
        if ($request->user_id) $query = $query = $query->where('user_id', $request->user_id);

        if ($request->product_brand_id) {
            $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
        }

        $result = $query->get();
        return response()->json($result);

        return response()->json($result);
    }

    // public function brandDetails(Request $request)
    // {
    //     $startDate = Carbon::now();
    //     $endDate = $startDate;
    //     if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
    //         $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
    //         $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
    //     }

    //     $user = user();
    //     $channelId = $request->channel_id ?? null;

    //     $userType = $request->user_type ?? null;
    //     $id = $request->id ?? null;

    //     if ($userType && $id) {
    //         $user = match ($userType) {
    //             'bum' => User::findOrFail($id),
    //             'store' => Channel::findOrFail($id),
    //             'sales' => User::findOrFail($id),
    //             default => $user,
    //         };
    //     }

    //     $query = ActivityBrandValue::selectRaw('product_brands.name as product_brand, brand_categories.name as brand_category')
    //         ->join('product_brands', 'product_brands.id', '=', 'activity_brand_values.product_brand_id')
    //         ->join('brand_categories', 'brand_categories.id', '=', 'product_brands.brand_category_id');

    //     if ($user instanceof Channel) {
    //         $userType = 'channel';
    //         $query = $query->whereHas('order', fn ($q) => $q->where('channel_id', $user->id));
    //     } elseif ($user->type->is(UserType::SUPERVISOR)) {
    //         $userType = 'supervisor';
    //         $query = $query->whereHas('order', fn ($q) => $q->whereIn('channel_id', $user->channels->pluck('id')->all()));
    //     } else {
    //         // sales
    //         $userType = 'sales';
    //         $query = $query->where('activity_brand_values.user_id', $user->id);
    //     }

    //     $query = $query->withSum([
    //         'estimated_value as estimation' => function ($q) use ($userType, $id, $startDate, $endDate) {
    //             $q->whereCreatedAtRange($startDate, $endDate);

    //             if ($userType == 'channel') {
    //                 $q->whereHas('lead', fn ($q) => $q->where('channel_id', $id));
    //             } elseif ($userType == 'supervisor') {
    //                 $q->whereHas('lead', fn ($q) => $q->where('channel_id', $id));
    //             } else {
    //                 // sales
    //                 $q->where('user_id', $id);
    //             }
    //             // $q->whereHas('lead', fn ($q2) => $q2->whereNotIn('type', [4])); // lead type drop di allow dulu karena data new lead yang type nya drop dimunculin juga

    //             // if ($channelId) $q->whereHas('activity', fn ($q2) => $q2->where('channel_id', $channelId));

    //             // if (request()->product_brand_id) {
    //             //     $q->where('product_brand_id', request()->product_brand_id);
    //             // }
    //         }
    //     ], 'estimated_value')
    //         ->withSum([
    //             'activityBrandValues as order_value' => function ($q) use ($userType, $id, $startDate, $endDate) {
    //                 $q->whereHas('order', fn ($q) => $q->whereNotIn('status', [5, 6])->whereIn('payment_status', [2, 3, 4, 6])->whereCreatedAtRange($startDate, $endDate));

    //                 if ($userType == 'channel') {
    //                     $q->whereHas('lead', fn ($q) => $q->where('channel_id', $id));
    //                 } elseif ($userType == 'supervisor') {
    //                     $q->whereHas('lead', fn ($q) => $q->where('channel_id', $id));
    //                 } else {
    //                     // sales
    //                     $q->where('user_id', $id);
    //                 }
    //             }
    //         ], 'order_value')
    //         ->withSum([
    //             'activityBrandValues as quotation' => function ($q) use ($userType, $id, $startDate, $endDate) {
    //                 $q->whereHas('order', fn ($q) => $q->whereNotIn('status', [5, 6])->whereCreatedAtRange($startDate, $endDate));
    //             }
    //         ], 'order_value');

    //     if ($request->company_id) $query = $query->where('company_id', $request->company_id);
    //     if ($channelId) $query = $query->where('channel_id', $channelId);
    //     if ($request->user_id) $query = $query = $query->where('user_id', $request->user_id);

    //     if ($request->product_brand_id) {
    //         $query = $query->whereHas('activityBrandValues', fn ($q) => $q->where('product_brand_id', $request->product_brand_id));
    //     }

    //     $result = $query->get();
    //     return response()->json($result);

    //     return response()->json($result);
    // }
}
