<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\OrderApprovalStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStockStatus;
use App\Enums\OrderDetailStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyOrderRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Channel;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Address;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\PaymentCategory;
use App\Models\Lead;
use App\Models\Activity;
use App\Models\CartDemand;
use App\Models\CompanyData;
use App\Models\PaymentType;
use App\Models\Payment;
use App\Services\OrderService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class OrderControllerBackup extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Order::with(['user', 'customer', 'channel', 'orderPayments', 'interiorDesign'])
                ->select(sprintf('%s.*', (new Order)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'order_show';
                $editGate      = 'order_edit';
                $payment      = true;
                $crudRoutePart = 'orders';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'payment',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->addColumn('customer_first_name', function ($row) {
                return $row->customer ? $row->customer->first_name : '';
            });

            $table->addColumn('channel_name', function ($row) {
                return $row->channel ? $row->channel->name : '';
            });
            $table->editColumn('interior_design_id', function ($row) {
                return $row->interiorDesign ? $row->interiorDesign->name : "";
            });
            $table->editColumn('invoice_number', function ($row) {
                return $row->invoice_number ? $row->invoice_number : "";
            });

            $table->editColumn('status', function ($row) {
                return $row->status?->description ?? '';
            });

            $table->editColumn('payment_status', function ($row) {
                return $row->payment_status?->description ?? '';
            });
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? date('d-m-Y H:i:s', strtotime($row->created_at)) : "";
            });
            $table->editColumn('expected_shipping_datetime', function ($row) {
                return $row->expected_shipping_datetime ? date('d-m-Y H:i:s', strtotime($row->expected_shipping_datetime)) : "";
            });
            $table->editColumn('quotation_valid_until_datetime', function ($row) {
                return $row->quotation_valid_until_datetime ? date('d-m-Y H:i:s', strtotime($row->quotation_valid_until_datetime)) : "";
            });

            $table->editColumn('shipment_status', function ($row) {
                return $row->shipment_status?->description ?? '';
            });

            $table->editColumn('total_price', function ($row) {
                return $row->total_price ? rupiah($row->total_price) : "";
            });

            $table->addColumn('total_outstanding', function ($row) {
                $totalApprovedPayments = OrderService::getTotalPaymentAmount($row, PaymentStatus::APPROVED());
                return rupiah($row->total_price - $totalApprovedPayments);
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'customer', 'channel']);

            return $table->make(true);
        }

        $channels = Channel::tenanted()->get();

        return view('admin.orders.index', compact('channels'));
    }

    public function create()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $channels = Channel::tenanted()->pluck('name', 'id');
        return view('admin.orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expected_price' => 'required|numeric'
        ]);
        $items = [];
        for ($i = 0; $i < count($request->products); $i++) {
            $items[] = [
                'id' => $request->products[$i],
                'quantity' => $request->qty[$i],
            ];
        }

        $user = User::findOrFail($request->user_id);
        if (!$user->channel) {
            return redirect()->back()->withInput()->with('message', 'Sales don\'t have a channel');
        }

        $lead = Lead::findOrFail($request->lead_id);
        $address = $lead->customer->defaultCustomerAddress ? Address::findOrFail($lead->customer->defaultCustomerAddress->id)->toRecord() : Address::findOrFail($lead->customer->customerAddresses->first()->id)->toRecord();

        $order = new Order;
        $order->note                           = $request->note ?? null;
        $order->lead_id                        = $request->lead_id;
        $order->shipping_fee                   = filterPrice($request->shipping_fee) ?? 0;
        $order->packing_fee                    = filterPrice($request->packing_fee) ?? 0;
        $order->additional_discount            = filterPrice($request->additional_discount) ?? 0;
        $order->expected_shipping_datetime     = $request->expected_shipping_datetime ?? null;
        $order->quotation_valid_until_datetime = $request->quotation_valid_until_datetime ?? now()->addMinutes(config('quotation_valid_for_minutes'));
        $order->user_id                        = $request->user_id;
        $order->customer_id                    = $lead->customer_id;
        $order->channel_id                     = $user->channel->id;
        $order->company_id                     = $user->company_id;
        $order->total_discount                 = 0;
        $order->status                         = OrderStatus::QUOTATION();
        $order->stock_status                   = OrderStockStatus::INDENT();
        $order->approval_status                = OrderApprovalStatus::NOT_REQUIRED();
        $order->payment_status                 = OrderPaymentStatus::NONE();
        $order->expected_price                 = $request->expected_price ?? null;

        $record['billing_address']  = $address;
        $record['shipping_address'] = $address;
        $record['tax_invoice']      = null;
        $order->records             = $record;

        // Starts by grabbing all the product unit model
        $items = collect($items);
        $units = Product::whereIn('id', $items->pluck('id'))
            ->with(['product', 'colour', 'covering'])
            ->get()
            ->keyBy('id');

        $company_id = $order->company_id;
        $order_details = $items->map(function ($data) use ($units, $company_id) {

            /** @var Product $product_unit */
            $product_unit = $units[$data['id']];

            $order_detail             = new OrderDetail();
            $order_detail->status     = OrderDetailStatus::NOT_FULFILLED();
            $order_detail->company_id = $company_id;

            // We do not bother with stock fulfilment and discount
            // calculation yet at this stage
            $order_detail->records         = [
                'product_unit' => $product_unit->toRecord(),
                'product'      => $product_unit->product->toRecord(),
                'images'       => $product_unit->product->version->getRecordImages()
            ];
            $order_detail->quantity        = (int)$data['quantity'];
            $order_detail->product_unit_id = $product_unit->id;
            $order_detail->unit_price      = $product_unit->price;
            $order_detail->total_discount  = 0;
            $order_detail->total_price     = $product_unit->price * $data['quantity'];

            return $order_detail;
        });

        $order->order_details = $order_details;
        $order->total_price   = $order_details->sum(fn (OrderDetail $detail) => $detail->total_price);

        if ($order->additional_discount != 0) {

            $order->total_price -= $order->additional_discount;

            $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
            $order->additional_discount_ratio = app(OrderService::class)->calculateOrderAdditionalDiscountRatio($order);
        }

        if ($order->shipping_fee) {
            $order->total_price += $order->shipping_fee;
        }

        if ($order->packing_fee) {
            $order->total_price += $order->packing_fee;
        }

        if ($order->expected_price) {
            if ($order->total_price != $order->expected_price) {
                return redirect()->back()->withInput()->with('message', 'The total price is not the same as the expected price.');
                // throw new ExpectedOrderPriceMismatchException();
            }
        }

        $order = DB::transaction(function () use ($order) {
            $details = $order->order_details;
            unset($order->order_details);
            unset($order->discount);

            collect($details)->each(function (OrderDetail $detail) {
                unset($detail->discount);
                unset($detail->discount_id);
            });

            $order->save();
            $order->order_details()->saveMany($details);

            return $order;
        });

        Activity::createForOrder($order);

        $order->invoice_number = CompanyData::getInvoiceNumber($order->company_id, $order->created_at ?? now());
        $order->save();

        if ($order->approval_status == OrderApprovalStatus::WAITING_APPROVAL()) {
            $type = \App\Enums\NotificationType::DiscountApproval();
            $link = config("notification-link.{$type->key}") ?? 'no-link';

            $sales = $user;
            $getSupervisor = User::where('id', $sales->supervisor_id)->first();

            if ($getSupervisor) {
                $user = $getSupervisor;

                if ($user->supervisor_type_id == 1) {
                    // store leader
                    $approvalLimit = $user->supervisorType->discount_approval_limit_percentage;

                    if ($order->additional_discount_ratio > $approvalLimit) {
                        // get BUM
                        $user = User::where('id', $getSupervisor->supervisor_id)->first();
                    }
                }

                if (isset($user->notificationDevices) && count($user->notificationDevices) > 0) {
                    \App\Events\SendExpoNotification::dispatch([
                        'receipents' => $user,
                        'badge_for' => $user,
                        'title' => $sales->name . " from " . $sales->channel->name . " Has Request a New Approval",
                        'body' => $sales->name .  ' has request a new discount approval of ' . number_format($order->additional_discount) . ' on invoice ' . $order->invoice_number,
                        'code' => $type->key,
                        'link' => $link,
                    ]);
                }
            }
        }

        return redirect()->route('admin.orders.index')->with('message', 'Order #' . $order->invoice_number . ' created successfully.');
    }

    public function edit(Order $order)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channels        = Channel::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $status          = OrderStatus::getInstances();
        $order->load('user', 'customer', 'channel');

        return view('admin.orders.edit', compact('status', 'channels', 'order'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());

        return redirect()->route('admin.orders.index');
    }

    public function show(Order $order)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $order->load('user', 'customer', 'address', 'channel', 'tax_invoice', 'orderOrderDetails', 'orderShipments', 'orderPayments');
        $order->load('user', 'customer', 'address', 'channel', 'orderOrderDetails', 'orderShipments', 'orderPayments', 'cartDemand');

        $total_amount = OrderService::getTotalPaymentAmount($order);

        return view('admin.orders.show', compact('order', 'total_amount'));
    }

    public function destroy(Order $order)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrderRequest $request)
    {
        Order::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function createProduct(StoreProductRequest $request, $cartDemandId)
    {
        $cartDemand = CartDemand::findOrFail($cartDemandId);
        $tmp_product_unit_id = $request->input('tmp_product_unit_id');
        $items = $cartDemand->items;
        $item = collect($items)->filter(function ($cart) use ($tmp_product_unit_id) {
            return $cart['id'] == $tmp_product_unit_id;
        })->toArray();
        $item = array_values($item)[0];

        if (count($item) <= 0) {
            return redirect()->back()->with('message', 'Product Unit not found');
        }

        $remainingItems = collect($items)->filter(function ($cart) use ($tmp_product_unit_id) {
            return $cart['id'] != $tmp_product_unit_id;
        })->toArray();
        $remainingItems = array_values($remainingItems);

        $product = Product::create($request->validated());

        // insert into order detail
        $detail = new OrderDetail();
        $detail->status = OrderDetailStatus::NOT_FULFILLED();
        $detail->order_id = $cartDemand->order->id;
        $detail->company_id = $cartDemand->order->company_id;
        $detail->quantity = (int)$item['quantity'];
        $detail->product_unit_id = $product->id;
        $detail->unit_price = $product->price;
        $detail->total_discount = 0;
        $detail->total_price = $product->price * (int)$item['quantity'];
        $detail->records = [
            'product_unit' => $product->toRecord(),
            'product'      => $product->product->toRecord(),
            'images'       => $product->product->version->getRecordImages()
        ];
        $detail->save();

        //check if item have image, move to order detail by its id
        if (isset($item['image']) && $item['image'] != '') {
            $itemImage = basename($item['image']);
            $media = \App\Models\Media::where('file_name', basename($itemImage))->first();
            if ($media) {
                $media->update(['model_type' => OrderDetail::class, 'model_id' => $detail->id]);
            }
        }

        // check if items is empty, delete cart demand.
        if (count($remainingItems) <= 0) {
            $cartDemand->delete();
        } else {
            // delete item by id in cart demand and update total price
            $total_price = collect($remainingItems)->sum(function ($i) {
                return $i['price'] * $i['quantity'];
            });
            $cartDemand->update(['items' => $remainingItems, 'total_price' => $total_price]);
        }

        return redirect()->back()->with('message', 'Product Unit created successfully');
    }

    public function getproduct(Request $request)
    {
        $data = [];
        if ($request->has('q') && $request->input('q') != '') {
            $data = Product::whereActive()->select("id", "name");
            if ($request->has('company_id') && $request->input('company_id') != '' && $request->input('company_id') != null && $request->input('company_id') != 'null') {
                $data = $data->where('company_id', $request->input('company_id'));
            }

            if ($request->has('product_brand') && $request->input('product_brand') != '' && $request->input('product_brand') != null && $request->input('product_brand') != 'null') {
                $product_brand_id = $request->input('product_brand');
                $data = $data->whereHas('product.brand', function ($q) use ($product_brand_id) {
                    $q->where('id', $product_brand_id);
                });
            }

            $search = $request->q;
            $data = $data->where('name', 'LIKE', "%$search%")->get();
        }

        return response()->json($data);
    }

    public function getProductBrand(Request $request)
    {
        $data = ProductBrand::select("id", "name")->where('company_id', $request->company_id)->get();

        return response()->json($data);
    }

    public function getsales()
    {
        return User::whereIsSales()->select("id", "name")->where('name', 'LIKE', "%" . $_GET['q'] . "%")->get();
    }

    public function detailproduct($id)
    {
        return Product::select('price')->where('id', $id)->first()->price;
    }

    public function getPaymentType($payment_category_id)
    {
        $t = PaymentType::where('payment_category_id', $payment_category_id)->pluck('name', 'id');
        return $t;
    }

    public function getLeads($sales_id)
    {
        $t = Lead::where('user_id', $sales_id)->pluck('label', 'id');
        return $t;
    }

    public function payment(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($request->isMethod('post')) {
            $request->validate([
                'payment_category' => 'required',
                'payment_type_id' => 'required',
                'amount' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:5120'
            ]);
            if ($request->amount == 0 || $request->amount == '0') {
                return redirect()->back()->withInput()->with('message', 'Amount must be more than 0');
            }

            $paymentType = PaymentType::findOrFail($request->payment_type_id);

            if ($order->company_id != $paymentType->company_id) {
                return redirect()->back()->withInput()->with('message', 'Invalid payment type for this order');
            }

            if ($order->approval_status->is(OrderApprovalStatus::WAITING_APPROVAL)) {
                return redirect()->back()->withInput()->with('message', 'Unable to make payment, order awaiting supervisor approval.');
            }

            DB::transaction(function () use ($request, $order) {
                $payment = Payment::make(
                    [
                        'amount'          => filterPrice($request->amount),
                        'reference'       => $request->reference,
                        'status'          => PaymentStatus::PENDING(),
                        'payment_type_id' => $request->payment_type_id,
                        'added_by_id'     => auth()->id(),
                        'order_id'        => $order->id,
                        'company_id'      => $order->company_id,
                    ]
                );
                $payment->save();
                // $payment->addMedia($request->file('image'))->toMediaCollection(Payment::PROOF_COLLECTION, 's3-private');
            });
            return redirect()->back()->with('message', 'Order #' . $order->invoice_number . ' successfully paid.');
        }
        $payment_categories = PaymentCategory::where('company_id', $order->company_id)->pluck('name', 'id');
        return view('admin.orders.payment', compact('order', 'payment_categories'));
    }

    public function ajax_upload_payment(Request $request, $id)
    {
    }
}
