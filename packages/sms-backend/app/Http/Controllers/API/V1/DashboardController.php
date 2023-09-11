<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\ActivityStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\TargetType;
use App\Models\Activity;
use App\Models\CartDemand;
use App\Models\Order;
use App\Models\Target;
use App\Models\User;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Channel;
use App\Services\ApiReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseApiController
{
    protected $maxLimit = 50;

    public function indexTopSales(Request $request, $typePV = 'value')
    {
        if ($typePV == 'target') {
            $typePV = 'percentage';
        } else {
            $typePV = 'value';
        }
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
        }

        $limit = $request->has('limit') && $request->limit != '' ? intval($request->limit) : 5;

        $topSales = Target::with(['model' => function ($query) {
            $query->select('id', 'name', 'company_id', 'channel_id', 'supervisor_id');
        }, 'model.channel' => function ($query) {
            $query->select('id', 'name', 'company_id');
        }, 'model.channel.company' => function ($query) {
            $query->select('id', 'name');
        }])->where('type', TargetType::DEALS_ORDER_PRICE)->where('model_type', 'user')
            ->whereHas('report', function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '>=', Carbon::parse($startDate))->where('end_date', '<=', Carbon::parse($endDate));
            })->selectRaw('id, model_type, model_id, target, value, IF(target = 0, 0, ((value / target) * 100)) as percentage');

        $topBum = Target::with(['model' => function ($query) {
            $query->select('id', 'name', 'company_id', 'channel_id', 'supervisor_id');
        }, 'model.channel' => function ($query) {
            $query->select('id', 'name', 'company_id');
        }, 'model.channel.company' => function ($query) {
            $query->select('id', 'name');
        }])->where('type', TargetType::DEALS_ORDER_PRICE)->where('model_type', 'user')
            ->whereHas('report', function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '>=', Carbon::parse($startDate))->where('end_date', '<=', Carbon::parse($endDate));
            })->selectRaw('id, model_type, model_id, target, value, IF(target = 0, 0, ((value / target) * 100)) as percentage');

        $topChannel = Target::with(['model' => function ($query) {
            $query->select('id', 'name');
        }, 'model.company' => function ($query) {
            $query->select('id', 'name');
        }])->where('type', TargetType::DEALS_ORDER_PRICE)->where('model_type', 'channel')
            ->whereHas('report', function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '>=', Carbon::parse($startDate))->where('end_date', '<=', Carbon::parse($endDate));
            })->selectRaw('id, model_type, model_id, target, value, IF(target = 0, 0, ((value / target) * 100)) as percentage');

        //tenancy
        $user = $request->user();
        if ($user->is_director) {
            $topSales = $topSales->whereHas('user', function ($query) use ($user) {
                // $query->where('type', 2)->whereIn('id', $user->whereCompanyId($user->company_id)->pluck('id')->toArray());
                $query->where('type', 2)->whereIn('id', $user->whereCompanyIds($user->company_ids ?? [])->pluck('id')->toArray());
            });
            $topBum = $topBum->whereHas('user', function ($query) use ($user) {
                // $query->where('type', 3)->where('supervisor_type_id', 2)->whereIn('id', $user->whereCompanyId($user->company_id)->pluck('id')->toArray());
                $query->where('type', 3)->where('supervisor_type_id', 2)->whereIn('id', $user->whereCompanyIds($user->company_ids ?? [])->pluck('id')->toArray());
            });
            $topChannel = $topChannel->whereHas('channel', function ($query) use ($user) {
                // $query->where('company_id', $user->company_id);
                $query->whereIn('company_id', $user->company_ids ?? []);
            });
        } elseif ($user->is_supervisor) {
            $topSales = $topSales->whereHas('user', function ($query) use ($user) {
                $query->where('type', 2)->whereIn('id', $user->getAllChildrenSales()->pluck('id')->toArray());
            });
            $topChannel = $topChannel->whereHas('channel', function ($query) use ($user) {
                $query->whereIn('id', $user->channelsPivot()->pluck('channel_id')->toArray());
            });
        } elseif ($user->is_sales) {
            $topSales = $topSales->whereHas('user', function ($query) use ($user) {
                $query->whereIn('id', $user->getSalesFriends()->pluck('id')->toArray());
            });
        }
        //end tenancy

        if ($request->has('company_id') && $request->company_id != '') {
            $company_id = $request->company_id;
            $topSales = $topSales->whereHasMorph('model', [User::class], function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
            $topBum = $topBum->whereHas('user', function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
            $topChannel = $topChannel->whereHas('channel', function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }

        if (($request->has('supervisor_id') && $request->supervisor_id != '') && !$request->has('channel_id')) {
            $supervisor_id = $request->supervisor_id;
            $salesIds = User::whereDescendantOf($request->supervisor_id)->whereIsSales()->get(['id'])->pluck('id')->all();
            $topSales = $topSales->whereHasMorph('model', [User::class], function ($query) use ($salesIds) {
                $query->whereIn('id', $salesIds);
            });

            $topBum = $topBum->whereHas('user', function ($query) use ($supervisor_id) {
                $query->where('id', $supervisor_id);
            });

            $channelIds = User::find($request->supervisor_id)?->channelsPivot()->pluck('channel_id')->toArray() ?? [];
            $topChannel = $topChannel->whereHas('channel', function ($query) use ($channelIds) {
                $query->whereIn('id', $channelIds);
            });
        }

        if ($request->has('channel_id') && $request->channel_id != '') {
            $channel_id = $request->channel_id;
            $topSales = $topSales->whereHasMorph('model', [User::class], function ($query) use ($channel_id) {
                $query->where('channel_id', $channel_id);
            });
            $topBum = $topBum->whereHas('user', function ($query) use ($channel_id) {
                $query->where('channel_id', $channel_id);
            });
            $topChannel = $topChannel->whereHas('channel', function ($query) use ($channel_id) {
                $query->where('id', $channel_id);
            });
        }

        if ($user->is_director) {
            $topSales = $topSales->orderBy($typePV, 'desc')->limit($limit)->get();
            $topChannel = $topChannel->orderBy($typePV, 'desc')->limit($limit)->get();
            $topBum = $topBum->orderBy($typePV, 'desc')->limit($limit)->get();
        } elseif ($user->is_supervisor) {
            $topSales = $topSales->orderBy($typePV, 'desc')->limit($limit)->get();
            $topChannel = $topChannel->orderBy($typePV, 'desc')->limit($limit)->get();
        } elseif ($user->is_sales) {
            $topSales = $topSales->orderBy($typePV, 'desc')->limit($limit)->get();
        }


        // dd($topBum);
        $dataTopSales = [];
        $number = 1;
        foreach ($topSales ?? [] as $target) {
            $dataTopSales[] = [
                'priority' => $number++,
                'model_type' => $target->model_type,
                'model_id' => $target->model_id,
                'target' => 'Rp ' . number_format($target->target, 0, ',', '.'),
                'value' => 'Rp ' . number_format(potongPPN($target->value), 0, ',', '.'),
                'percentage' => round($target->percentage) . '%',
                'model' => $target->model,
            ];
        }

        $dataTopBum = [];
        $number = 1;
        foreach ($topBum ?? [] as $target) {
            $dataTopBum[] = [
                'priority' => $number++,
                'model_type' => $target->model_type,
                'model_id' => $target->model_id,
                'target' => 'Rp ' . number_format($target->target, 0, ',', '.'),
                'value' => 'Rp ' . number_format(potongPPN($target->value), 0, ',', '.'),
                'percentage' => round($target->percentage) . '%',
                'model' => $target->model,
            ];
        }

        $dataTopChannel = [];
        $number = 1;
        foreach ($topChannel ?? [] as $target) {
            $dataTopChannel[] = [
                'priority' => $number++,
                'model_type' => $target->model_type,
                'model_id' => $target->model_id,
                'target' => 'Rp ' . number_format($target->target, 0, ',', '.'),
                'value' => 'Rp ' . number_format(potongPPN($target->value), 0, ',', '.'),
                'percentage' => round($target->percentage) . '%',
                'model' => $target->model,
            ];
        }
        return response()->json(['sales' => $dataTopSales, 'bum' => $dataTopBum, 'channel' => $dataTopChannel]);
    }

    public function topSales(Request $request, $typePV = 'value')
    {
        if ($typePV == 'target') {
            $typePV = 'percentage';
        } else {
            $typePV = 'value';
        }
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
        }

        $limit = $request->has('limit') && $request->limit != '' ? intval($request->limit) : 5;

        if ($request->type == 'sales' || $request->type == 'supervisor' || !$request->has('type')) {
            $targets = Target::with(['model' => function ($query) {
                $query->select('id', 'name', 'company_id', 'channel_id', 'supervisor_id');
            }, 'model.channel' => function ($query) {
                $query->select('id', 'name', 'company_id');
            }, 'model.channel.company' => function ($query) {
                $query->select('id', 'name');
            }])->where('type', TargetType::DEALS_ORDER_PRICE)->where('model_type', 'user')
                ->whereHas('report', function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '>=', Carbon::parse($startDate))->where('end_date', '<=', Carbon::parse($endDate));
                });
        } elseif ($request->type == 'channel') {
            $targets = Target::with(['model' => function ($query) {
                $query->select('id', 'name');
            }, 'model.company' => function ($query) {
                $query->select('id', 'name');
            }])->where('type', TargetType::DEALS_ORDER_PRICE)->where('model_type', 'channel')
                ->whereHas('report', function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '>=', Carbon::parse($startDate))->where('end_date', '<=', Carbon::parse($endDate));
                });
        }

        //tenancy
        $user = $request->user();
        if ($user->is_director) {
            if ($request->type == 'sales' || !$request->has('type')) {
                $targets = $targets->whereHas('user', function ($query) use ($user) {
                    // $query->where('type', 2)->whereIn('id', $user->whereCompanyId($user->company_id)->pluck('id')->toArray());
                    $query->where('type', 2)->whereIn('id', $user->whereCompanyIds($user->company_ids ?? [])->pluck('id')->toArray());
                });
            } elseif ($request->type == 'supervisor') {
                $targets = $targets->whereHas('user', function ($query) use ($user) {
                    // $query->where('type', 3)->where('supervisor_type_id', 2)->whereIn('id', $user->whereCompanyId($user->company_id)->pluck('id')->toArray());
                    $query->where('type', 3)->where('supervisor_type_id', 2)->whereIn('id', $user->whereCompanyIds($user->company_ids ?? [])->pluck('id')->toArray());
                });
            } else {
                $targets = $targets->whereHas('channel', function ($query) use ($user) {
                    // $query->where('company_id', $user->company_id);
                    $query->whereIn('company_id', $user->company_ids ?? []);
                });
            }
        } elseif ($user->is_supervisor) {
            if ($request->type == 'sales' || !$request->has('type')) {
                $targets = $targets->whereHas('user', function ($query) use ($user) {
                    $query->where('type', 2)->whereIn('id', $user->getAllChildrenSales()->pluck('id')->toArray());
                });
            } elseif ($request->type == 'supervisor') {
                $targets = $targets->whereHas('user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                });
            } else {
                $targets = $targets->whereHas('channel', function ($query) use ($user) {
                    $query->whereIn('id', $user->channelsPivot()->pluck('channel_id')->toArray());
                });
            }
        } elseif ($user->is_sales) {
            if ($request->type == 'sales' || !$request->has('type')) {
                $targets = $targets->whereHas('user', function ($query) use ($user) {
                    $query->whereIn('id', $user->getSalesFriends()->pluck('id')->toArray());
                });
            }
        }
        //end tenancy

        $targets = $targets->selectRaw('id, model_type, model_id, target, value, IF(target = 0, 0, ((value / target) * 100)) as percentage');

        if ($request->has('company_id') && $request->company_id != '') {
            $company_id = $request->company_id;
            if ($request->type == 'sales' || $request->type == 'supervisor' || !$request->has('type')) {
                $targets = $targets->whereHasMorph('model', [User::class], function ($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                });
            } else {
                $targets = $targets->whereHas('channel', function ($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                });
            }
        }

        if (($request->has('supervisor_id') && $request->supervisor_id != '') && !$request->has('channel_id')) {
            if ($request->type == 'sales' || !$request->has('type')) {
                $salesIds = User::whereDescendantOf($request->supervisor_id)->whereIsSales()->get(['id'])->pluck('id')->all();
                $targets = $targets->whereHasMorph('model', [User::class], function ($query) use ($salesIds) {
                    $query->whereIn('id', $salesIds);
                });
            } elseif ($request->type == 'supervisor') {
                $supervisor_id = $request->supervisor_id;
                $targets = $targets->whereHasMorph('model', [User::class], function ($query) use ($supervisor_id) {
                    $query->where('id', $supervisor_id);
                });
            } else {
                $targets = $targets->whereHas('channel', function ($query) use ($user) {
                    $query->whereIn('id', $user->channelsPivot()->pluck('channel_id')->toArray());
                });
            }
        }

        if ($request->has('channel_id') && $request->channel_id != '') {
            $channel_id = $request->channel_id;
            if ($request->type == 'sales' || $request->type == 'supervisor' || !$request->has('type')) {
                $targets = $targets->whereHasMorph('model', [User::class], function ($query) use ($channel_id) {
                    $query->where('channel_id', $channel_id);
                });
            } else {
                $targets = $targets->whereHas('channel', function ($query) use ($channel_id) {
                    $query->where('id', $channel_id);
                });
            }
        }

        $targets = $targets->orderBy($typePV, 'desc')->limit($limit)->get();
        $data = [];
        $number = 1;
        foreach ($targets as $target) {
            $data[] = [
                'priority' => $number++,
                'model_type' => $target->model_type,
                'model_id' => $target->model_id,
                'target' => 'Rp ' . number_format($target->target, 0, ',', '.'),
                'value' => 'Rp ' . number_format(potongPPN($target->value), 0, ',', '.'),
                'percentage' => round($target->percentage) . '%',
                'model' => $target->model,
            ];
        }
        return response()->json($data);
    }

    public function brandCategories(Request $request)
    {
        $data = Order::tenanted()->notCancelled()->notReturned()->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::DOWN_PAYMENT, OrderPaymentStatus::OVERPAYMENT])
            ->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->join('product_units', 'product_units.id', '=', 'order_details.product_unit_id')
            ->join('brand_categories', 'brand_categories.id', '=', 'product_units.brand_category_id');

        if ($request->has('company_id') && $request->company_id != '') $data = $data->where('order_details.company_id', $request->company_id);
        if ($request->has('channel_id') && $request->channel_id != '') $data = $data->where('channel_id', $request->channel_id);

        //tenancy
        $user = $request->user();
        if ($user->is_supervisor) {
            // $data = $data->whereHas('order', function ($q) use ($user) {
            //     $q->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray());
            // });
            $data = $data->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray());
        } elseif ($user->is_sales) {
            // $data = $data->whereHas('order', function ($q) use ($user) {
            //     $q->where('user_id', $user->id);
            // });
            $data = $data->where('user_id', $user->id);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            // $data = $data->whereHas('order', function ($q) use ($request) {
            //     $q->where('user_id', $request->user_id);
            // });
            $data = $data->where('user_id', $request->user_id);
        }
        //end tenancy

        if ($request->has('start_at') && $request->has('end_at')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
            // $data = $data->whereHas('order', function ($q) use ($startDate, $endDate) {
            //     $q->whereBetween('deal_at', [$startDate, $endDate]);
            // });
            $data = $data->whereBetween('deal_at', [$startDate, $endDate]);
        }

        $data = $data->selectRaw('orders.total_price, brand_categories.id as id, brand_categories.name')
            ->groupBy('orders.id', 'brand_categories.id', 'brand_categories.name')
            ->orderBy('total_price', 'desc')
            ->get();

        $data = array_reduce($data->toArray(), function ($carry, $item) {
            if (!isset($carry[$item['id']])) {
                $carry[$item['id']] = ['id' => $item['id'], 'total_price' => $item['total_price'], 'name' => $item['name']];
            } else {
                $carry[$item['id']]['total_price'] += $item['total_price'];
            }
            return $carry;
        });

        $result = [];
        $brandCategories = \Illuminate\Support\Facades\DB::table('brand_categories')->selectRaw('id, name, IFNULL(NULL, 0) as total_price')->get();
        foreach ($brandCategories as $bc) {
            if (isset($data[$bc->id])) {
                $result[] = [
                    'id' => $data[$bc->id]['id'],
                    'name' => $data[$bc->id]['name'],
                    'total_price' => 'Rp ' . number_format(potongPPN($data[$bc->id]['total_price']), 0, ',', '.'),
                ];
            } else {
                $result[] = [
                    'id' => $bc->id,
                    'name' => $bc->name,
                    'total_price' => 'Rp ' . number_format(potongPPN($bc->total_price), 0, ',', '.'),
                ];
            }
        }

        return response()->json($result);
    }

    public function detailBrandCategories(Request $request, $brandCategoryId)
    {
        // $data = OrderDetail::join('product_units', 'product_units.id', '=', 'order_details.product_unit_id')
        //     ->join('products', 'products.id', '=', 'product_units.product_id')
        //     ->join('product_brands', 'product_brands.id', '=', 'products.product_brand_id')
        //     ->join('product_brand_categories', 'product_brand_categories.product_brand_id', '=', 'product_brands.id')
        //     ->join('brand_categories', 'brand_categories.id', '=', 'product_brand_categories.brand_category_id')
        //     ->whereHas('order', fn ($query) => $query->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::DOWN_PAYMENT, OrderPaymentStatus::OVERPAYMENT])->notCancelled()->notReturned())
        //     ->where('brand_categories.id', $brandCategoryId);
        $data = Order::tenanted()->notCancelled()->notReturned()->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::DOWN_PAYMENT, OrderPaymentStatus::OVERPAYMENT])
            ->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->join('product_units', 'product_units.id', '=', 'order_details.product_unit_id')
            ->join('products', 'products.id', '=', 'product_units.product_id')
            ->join('product_brands', 'product_brands.id', '=', 'products.product_brand_id')
            ->join('brand_categories', 'brand_categories.id', '=', 'product_units.brand_category_id');

        if ($request->has('company_id') && $request->company_id != '') $data = $data->where('order_details.company_id', $request->company_id);
        if ($request->has('channel_id') && $request->channel_id != '') $data = $data->where('channel_id', $request->channel_id);
        // if ($request->has('channel_id') && $request->channel_id != '') $data = $data->whereHas('order', function ($q) use ($request) {
        //     $q->where('channel_id', $request->channel_id);
        // });

        //tenancy
        $user = $request->user();
        if ($user->is_supervisor) {
            $data = $data->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray());
        } elseif ($user->is_sales) {
            $data = $data->where('user_id', $user->id);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $data = $data->where('user_id', $request->user_id);
        }
        //end tenancy

        if ($request->has('start_at') && $request->has('end_at')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
            $data = $data->whereBetween('deal_at', [$startDate, $endDate]);
        }

        $data = $data->selectRaw('orders.total_price, brand_categories.id as id, brand_categories.name')
            ->groupBy('orders.id', 'brand_categories.id', 'brand_categories.name')
            ->orderBy('total_price', 'desc')
            ->get();

        $data = array_reduce($data->toArray(), function ($carry, $item) {
            if (!isset($carry[$item['id']])) {
                $carry[$item['id']] = ['id' => $item['id'], 'total_price' => $item['total_price'], 'name' => $item['name']];
            } else {
                $carry[$item['id']]['total_price'] += $item['total_price'];
            }
            return $carry;
        });

        if (!$data) {
            return response()->json([
                'status' => collect(ActivityStatus::getInstances())->pluck('description', 'value'),
                'data' => [
                    'id' => 0,
                    'name' => '-',
                    'total_price' => '-',
                    'estimated_value' => '-',
                ]
            ]);
        }

        $productBrandIds = collect($data)->pluck('id')->all();
        $dataEstimation = Activity::tenanted()->join('activity_product_brand', 'activity_product_brand.activity_id', '=', 'activities.id')
            ->join('product_brands', 'product_brands.id', '=', 'activity_product_brand.product_brand_id')
            ->whereHas('activityProductBrands')
            ->whereIn('product_brands.id', $productBrandIds);

        if ($request->has('status') && $request->status != '') $dataEstimation = $dataEstimation->where('activities.status', $request->status);
        if ($request->has('company_id') && $request->company_id != '') $dataEstimation = $dataEstimation->where('product_brands.company_id', $request->company_id);
        if ($request->has('channel_id') && $request->channel_id != '') $dataEstimation = $dataEstimation->where('activities.channel_id', $request->channel_id);
        if ($request->has('user_id') && $request->user_id != '') $dataEstimation = $dataEstimation->where('activities.user_id', $request->user_id);
        if ($request->has('start_at') && $request->has('end_at')) {
            $dataEstimation = $dataEstimation->whereBetween('activities.created_at', [$startDate, $endDate]);
        }

        $dataEstimation = $dataEstimation
            ->selectRaw('product_brands.id, product_brands.name, sum(activities.estimated_value) as estimated_value')
            ->groupBy('product_brands.id', 'product_brands.name')
            ->get();
        $dataEstimation = array_combine($dataEstimation->pluck('id')->all(), $dataEstimation->toArray());

        $results = [];
        foreach ($data as $d) {
            $estimated_value = $dataEstimation[$d['id']]['estimated_value'] ?? 0;
            array_push($results, [
                'id' => $d['id'],
                'name' => $d['name'],
                'total_price' => 'Rp ' . number_format(potongPPN($d['total_price']), 0, ',', '.'),
                'estimated_value' => 'Rp ' . number_format($estimated_value, 0, ',', '.'),
            ]);
        }

        return response()->json([
            'status' => collect(ActivityStatus::getInstances())->pluck('description', 'value'),
            'data' => $results
        ]);
    }

    public function interiorDesign(Request $request)
    {
        $data = Order::tenanted()->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::OVERPAYMENT, OrderPaymentStatus::DOWN_PAYMENT])->notCancelled()->notReturned()->selectRaw('IFNULL(sum(total_price), 0) as total');

        if ($request->has('company_id') && $request->company_id != '') {
            $company_id = $request->company_id;
            $data->where('company_id', $company_id);
        }

        if ($request->has('channel_id') && $request->channel_id != '') {
            $channel_id = $request->channel_id;
            $data->where('channel_id', $channel_id);
        }

        // tenanted
        $user = $request->user();
        if ($user->is_supervisor) {
            $data = $data->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray());
        } elseif ($user->is_sales) {
            $data = $data->where('user_id', $user->id);
        }
        // end tenanted

        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
            $data->whereBetween('deal_at', [$startDate, $endDate]);
        }

        $data = $data->first();
        $total = potongPPN($data->total);
        $data['total'] = 'Rp ' . number_format($total, 0, ',', '.');
        return response()->json($data);
    }

    public function interiorDesignDetail(Request $request, $interiorDesignId = null)
    {
        $data = Order::tenanted()->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::OVERPAYMENT, OrderPaymentStatus::DOWN_PAYMENT])->notCancelled()->notReturned();

        if ($interiorDesignId) {
            $data = $data->join('channels', 'channels.id', '=', 'orders.channel_id')->join('users', 'users.id', '=', 'orders.user_id')->select('orders.deal_at as date', 'channels.name as channel', 'users.name as sales', 'orders.total_price as total')->orderBy('orders.deal_at', 'desc');
        } else {
            $data = $data->with(['interiorDesign' => function ($query) {
                return $query->select('id', 'name');
            }])->selectRaw('sum(total_price) as total')->orderBy('total', 'desc');
        }

        if ($request->has('company_id') && $request->company_id != '') {
            $company_id = $request->company_id;
            $data->where('orders.company_id', $company_id);
        }

        if ($request->has('channel_id') && $request->channel_id != '') {
            $channel_id = $request->channel_id;
            $data->where('orders.channel_id', $channel_id);
        }

        // tenanted
        $user = $request->user();
        if ($user->is_supervisor) {
            $data = $data->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray());
        } elseif ($user->is_sales) {
            $data = $data->where('user_id', $user->id);
        }
        // end tenanted

        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
            $data->whereBetween('orders.deal_at', [$startDate, $endDate]);
        }

        $data = $data->get();

        $results = [];
        $priority = 1;
        foreach ($data as $d) {
            $d['priority'] = $priority++;
            $d['total'] = 'Rp ' . number_format(potongPPN($d->total), 0, ',', '.');
            array_push($results, $d);
        }

        return response()->json($results);
    }

    public function pelunasan(Request $request)
    {
        $channel_id = $request->channel_id;
        $company_id = $request->company_id;
        $start_at = $request->start_at;
        $end_at = $request->end_at;
        $conditions = [
            'channel_id' => $channel_id,
            'company_id' => $company_id,
        ];

        $user = tenancy()->getUser();
        $sales_id = app(User::class)->userSalesIds($user, $conditions);
        $orders = Order::tenanted()->with(['customer' => function ($q) {
            $q->select('id', 'first_name', 'last_name');
        }, 'activity' => function ($q) {
            $q->select('id', 'order_id');
        }])->whereIn('user_id', $sales_id)->whereHas('orderPayments', fn ($q) => $q->where('status', \App\Enums\PaymentStatus::APPROVED))->whereIn('payment_status', [OrderPaymentStatus::DOWN_PAYMENT, OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::OVERPAYMENT])->notCancelled()->notReturned();
        if (($start_at && $start_at != '') && ($end_at && $end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $end_at)->endOfDay();
            $orders->whereBetween('deal_at', [$startDate, $endDate]);
        }
        $orders = $orders->selectRaw('id, deal_at, invoice_number, customer_id')->orderBy('deal_at', 'desc')->get();
        $data = [];
        foreach ($orders as $o) {
            array_push($data, [
                'activity_id' => $o->activity->id,
                'date' => date('d-M-Y', strtotime($o->deal_at)),
                'invoice' => $o->invoice_number,
                'customer' => $o->customer->fullName,
            ]);
        }
        return response()->json($data);
    }

    public function cartDemands(Request $request)
    {
        $conditions = [
            'channel_id' => '',
            'company_id' => '',
        ];

        $conditions['company_id'] = $request->company_id ?? auth()->user()->company_id;

        if ($request->has('channel_id') && $request->channel_id != '') {
            $conditions['channel_id'] = $request->channel_id;
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $sales_id = $request->user_id;
        } else {
            //tenanted
            $user = tenancy()->getUser();
            $sales_id = app(User::class)->userSalesIds($user, $conditions);
        }

        $cartDemands = CartDemand::whereOrdered()->whereIn('user_id', $sales_id)->whereBetween('created_at', [$startDate, $endDate])->whereHas('order', fn ($query) => $query->notCancelled()->notReturned())->sum('total_price');

        return response()->json(rupiah(potongPPN($cartDemands)));
    }

    public function cartDemandDetail(Request $request)
    {
        $conditions = [
            'channel_id' => '',
            'company_id' => '',
        ];

        if ($request->has('company_id') && $request->company_id != '') {
            $conditions['company_id'] = $request->company_id ?? auth()->user()->company_id;
        }

        if ($request->has('channel_id') && $request->channel_id != '') {
            $conditions['channel_id'] = $request->channel_id;
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $sales_id = $request->user_id;
        } else {
            //tenanted
            $user = tenancy()->getUser();
            $sales_id = app(User::class)->userSalesIds($user, $conditions);
        }

        $orders = CartDemand::with(['order' => function ($q) {
            return $q->join('activities', 'activities.order_id', '=', 'orders.id')->selectRaw('orders.id, activities.id as activity_id, orders.invoice_number, orders.total_price, orders.customer_id');
        }, 'order.customer'])->whereOrdered()->whereIn('user_id', $sales_id)->whereBetween('created_at', [$startDate, $endDate])->whereHas('order', fn ($query) => $query->notCancelled()->notReturned())->select('id', 'order_id')->get();

        $data = [];
        foreach ($orders as $o) {
            $o['order']['total_price_format'] = rupiah(potongPPN($o->order->total_price));
            $data[] = $o;
        }

        return response()->json($data);
    }

    public function media()
    {
        $medias = \Illuminate\Support\Facades\DB::table('media')->orderBy('id', 'desc')->limit(10)->get();
        return response()->json($medias);
    }

    public function salesEstimation(Request $request, $brand_category_id)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
        }

        $activities = Activity::tenanted()->join('customers', 'customers.id', '=', 'activities.customer_id')
            ->join('users as sales', 'sales.id', '=', 'activities.user_id')
            ->join('users as bum', 'bum.id', '=', 'sales.supervisor_id')
            ->join('activity_brand_values', 'activity_brand_values.activity_id', '=', 'activities.id')
            ->join('product_brands', 'product_brands.id', '=', 'activity_brand_values.product_brand_id')
            ->join('product_brand_categories', 'product_brand_categories.product_brand_id', '=', 'product_brands.id')
            ->selectRaw('
                    activity_brand_values.lead_id, sales.name as sales, bum.name as bum, customers.first_name as customer, customers.id as customer_id, activities.id as activity_id, product_brands.name as brand, activity_brand_values.estimated_value as estimated_value, activity_brand_values.order_value as order_value, activities.created_at
                    ,(
                        SELECT SUM(abv.estimated_value) FROM activity_brand_values abv WHERE abv.lead_id = activities.lead_id
                    ) total_estimated_value
                ')
            ->whereBetween('activities.created_at', [$startDate, $endDate]);

        if ($brand_category_id != 'all') {
            $activities = $activities->where('product_brand_categories.brand_category_id', $brand_category_id);
        }

        if ($request->has('channel_id') && $request->channel_id != '') {
            $activities = $activities->where('activities.channel_id', $request->channel_id);
        }

        if ($request->has('supervisor_id') && $request->supervisor_id != '') {
            $salesIds = User::tenanted()->whereDescendantOf($request->supervisor_id)->whereIsSales()->get(['id'])->pluck('id')->all();
            $activities = $activities->whereIn('activities.user_id', $salesIds);
        }

        if ($request->has('sales_id') && $request->sales_id != '') {
            $activities = $activities->where('activities.user_id', $request->sales_id);
        }

        if ($request->has('product_brand_id') && $request->product_brand_id != '') {
            $activities = $activities = $activities->whereIn('activities.id', \App\Models\ActivityBrandValue::where('product_brand_id', $request->product_brand_id)->pluck('activity_id')->all() ?? []);
        }

        $activities = $activities->groupBy('activity_brand_values.lead_id', 'activity_brand_values.product_brand_id')
            ->get();

        $data = [];
        foreach ($activities as $a) {
            $a['order_value'] = potongPPN($a['order_value']);

            if ($request->device == 'mobile') {
                $data[] = $a;
            } else {
                $data[$a->lead_id][] = $a;
            }
        }

        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SalesEstimationReport($activities), 'sales-estimation-' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        return response()->json($data);
    }

    public function reportLeads(Request $request)
    {
        $result = app(ApiReportService::class)->reportLeadsNew();
        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportLeadsExport($result), 'report-leads-' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }
        return response()->json($result);
    }

    public function reportLeadsOptimized(Request $request)
    {
        $result = app(ApiReportService::class)->reportLeadsOptimized();

        $result = json_decode(json_encode($result), true);

        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportLeadsExport($result), 'report-leads-' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        return response()->json($result);
    }

    public function reportLeadsDetails(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $query = DB::table('leads')
            ->leftJoin('customers', 'leads.customer_id', '=', 'customers.id')
            ->join('users', 'users.id', '=', 'leads.user_id')
            ->leftJoin('users as sl', 'users.supervisor_id', '=', 'sl.id')
            ->leftJoin('users as bum', 'sl.supervisor_id', '=', 'bum.id')
            ->join('channels', 'channels.id', '=', 'leads.channel_id')
            ->leftJoin('activities', 'activities.lead_id', '=', 'leads.id')
            ->selectRaw('
                leads.id as lead_id,
                leads.label,
                leads.user_id,
                customers.first_name as first_name,
                customers.last_name as last_name,
                users.name,
                channels.id as channel_id,
                channels.name as channel,
                sl.name as store_leader,
                bum.id as supervisor_id,
                bum.name as bum,
                COUNT(distinct leads.id) as total_leads,
                COUNT(IF(activities.status = 4, 1, NULL)) as closed,
                COUNT(IF(activities.status = 3, 1, NULL)) as cold,
                COUNT(IF(activities.status = 2, 1, NULL)) as warm,
                COUNT(IF(activities.status = 1, 1, NULL)) as hot,
                SUM(activities.estimated_value) as estimated_value,
                (
                    SELECT SUM(orders.total_price) FROM orders WHERE orders.lead_id = leads.id AND DATE(orders.deal_at) >= "' . $startDate . '" AND DATE(orders.deal_at) <= "' . $endDate . '" AND orders.deleted_at IS NULL AND orders.status NOT IN (5,6)
                ) as quotation,
                (
                    SELECT SUM(orders.total_price) FROM orders WHERE orders.lead_id = leads.id AND orders.payment_status IN (2, 3, 4, 6) AND DATE(orders.deal_at) >= "' . $startDate . '" AND DATE(orders.deal_at) <= "' . $endDate . '" AND orders.deleted_at IS NULL
                ) as invoice_price,
                (
                    SELECT SUM(orders.amount_paid) FROM orders WHERE orders.lead_id = leads.id AND orders.payment_status IN (2, 3, 4, 6) AND DATE(orders.deal_at) >= "' . $startDate . '" AND DATE(orders.deal_at) <= "' . $endDate . '" AND orders.deleted_at IS NULL
                ) as amount_paid
            ')
            ->whereNull('leads.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereDate('leads.created_at', '>=', $startDate)
            ->whereDate('leads.created_at', '<=', $endDate)
            ->where('users.type', 2);

        if ($request->sales_name) $query = $query->where('users.name', 'like', '%' . $request->sales_name . '%');
        if ($request->user_id) $query = $query->where('leads.user_id', $request->user_id);

        $user = user();
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

        $result = $query->groupByRaw('leads.id')
            ->orderByDesc('leads.id')
            ->get();

        return response()->json($result);
    }

    public function reportBrands(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $user = user();
        // $company_id = $request->company_id ? [$request->company_id] : $user->company_ids ?? $user->userCompanies->pluck('id')->all();
        $company_id = $request->company_id ? $request->company_id : ($user->company_id ?? $user->company_ids[0] ?? 1);

        $productBrands = DB::table('product_brands')->selectRaw('name as product_brand, 0 as estimated_value, 0 as order_value')->whereNull('deleted_at')->where('show_in_moves', 1)->where('company_id', $company_id)->get()->toArray();

        $query = DB::table('users')
            ->leftJoin('channels', 'channels.id', '=', 'users.channel_id')
            ->leftJoin('leads', 'leads.user_id', '=', 'users.id')
            ->join('customers', 'customers.id', '=', 'leads.customer_id')
            ->leftJoin('users as sl', 'users.supervisor_id', '=', 'sl.id')
            ->leftJoin('users as bum', 'sl.supervisor_id', '=', 'bum.id')
            ->leftJoin('activity_brand_values', 'activity_brand_values.lead_id', '=', 'leads.id')
            ->leftJoin('product_brands', 'product_brands.id', '=', 'activity_brand_values.product_brand_id')
            ->selectRaw('
            users.id as user_id,
            users.name as sales,
            sl.name as store_leader,
            bum.name as bum,
            channels.name as channel,
            COUNT(DISTINCT leads.id) as total_leads,
            product_brands.name as product_brand,
            SUM(activity_brand_values.estimated_value) as estimated_value,
            SUM(activity_brand_values.order_value) as order_value
            ')
            ->whereNull('leads.deleted_at')
            ->whereNull('users.deleted_at')
            ->where('users.type', 2)
            ->whereBetween('leads.created_at', [$startDate, $endDate]);

        if ($request->sales_name) $query = $query->where('users.name', 'like', '%' . $request->sales_name . '%');
        if ($user->is_director) {
            // $channel_ids = DB::table('channels')->whereIn('company_id', $company_id)->pluck('id')->all();
            $channel_ids = DB::table('channels')->where('company_id', $company_id)->pluck('id')->all();
            $query = $query->whereIn('leads.channel_id', $channel_ids ?? []);
        } elseif ($user->is_supervisor) {
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('leads.user_id', $user_ids ?? []);
        } else {
            $query = $query->where('leads.user_id', $user->id);
        }

        if ($request->supervisor_id) $query = $query->whereIn('leads.user_id', \App\Models\User::findOrFail($request->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if ($request->channel_id) $query = $query->where('leads.channel_id', $request->channel_id);
        if ($request->user_id) $query = $query->where('leads.user_id', $request->user_id);
        if ($request->product_brand_name) $query = $query->where('product_brands.product_brand_name', $request->product_brand_name);
        if ($request->sales_name) $query = $query->where('users.name', $request->sales_name);

        $result = $query->groupByRaw('users.id')
            ->orderByDesc('leads.id')
            ->get();

        $datas = [];
        $total['product_brands'] = $productBrands;
        $productBrandList = DB::table('product_brands')->selectRaw('name as product_brand, 0 as estimated_value, 0 as order_value')->whereNull('deleted_at')->where('show_in_moves', 1)->where('company_id', $company_id)->get()->toArray();

        foreach ($result as $i => $r) {
            $datas[$r->user_id]['user_id'] = $r->user_id;
            $datas[$r->user_id]['sales'] = $r->sales;
            $datas[$r->user_id]['store_leader'] = $r->store_leader;
            $datas[$r->user_id]['bum'] = $r->bum;
            $datas[$r->user_id]['channel'] = $r->channel;
            $datas[$r->user_id]['total_leads'] = $r->total_leads;

            $key = array_search($r->product_brand, array_column($productBrands, 'product_brand'));
            if (!isset($datas[$r->user_id]['product_brands'])) {
                $datas[$r->user_id]['product_brands'] = $productBrandList;
            }

            $datas[$r->user_id]['product_brands'][$key] = [
                'product_brand' => $r->product_brand,
                'estimated_value' => (int)$r->estimated_value,
                'order_value' => (int)$r->order_value,
            ];

            $total['product_brands'][$key]->estimated_value += $r->estimated_value;
            $total['product_brands'][$key]->order_value += $r->order_value;

            if (isset($total['leads'])) {
                $total['leads'] += $r->total_leads;
            } else {
                $total['leads'] = $r->total_leads;
            }

            if (isset($total['estimation'])) {
                $total['estimation'] += $r->estimated_value;
            } else {
                $total['estimation'] = $r->estimated_value;
            }

            if (isset($total['quotation'])) {
                $total['quotation'] += $r->order_value;
            } else {
                $total['quotation'] = $r->order_value;
            }


            if (isset($datas[$r->user_id]['total_estimated'])) {
                $datas[$r->user_id]['total_estimated'] += $r->estimated_value;
            } else {
                $datas[$r->user_id]['total_estimated'] = $r->estimated_value;
            }

            if (isset($datas[$r->user_id]['total_quotation'])) {
                $datas[$r->user_id]['total_quotation'] += $r->order_value;
            } else {
                $datas[$r->user_id]['total_quotation'] = $r->order_value;
            }
        }

        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportBrands(array_values($datas)), 'report-brands' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        return response()->json([
            'data' => array_values($datas),
            'total' => $total
        ]);
    }

    public function reportBrandsDetails(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $user = user();
        // $company_id = $request->company_id ? [$request->company_id] : $user->company_ids ?? $user->userCompanies->pluck('id')->all();
        $company_id = $request->company_id ? $request->company_id : ($user->company_id ?? $user->company_ids[0] ?? 1);

        $productBrands = DB::table('product_brands')->selectRaw('name as product_brand, 0 as estimated_value, 0 as order_value')->whereNull('deleted_at')->where('show_in_moves', 1)->where('company_id', $company_id)->get()->toArray();

        $query = DB::table('users')
            ->leftJoin('channels', 'channels.id', '=', 'users.channel_id')
            ->leftJoin('leads', 'leads.user_id', '=', 'users.id')
            ->join('customers', 'customers.id', '=', 'leads.customer_id')
            ->leftJoin('users as sl', 'users.supervisor_id', '=', 'sl.id')
            ->leftJoin('users as bum', 'sl.supervisor_id', '=', 'bum.id')
            ->leftJoin('lead_categories', 'lead_categories.id', '=', 'leads.lead_category_id')
            ->leftJoin('activity_brand_values', 'activity_brand_values.lead_id', '=', 'leads.id')
            ->leftJoin('product_brands', 'product_brands.id', '=', 'activity_brand_values.product_brand_id')
            ->selectRaw('
            leads.id as lead_id,
            leads.label,
            users.name as sales,
            channels.name as channel,
            IF(customers.last_name != "", CONCAT(customers.first_name, " ", customers.last_name), customers.first_name) as customer,
            customers.phone,
            lead_categories.name as source,
            product_brands.name as product_brand,
            SUM(activity_brand_values.estimated_value) as estimated_value,
            SUM(activity_brand_values.order_value) as order_value
            ')
            ->whereNull('leads.deleted_at')
            ->whereBetween('leads.created_at', [$startDate, $endDate]);

        if ($request->sales_name) $query = $query->where('users.name', 'like', '%' . $request->sales_name . '%');
        if ($user->is_director) {
            $channel_ids = DB::table('channels')->where('company_id', $company_id)->pluck('id')->all();
            $query = $query->whereIn('leads.channel_id', $channel_ids ?? []);
        } elseif ($user->is_supervisor) {
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('leads.user_id', $user_ids ?? []);
        } else {
            $query = $query->where('leads.user_id', $user->id);
        }

        if ($request->supervisor_id) $query = $query->whereIn('leads.user_id', \App\Models\User::findOrFail($request->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if ($request->channel_id) $query = $query->where('leads.channel_id', $request->channel_id);
        if ($request->user_id) $query = $query->where('leads.user_id', $request->user_id);
        if ($request->product_brand_name) $query = $query->where('product_brands.product_brand_name', $request->product_brand_name);
        if ($request->sales_name) $query = $query->where('users.name', $request->sales_name);

        $result = $query->groupByRaw('leads.id, activity_brand_values.product_brand_id')
            ->orderByDesc('leads.id')
            ->get();

        $datas = [];
        foreach ($result as $r) {
            $datas[$r->lead_id]['lead_id'] = $r->lead_id;
            $datas[$r->lead_id]['label'] = $r->label;
            $datas[$r->lead_id]['sales'] = $r->sales;
            $datas[$r->lead_id]['channel'] = $r->channel;
            $datas[$r->lead_id]['customer'] = $r->customer;
            $datas[$r->lead_id]['phone'] = $r->phone;
            $datas[$r->lead_id]['source'] = $r->source;

            $key = array_search($r->product_brand, array_column($productBrands, 'product_brand'));
            if (!isset($datas[$r->lead_id]['product_brands'])) {
                $datas[$r->lead_id]['product_brands'] = $productBrands;
            }

            $datas[$r->lead_id]['product_brands'][$key] = [
                'product_brand' => $r->product_brand,
                'estimated_value' => (int)$r->estimated_value,
                'order_value' => (int)$r->order_value,
            ];

            if (isset($datas[$r->lead_id]['total_estimated'])) {
                $datas[$r->lead_id]['total_estimated'] += $r->estimated_value;
            } else {
                $datas[$r->lead_id]['total_estimated'] = $r->estimated_value;
            }

            if (isset($datas[$r->lead_id]['total_quotation'])) {
                $datas[$r->lead_id]['total_quotation'] += $r->order_value;
            } else {
                $datas[$r->lead_id]['total_quotation'] = $r->order_value;
            }
        }
        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportBrandsDetail(array_values($datas)), 'report-brands-detail' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }
        return response()->json(array_values($datas));
    }

    public function reportDrop(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $query = Lead::tenanted()->with('sales', 'customer', 'latestActivity', 'channel')->whereNull('leads.deleted_at')
            ->where('type', 4)
            ->whereDate('created_at', '>=', date($startDate))
            ->whereDate('created_at', '<=', date($endDate));

        $user = user();
        if ($user->is_director) {
            $company_id = $request->company_id ? [$request->company_id] : $user->company_ids;

            $channel_ids = DB::table('channels')->whereIn('company_id', $company_id)->pluck('id')->all();
            $query = $query->whereIn('channel_id', $channel_ids ?? []);
        } else if ($user->is_supervisor) {
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('user_id', $user_ids ?? []);
        } else {
            $query = $query->where('user_id', $user->id);
        }

        if ($request->supervisor_id) $query = $query->whereIn('user_id', \App\Models\User::findOrFail($request->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if ($request->channel_id) $query = $query->where('channel_id', $request->channel_id);
        if ($request->sales_id) $query = $query->where('user_id', $request->sales_id);
        if ($request->product_brand_id) $query = $query->whereIn('id', \App\Models\ActivityBrandValue::where('product_brand_id', $request->product_brand_id)->pluck('lead_id')->all() ?? []);

        // $result = $query->groupByRaw('leads.id')->get();
        $result = $query->get();
        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DropLeadsReport($result), 'drop-leads-report-' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        return response()->json($result);
    }

    public function reportHot(Request $request)
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
            ->leftJoin('activities', 'activities.lead_id', '=', 'leads.id')
            ->leftJoin('orders', 'orders.id', '=', 'activities.order_id')
            ->selectRaw('
                leads.id as lead_id,
                leads.label,
                leads.user_id,
                leads.customer_id,
                users.name,
                channels.id as channel_id,
                channels.name as channel,
                sl.name as store_leader,
                bum.id as supervisor_id,
                bum.name as bum,
                activities.id as activity_id,
                activities.order_id as order_id,
                activities.estimated_value as estimated_value,
                (
                    SELECT SUM(orders.total_price) FROM orders WHERE orders.id = activities.order_id AND orders.deleted_at IS NULL AND orders.status NOT IN (5,6)
                ) as quotation
            ')
            ->whereRaw('orders.status NOT IN (5,6)')
            ->whereNull('leads.deleted_at')
            ->whereNull('users.deleted_at')
            // ->whereNull('orders.deleted_at')
            ->where('activities.status', 1)

            // ->whereRaw('orders.payment_status IN (2, 3, 4, 6)')
            // ->whereDate('orders.deal_at', '>=', date($startDate))
            // ->whereDate('orders.deal_at', '<=', date($endDate))

            ->whereDate('activities.created_at', '>=', date($startDate))
            ->whereDate('activities.created_at', '<=', date($endDate))
            ->where('users.type', 2);

        $user = user();
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
        if ($request->sales_id) $query = $query->where('leads.user_id', $request->sales_id);
        if ($request->product_brand_id) $query = $query->whereIn('leads.id', \App\Models\ActivityBrandValue::where('product_brand_id', $request->product_brand_id)->pluck('lead_id')->all() ?? []);

        // $result = $query->groupByRaw('leads.id')->get();
        $result = $query->get();
        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportLeadsHot($result), 'report-leads-hot' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        foreach ($result as $r) {
            $r->user = User::find($r->user_id);
            $r->lead = Lead::find($r->lead_id);
            $r->customer = Customer::find($r->customer_id);
            $r->order = Order::find($r->order_id);
            $r->activity = Activity::find($r->activity_id);
        }

        return response()->json($result);
    }

    public function reportStatus(Request $request)
    {
        $activityStatus = \App\Enums\ActivityStatus::fromKey(strtoupper($request->status));

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
            ->leftJoin('activities', 'activities.lead_id', '=', 'leads.id')
            ->selectRaw('activities.id as activity_id')
            ->whereNull('leads.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereDate('activities.created_at', '>=', date($startDate))
            ->whereDate('activities.created_at', '<=', date($endDate))
            ->where('activities.status', $activityStatus->value)
            ->where('users.type', 2);

        $user = user();
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
        if ($request->sales_id) $query = $query->where('leads.user_id', $request->sales_id);
        if ($request->product_brand_id) $query = $query->whereIn('leads.id', \App\Models\ActivityBrandValue::where('product_brand_id', $request->product_brand_id)->pluck('lead_id')->all() ?? []);

        $result = $query->groupBy('activities.id')
            ->orderByDesc('activities.id')
            ->get()->pluck('activity_id')->all();

        $query = fn ($q) => $q->whereIn('id', $result)->with(['lead', 'user', 'customer', 'order']);

        if ($request?->is_export) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LeadsActivityReport(\App\Classes\CustomQueryBuilder::buildResource(\App\Models\Activity::class, \App\Http\Resources\V1\Activity\ActivityResource::class, $query)), 'leads-activity-report-' . date('M-Y') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        return \App\Classes\CustomQueryBuilder::buildResource(\App\Models\Activity::class, \App\Http\Resources\V1\Activity\ActivityResource::class, $query);
    }

    public function reportStatusNew(Request $request)
    {
        $activityStatus = \App\Enums\ActivityStatus::fromKey(strtoupper($request->status));

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $query = Lead::tenanted()->has('sales')->with('customer')->withSum('leadActivities as total_estimated_value', 'estimated_value')->withSum([
            'leadActivityOrders as total_quotation' => function ($q) use ($startDate, $endDate) {
                $q->whereDate('deal_at', '>=', date($startDate));
                $q->whereDate('deal_at', '<=', date($endDate));
                $q->whereNotIn('orders.status', [5, 6]);
            }
        ], 'total_price')->whereHas('leadActivities', function ($q) use ($activityStatus, $startDate, $endDate) {
            $q->whereDate('created_at', '>=', date($startDate));
            $q->whereDate('created_at', '<=', date($endDate));
            $q->where('status', $activityStatus->value);
        });

        $user = user();
        if ($user->is_director) {
            $company_id = $request->company_id ? [$request->company_id] : $user->company_ids;

            $channel_ids = Channel::whereIn('company_id', $company_id)->pluck('id')->all();
            $query = $query->whereIn('channel_id', $channel_ids ?? []);
        } else if ($user->is_supervisor) {
            $user_ids = $user->getAllChildrenSales()->pluck('id')->all();
            $query = $query->whereIn('user_id', $user_ids ?? []);
        } else {
            $query = $query->where('user_id', $user->id);
        }

        if ($request->supervisor_id) $query = $query->whereIn('user_id', \App\Models\User::findOrFail($request->supervisor_id)->getAllChildrenSales()->pluck('id')->all() ?? []);
        if ($request->channel_id) $query = $query->where('channel_id', $request->channel_id);
        if ($request->sales_id) $query = $query->where('user_id', $request->sales_id);
        if ($request->product_brand_id) $query = $query->whereHas('activityBrandValues', function ($q) use ($request) {
            $q->where('product_brand_id', $request->product_brand_id);
        });

        $result = $query->get();

        return response()->json($result);
    }
}
