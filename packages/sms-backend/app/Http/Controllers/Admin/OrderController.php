<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\OrderApprovalStatus;
use App\Enums\OrderDetailStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Channel;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\PaymentCategory;
use App\Models\Lead;
use App\Models\CartDemand;
use App\Models\PaymentType;
use App\Models\Payment;
use App\Pipes\Order\Admin\AddAdditionalDiscount;
use App\Pipes\Order\AddAdditionalFees;
use App\Pipes\Order\Admin\FillOrderAttributes;
use App\Pipes\Order\Admin\MakeOrderLines;
use App\Pipes\Order\Admin\SaveOrder;
use App\Pipes\Order\Admin\SetExpectedOrderPrice;
use App\Pipes\Order\ApplyDiscount;
use App\Pipes\Order\CheckExpectedOrderPrice;
use App\Pipes\Order\CreateActivity;
use App\Pipes\Order\ProcessInvoiceNumber;
use App\Pipes\Order\SendDiscountApprovalNotification;
use App\Pipes\Order\UpdateDiscountUse;
use Illuminate\Pipeline\Pipeline;
use App\Services\OrderService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Order::with(['user', 'customer', 'channel', 'orderPayments'])
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
            // $table->editColumn('interior_design_id', function ($row) {
            //     return $row->interiorDesign ? $row->interiorDesign->name : "";
            // });
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

            // $table->editColumn('shipment_status', function ($row) {
            //     return $row->shipment_status?->description ?? '';
            // });

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

        $channels = Channel::get();

        return view('admin.orders.index', compact('channels'));
    }

    public function create()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $channels = Channel::tenanted()->pluck('name', 'id');
        return view('admin.orders.create');
    }

    public function preview(StoreOrderRequest $request)
    {
        $items = [];
        for ($i = 0; $i < count($request->products); $i++) {
            $items[] = [
                'id' => intval($request->products[$i]),
                'quantity' => intval($request->qty[$i]),
            ];
        }

        $data = [
            // 'interior_design_id' => $request->interior_design_id,
            // 'discount_id' => (int)$request->discount_id,
            'expected_price' => (int)$request->expected_price,
            'lead_id' => (int)$request->lead_id,
            'note' => $request->note,
            'packing_fee' => (int)$request->packing_fee,
            'shipping_fee' => (int)$request->shipping_fee,
            'discount_type' => (int)$request->discount_type ?? 0,
            'additional_discount' => (int)$request->additional_discount,
            'expected_shipping_datetime' => $request->expected_shipping_datetime,
            'quotation_valid_until_datetime' => $request->expected_shipping_datetime,
        ];

        if (isset($request->discount_ids) && count($request->discount_ids) > 0) $data['discount_ids'] = $request->discount_ids;

        $data = array_merge(['items' => $items], $data);
        unset($data['products']);
        unset($data['qty']);
        $data = Order::make(['raw_source' => $data]);

        $order = app(Pipeline::class)
            ->send($data)
            ->through(
                [
                    FillOrderAttributes::class,
                    MakeOrderLines::class,
                    ApplyDiscount::class,
                    AddAdditionalDiscount::class,
                    AddAdditionalFees::class,
                    SetExpectedOrderPrice::class,
                ]
            )
            ->thenReturn();
        return response()->json($order->refresh());
    }

    public function store(StoreOrderRequest $request)
    {
        if ((int)$request->expected_price < 0) return redirect()->back()->withInput()->withStatus('Total Price min 0');
        $items = [];
        for ($i = 0; $i < count($request->products); $i++) {
            $items[] = [
                'id' => intval($request->products[$i]),
                'quantity' => intval($request->qty[$i]),
            ];
        }

        $data = [
            // 'interior_design_id' => $request->interior_design_id,
            // 'discount_id' => (int)$request->discount_id,
            'expected_price' => (int)$request->expected_price,
            'lead_id' => (int)$request->lead_id,
            'note' => $request->note,
            'packing_fee' => (int)$request->packing_fee,
            'shipping_fee' => (int)$request->shipping_fee,
            'discount_type' => (int)$request->discount_type ?? 0,
            'additional_discount' => (int)$request->additional_discount,
            'expected_shipping_datetime' => $request->expected_shipping_datetime,
            'quotation_valid_until_datetime' => $request->expected_shipping_datetime,
        ];

        if (isset($request->discount_ids) && count($request->discount_ids) > 0) $data['discount_ids'] = $request->discount_ids;

        $data = array_merge(['items' => $items], $data);
        unset($data['products']);
        unset($data['qty']);

        $data = Order::make(['raw_source' => $data]);

        $order = app(Pipeline::class)
            ->send($data)
            ->through(
                [
                    // transfer attributes from raw_source to properties
                    FillOrderAttributes::class,

                    // // make order lines
                    MakeOrderLines::class,

                    // // apply discount
                    ApplyDiscount::class,
                    AddAdditionalDiscount::class,

                    // // add shipping and packing fee
                    AddAdditionalFees::class,

                    // // validate whether the final price match with the expected price
                    // // may differ if suddenly discount is expired or modified
                    CheckExpectedOrderPrice::class,

                    // // save the order and order details
                    SaveOrder::class,

                    // // record discount use (if used)
                    UpdateDiscountUse::class,

                    // // create activity against this order for the sales user
                    CreateActivity::class,

                    // // generate unique invoice number
                    ProcessInvoiceNumber::class,

                    // // generate unique invoice number
                    SendDiscountApprovalNotification::class,

                    // calculate stock per product unit
                    // CalculateStock::class
                ]
            )
            ->thenReturn();

        return redirect()->route('admin.orders.index')->with('message', 'Order #' . $order->invoice_number . ' created successfully.');
    }

    public function edit(Order $order)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $channels        = Channel::get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $status          = OrderStatus::getInstances();
        $order->load('user', 'customer', 'channel');
        return view('admin.orders.edit', compact('status', 'channels', 'order'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $reevaluateOrder = date('m', strtotime($request->created_at)) == date('m', strtotime($order->created_at));
        $order->update($request->validated());
        if (!$reevaluateOrder) {
            $date = date('Y-m-d', strtotime($request->created_at));
            $report = \App\Models\Report::where('reportable_type', 'user')->where('reportable_id', $order->user->id)->whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->first();
            if ($report) app(\App\Services\ReportService::class)->reevaluateReport($report);
        }

        return redirect()->route('admin.orders.index');
    }

    public function show(Order $order)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $order->load('user', 'customer', 'address', 'channel', 'tax_invoice', 'orderOrderDetails', 'orderShipments', 'orderPayments');
        $order->load('user', 'customer', 'address', 'channel', 'orderOrderDetails', 'orderPayments');

        $total_amount = OrderService::getTotalPaymentAmount($order);

        // $showCreateSOButton = OrderService::validateCreateManualSO($order);
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

    public function updateProduct(Request $request, $cartDemandId)
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

        $product = Product::findOrFail($request->product_unit_id);

        // insert into order detail
        $detail = new OrderDetail();
        $detail->status = OrderDetailStatus::NOT_FULFILLED();
        $detail->order_id = $cartDemand->order->id;
        // $detail->company_id = $cartDemand->order->company_id;
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

        //update cart demand dulu setelah hapus item biar pas CalculateCartDemand, item nya ga ikut keitung
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

        app(Pipeline::class)
            ->send($cartDemand->order)
            ->through(
                [
                    \App\Pipes\Order\Update\UpdateOrderLines::class,
                    \App\Pipes\Order\Admin\CartDemand\UpdateApplyDiscount::class,
                    \App\Pipes\Order\CalculateCartDemand::class,
                    \App\Pipes\Order\Admin\CartDemand\AddAdditionalDiscount::class,
                    \App\Pipes\Order\AddAdditionalFees::class,
                    \App\Pipes\Order\Admin\CartDemand\SaveOrder::class,
                ]
            )
            ->thenReturn();
        return redirect()->back()->with('message', 'Product Unit updated successfully');
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
        // $detail->company_id = $cartDemand->order->company_id;
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
            // if ($request->has('company_id') && $request->input('company_id') != '' && $request->input('company_id') != null && $request->input('company_id') != 'null') {
            //     $data = $data->where('company_id', $request->input('company_id'));
            // }

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

    public function getProductBrand()
    {
        // $data = ProductBrand::select("id", "name")->where('company_id', $request->company_id)->get();
        $data = ProductBrand::select("id", "name")->tenanted()->get();

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
        $leads = Lead::where('user_id', $sales_id)->has('customer.customerAddresses')->orderBy('id', 'desc')->pluck('label', 'id');
        return $leads;
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

            // if ($order->company_id != $paymentType->company_id) {
            //     return redirect()->back()->withInput()->with('message', 'Invalid payment type for this order');
            // }

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
                        // 'company_id'      => $order->company_id,
                    ]
                );
                $payment->save();
                $payment->addMedia($request->file('image'))->toMediaCollection(Payment::PROOF_COLLECTION, 's3-private');
            });
            return redirect()->back()->with('message', 'Order #' . $order->invoice_number . ' successfully paid.');
        }
        // $payment_categories = PaymentCategory::where('company_id', $order->company_id)->pluck('name', 'id');
        $payment_categories = PaymentCategory::pluck('name', 'id');
        return view('admin.orders.payment', compact('order', 'payment_categories'));
    }

    public function ajax_upload_payment(Request $request, $id)
    {
    }
}
