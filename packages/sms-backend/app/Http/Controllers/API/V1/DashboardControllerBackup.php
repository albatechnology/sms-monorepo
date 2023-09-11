<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\ActivityStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\TargetType;
use App\Models\Activity;
use App\Models\CartDemand;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Target;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardControllerBackup extends BaseApiController
{
    protected $maxLimit = 50;

    public function topSales(Request $request, $type = 'value')
    {
        if ($type == 'target') {
            $type = 'percentage';
        } else {
            $type = 'value';
        }
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
        }

        $limit = $request->has('limit') && $request->limit != '' ? intval($request->limit) : 5;

        $targets = Target::with(['model' => function ($query) {
            $query->select('id', 'name', 'company_id', 'channel_id', 'supervisor_id');
        }, 'model.channel' => function ($query) {
            $query->select('id', 'name', 'company_id');
        }, 'model.channel.company' => function ($query) {
            $query->select('id', 'name');
        }])->where('type', TargetType::DEALS_ORDER_PRICE)->where('model_type', 'user')
            ->whereHas('report', function ($query) use ($startDate, $endDate) {
                // $query->whereDate('start_date', Carbon::parse($startDate))->whereDate('end_date', Carbon::parse($endDate));
                $query->where('start_date', '>=', Carbon::parse($startDate))->where('end_date', '<=', Carbon::parse($endDate));
            });

        //tenancy
        $user = $request->user();
        if ($user->is_director) {
            $targets = $targets->whereHas('model', function ($query) use ($user) {
                $query->where('type', 2)->whereIn('id', $user->whereCompanyId($user->company_id)->pluck('id')->toArray());
            });
        } elseif ($user->is_supervisor) {
            $targets = $targets->whereHas('model', function ($query) use ($user) {
                $query->where('type', 2)->whereIn('id', $user->getAllChildrenSales()->pluck('id')->toArray());
            });
        } elseif ($user->is_sales) {
            $targets = $targets->whereHas('model', function ($query) use ($user) {
                $query->whereIn('id', $user->getSalesFriends()->pluck('id')->toArray());
            });
        }
        //end tenancy

        $targets = $targets->selectRaw('id, model_type, model_id, target, value, IF(target = 0, 0, IF(value > target, 100, ((value / target) * 100) )) as percentage');


        if ($request->has('company_id') && $request->company_id != '') {
            $company_id = $request->company_id;
            $targets = $targets->whereHasMorph('model', [User::class], function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }

        if ($request->has('channel_id') && $request->channel_id != '') {
            $channel_id = $request->channel_id;
            $targets = $targets->whereHasMorph('model', [User::class], function ($query) use ($channel_id) {
                $query->where('channel_id', $channel_id);
            });
        }

        $targets = $targets->orderBy($type, 'desc')->limit($limit)->get();
        $data = [];
        $number = 1;
        foreach ($targets as $target) {
            $data[] = [
                'priority' => $number++,
                'model_type' => $target->model_type,
                'model_id' => $target->model_id,
                'target' => 'Rp ' . number_format($target->target, 0, ',', '.'),
                'value' => 'Rp ' . number_format($target->value, 0, ',', '.'),
                'percentage' => round($target->percentage) . '%',
                'model' => $target->model,
            ];
        }
        return response()->json($data);
    }

    public function brandCategories(Request $request)
    {
        $data = OrderDetail::join('product_units', 'product_units.id', '=', 'order_details.product_unit_id')
            ->join('products', 'products.id', '=', 'product_units.product_id')
            ->join('product_brands', 'product_brands.id', '=', 'products.product_brand_id')
            ->join('product_brand_categories', 'product_brand_categories.product_brand_id', '=', 'product_brands.id')
            ->join('brand_categories', 'brand_categories.id', '=', 'product_brand_categories.brand_category_id')
            ->whereHas('order', fn ($query) => $query->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::DOWN_PAYMENT, OrderPaymentStatus::OVERPAYMENT])->notCancelled()->notReturned());

        if ($request->has('company_id') && $request->company_id != '') $data = $data->where('order_details.company_id', $request->company_id);
        if ($request->has('channel_id') && $request->channel_id != '') $data = $data->whereHas('order', function ($q) use ($request) {
            $q->where('channel_id', $request->channel_id);
        });

        //tenancy
        $user = $request->user();
        if ($user->is_supervisor) {
            $data = $data->whereHas('order', function ($q) use ($user) {
                $q->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray());
            });
        } elseif ($user->is_sales) {
            $data = $data->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $data = $data->whereHas('order', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }
        //end tenancy

        if ($request->has('start_at') && $request->has('end_at')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
            $data = $data->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('deal_at', [$startDate, $endDate]);
            });
        }

        $data = $data->selectRaw('brand_categories.id, brand_categories.name, sum(order_details.total_price) as total_price')
            ->groupBy('brand_categories.id', 'brand_categories.name')
            ->orderBy('total_price', 'desc')
            ->get()->keyBy('id');

        $result = [];
        $brandCategories = \Illuminate\Support\Facades\DB::table('brand_categories')->selectRaw('id, name, IFNULL(NULL, 0) as total_price')->get();
        foreach ($brandCategories as $bc) {
            if (isset($data[$bc->id])) {
                $result[] = [
                    'id' => $data[$bc->id]->id,
                    'name' => $data[$bc->id]->name,
                    'total_price' => 'Rp ' . number_format($data[$bc->id]->total_price, 0, ',', '.'),
                ];
            } else {
                $result[] = [
                    'id' => $bc->id,
                    'name' => $bc->name,
                    'total_price' => 'Rp ' . number_format($bc->total_price, 0, ',', '.'),
                ];
            }
        }

        return response()->json($result);
    }

    public function detailBrandCategories(Request $request, $brandCategoryId)
    {
        $data = OrderDetail::join('product_units', 'product_units.id', '=', 'order_details.product_unit_id')
            ->join('products', 'products.id', '=', 'product_units.product_id')
            ->join('product_brands', 'product_brands.id', '=', 'products.product_brand_id')
            ->join('product_brand_categories', 'product_brand_categories.product_brand_id', '=', 'product_brands.id')
            ->join('brand_categories', 'brand_categories.id', '=', 'product_brand_categories.brand_category_id')
            ->whereHas('order', fn ($query) => $query->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::DOWN_PAYMENT, OrderPaymentStatus::OVERPAYMENT])->notCancelled()->notReturned())
            ->where('brand_categories.id', $brandCategoryId);

        if ($request->has('company_id') && $request->company_id != '') $data = $data->where('order_details.company_id', $request->company_id);
        if ($request->has('channel_id') && $request->channel_id != '') $data = $data->whereHas('order', function ($q) use ($request) {
            $q->where('channel_id', $request->channel_id);
        });

        //tenancy
        $user = $request->user();
        if ($user->is_supervisor) {
            $data = $data->whereHas('order', function ($q) use ($user) {
                $q->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray());
            });
        } elseif ($user->is_sales) {
            $data = $data->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $data = $data->whereHas('order', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }
        //end tenancy

        if ($request->has('start_at') && $request->has('end_at')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
            $data = $data->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('deal_at', [$startDate, $endDate]);
            });
        }

        $data = $data
            ->selectRaw('product_brands.id, product_brands.name, sum(order_details.total_price) as total_price')
            ->groupBy('product_brands.id', 'product_brands.name')
            ->orderBy('total_price', 'desc')
            ->get();

        $productBrandIds = $data->pluck('id');
        $dataEstimation = Activity::join('activity_product_brand', 'activity_product_brand.activity_id', '=', 'activities.id')
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
                'total_price' => 'Rp ' . number_format($d['total_price'], 0, ',', '.'),
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
        $data = Order::whereNotNull('interior_design_id')->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::OVERPAYMENT, OrderPaymentStatus::DOWN_PAYMENT])->notCancelled()->notReturned()->selectRaw('IFNULL(sum(total_price), 0) as total');

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
        $data['total'] = 'Rp ' . number_format($data->total, 0, ',', '.');
        return response()->json($data);
    }

    public function interiorDesignDetail(Request $request, $interiorDesignId = null)
    {
        $data = Order::whereNotNull('interior_design_id')->whereIn('payment_status', [OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::OVERPAYMENT, OrderPaymentStatus::DOWN_PAYMENT])->notCancelled()->notReturned();

        if ($interiorDesignId) {
            $data = $data->join('channels', 'channels.id', '=', 'orders.channel_id')->join('users', 'users.id', '=', 'orders.user_id')->where('interior_design_id', $interiorDesignId)->select('orders.deal_at as date', 'channels.name as channel', 'users.name as sales', 'orders.total_price as total')->orderBy('orders.deal_at', 'desc');
        } else {
            $data = $data->with(['interiorDesign' => function ($query) {
                return $query->select('id', 'name');
            }])->selectRaw('interior_design_id, sum(total_price) as total')->groupBy('interior_design_id')->orderBy('total', 'desc');
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
            $d['total'] = 'Rp ' . number_format($d->total, 0, ',', '.');
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
        $orders = Order::with(['customer' => function ($q) {
            $q->select('id', 'first_name', 'last_name');
        }, 'activity' => function ($q) {
            $q->select('id', 'order_id');
        }])->whereIn('user_id', $sales_id)->whereHas('orderPayments', fn($q) => $q->where('status', \App\Enums\PaymentStatus::APPROVED))->whereIn('payment_status', [OrderPaymentStatus::DOWN_PAYMENT, OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::OVERPAYMENT])->notCancelled()->notReturned();
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

        return response()->json(rupiah($cartDemands));
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
            $o['order']['total_price_format'] = rupiah($o->order->total_price);
            $data[] = $o;
        }

        return response()->json($data);
    }

    public function media()
    {
        $medias = \Illuminate\Support\Facades\DB::table('media')->orderBy('id', 'desc')->limit(10)->get();
        return response()->json($medias);
    }
}
