<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Activity;
use App\Models\ActivityProductBrand;
use App\Models\Channel;
use App\Models\ProductBrand;
use App\Models\BrandCategory;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SalesEstimationController extends Controller
{
    use MediaUploadingTrait;
    use CsvImportTrait;

    // region sub report
    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_estimation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            // $query = Activity::with(['customer', 'user', 'brands']);
            $query = Activity::join('customers', 'customers.id', '=', 'activities.customer_id')
                ->join('users', 'users.id', '=', 'activities.user_id')
                ->join('activity_brand_values', 'activity_brand_values.activity_id', '=', 'activities.id')
                ->join('product_brands', 'product_brands.id', '=', 'activity_brand_values.product_brand_id')
                ->selectRaw('activities.id, activity_brand_values.lead_id, users.name as sales, customers.first_name as customer, product_brands.id as brand_id, product_brands.name as brand, sum(activity_brand_values.estimated_value) as estimated_value, sum(activity_brand_values.order_value) as order_value')
                ->groupBy('activity_brand_values.lead_id', 'activity_brand_values.product_brand_id')
                ->get();
                dd($query);
            $table = Datatables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->editColumn('user_id', function ($row) {
                return $row->user?->name ?? '-';
            });
            $table->editColumn('customer_id', function ($row) {
                return $row->customer?->first_name ?? "-";
            });
            $table->addColumn('phone', function ($row) {
                return $row->customer?->phone ?? '-';
            });
            $table->addColumn('brand', function ($row) {
                if (count($row->brands) > 0) {
                    $data = '<table class="table table-bordered">';
                    foreach ($row->brands as $brand) {
                        $data .= '<tr>';
                        $data .= '<td>' . $brand->name . '</td>';
                        $data .= '</tr>';
                    }
                    $data .= '</table>';
                    return $data ?? '-';
                }
                return '<center>-</center>';
            });
            $table->addColumn('estimated', function ($row) {
                if (count($row->brands) > 0) {
                    $data = '<table class="table table-bordered">';
                    foreach ($row->brands as $brand) {
                        $data .= '<tr>';
                        $data .= '<td>' . $brand->pivot->estimated_value . '</td>';
                        $data .= '</tr>';
                    }
                    $data .= '</table>';
                    return $data ?? '-';
                }
                return '<center>-</center>';
            });
            $table->addColumn('order_value', function ($row) {
                if (count($row->brands) > 0) {
                    $data = '<table class="table table-bordered">';
                    foreach ($row->brands as $brand) {
                        $data .= '<tr>';
                        $data .= '<td>' . $brand->pivot->order_value . '</td>';
                        $data .= '</tr>';
                    }
                    $data .= '</table>';
                    return $data ?? '-';
                }
                return '<center>-</center>';
            });

            $table->rawColumns(['brand', 'estimated', 'order_value', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.salesEstimation.index');
    }

    public function indexBackup(Request $request)
    {
        abort_if(Gate::denies('sales_estimation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $activities = Activity::limit(30)->orderByDesc('id')->get();

        $channels = Channel::get();
        $productBrands = ProductBrand::get();
        $brandCategories = BrandCategory::get();

        if ($request->ajax() || false) {

            $validator = Validator::make($request->all(), [
                'start_date' => 'date',
                'end_date'   => 'date',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!empty($errors->first('start_date'))) {
                    $request->request->remove('start_date');
                    $request->request->remove('end_date');
                }
                if (!empty($errors->first('end_date'))) {
                    $request->request->remove('end_date');
                }
            }
            /*
            #$query = Activity::with(['channel','lead', 'customer'])->select(sprintf('%s.*', (new Activity)->table));
            */
            DB::statement(DB::raw('set @rownum=0'));

            $query = Activity::whereNotNull('estimated_value')
                ->join('users', 'users.id', '=', 'activities.user_id')
                ->join('leads', 'leads.id', '=', 'activities.lead_id')
                ->join('lead_categories', 'lead_categories.id', '=', 'leads.lead_category_id')
                ->join('customers', 'customers.id', '=', 'activities.customer_id')
                ->leftJoin('addresses',  function ($join) {
                    $join->on('addresses.id', '=', 'customers.default_address_id');
                })
                ->join('channels', 'channels.id', '=', 'activities.channel_id')
                ->select(
                    DB::raw('@rownum  := @rownum  + 1 AS no'),
                    'activities.id AS activity_id',
                    'users.name AS sales_name',
                    DB::Raw("CONCAT_WS(' ', customers.first_name, customers.last_name) AS customer_name"),
                    'addresses.city AS customer_address',
                    'customers.phone AS customer_phone',
                    'lead_categories.name AS customer_source',
                    'activities.follow_up_datetime AS customer_start_date',
                    'activities.feedback AS customer_remarks',
                    'activities.estimated_value AS customer_estimated_value',
                    'activities.status AS activity_status'
                );

            if ($request->has('start_date')) {
                if ($request->has('end_date')) {
                    $query->whereDate('activities.follow_up_datetime', '>=', $request->input('start_date'));
                    $query->whereDate('activities.follow_up_datetime', '<=', $request->input('end_date'));
                } else {
                    $query->whereDate('activities.follow_up_datetime', '=', $request->input('start_date'));
                }
            }

            if ($request->channel) {
                $query->whereIn('channels.id', $request->channel);
            }

            if ($request->productBrand) {
                $query->whereHas('activityProductBrands', function ($q) use ($request) {
                    $q->where('product_brand_id', $request->productBrand);
                });
            }

            if ($request->brandCategory) {
                $query->whereHas('activityProductBrands.productBrandCategories', function ($q) use ($request) {
                    $q->whereIn('brand_category_id', $request->brandCategory);
                });
            }

            $table = Datatables::of($query);

            foreach ($productBrands as $productBrand) {
                $table->addColumn('productBrandId_' . $productBrand->id, function ($row) use ($query, $productBrand) {
                    if (ActivityProductBrand::where('activity_id', $row->activity_id)->where('product_brand_id', $productBrand->id)->first()) {
                        return $row->customer_estimated_value;
                    } else {
                        return null;
                    }
                });
            }

            $table->addColumn('activity_status', function ($row) {
                return \App\Enums\ActivityStatus::fromValue($row->activity_status)->description ?? '';
            });

            $table->filterColumn('sales_name', function ($query, $keyword) {
                $query->where('users.name', 'LIKE', "%{$keyword}%");
            });

            return $table->make(true);
        }
        return view('admin.salesEstimation.index', compact('channels', 'productBrands', 'brandCategories', 'activities'));
    }
}
