<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPaymentRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\PaymentType;
use App\Models\User;
use App\Services\OrderService;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Payment::with(['payment_type.payment_category', 'added_by', 'approved_by', 'order.customer'])->select(sprintf('%s.*', (new Payment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'payment_show';
                $editGate      = 'payment_edit';
                $deleteGate    = 'payment_delete';
                $crudRoutePart = 'payments';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?: "";
            });
            $table->addColumn('customer', function ($row) {
                return $row?->order?->customer?->fullName ?? '';
            });
            $table->editColumn('amount', function ($row) {
                return helper()->formatRupiah($row->amount) ?: "";
            });
            $table->addColumn('payment_category_name', function ($row) {
                return $row?->payment_type?->payment_category?->name ?? '';
            });
            $table->addColumn('payment_type_name', function ($row) {
                return $row?->payment_type?->name ?? '';
            });
            $table->editColumn('reference', function ($row) {
                return $row->reference ?: "";
            });
            $table->editColumn('created_at', function ($row) {
                return date('d-m-Y H:i', strtotime($row->created_at));
            });
            $table->addColumn('added_by_name', function ($row) {
                return $row->added_by->name ?? '';
            });

            $table->addColumn('approved_by_name', function ($row) {
                return $row->approved_by->name ?? '';
            });

            $table->editColumn('proof', function ($row) {
                if (!$row->proof) {
                    return '';
                }

                $links = [];

                foreach ($row->proof as $media) {
                    $links[] = '<a href="' . $media->getUrl('proof') . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });
            $table->editColumn('status', function ($row) {
                return $row->status?->description ?? '';
            });
            $table->editColumn('reason', function ($row) {
                return $row->reason ?: "";
            });
            $table->addColumn('invoice_number', function ($row) {
                return $row->order->invoice_number ?? '';
            });

            $table->rawColumns(['actions', 'placeholder', 'payment_type', 'added_by', 'approved_by', 'proof']);

            return $table->make(true);
        }

        $payment_categories = PaymentCategory::get();

        return view('admin.payments.index', compact('payment_categories'));
    }

    public function create()
    {
        abort_if(Gate::denies('payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment_types = PaymentType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.payments.create', compact('payment_types'));
    }

    public function store(StorePaymentRequest $request)
    {
        $order = Order::where('invoice_number', $request->get('invoice_number'))->firstOrFail();
        $data  = array_merge(
            $request->validated(),
            [
                'added_by_id' => user()->id,
                'order_id'    => $order->id,
                'company_id'  => $order->company_id,
            ]
        );

        $payment = Payment::create($data);

        foreach ($request->input('proof', []) as $file) {
            $payment->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('proof');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $payment->id]);
        }

        $order->refreshPaymentStatus();

        return redirect()->route('admin.payments.index');
    }

    public function edit(Payment $payment)
    {
        abort_if(Gate::denies('payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $approved_bies = User::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $payment->load('approved_by');

        return view('admin.payments.edit', compact('approved_bies', 'payment'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        if (!in_array($request->status, [0, 2]) && $payment->order->payment_status->is(OrderPaymentStatus::OVERPAYMENT)) {
            return redirect()->route('admin.payments.index')->with('message', "The order has passed payment. Can't add more payments!");
        }

        $payment->update($request->validated());

        if (count($payment->proof) > 0) {
            foreach ($payment->proof as $media) {
                if (!in_array($media->file_name, $request->input('proof', []))) {
                    $media->delete();
                }
            }
        }

        $media = $payment->proof->pluck('file_name')->toArray();

        foreach ($request->input('proof', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $payment->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('proof');
            }
        }

        $payment->order->refreshPaymentStatus();
        $message = 'Payment updated successfully';

        /**
         * 1. create sales order sekaligus sales invoice ketika pembayaran >= 50%
         * 2. setelah pembayaran >= 50%, setiap ada pembayaran create sales invoice
         */
        // $order = $payment->order;
        // if ($payment->status->isNot(PaymentStatus::REJECTED)) {
        //     if ($order->payment_status->is(OrderPaymentStatus::DOWN_PAYMENT)) {
        //         // jika status pembayaran masih SETTLEMENT, tapi sudah pernah create sales order
        //         if ($order->orlan_tr_no != null && $order->orlan_tr_no != '') {
        //             // create sales invoice

        //             try {
        //                 $salesInvoice = Http::post(env('ORLANSOFT_API_URL') . 'orlan-orders/salesInvoice/' . $order->orlan_tr_no . '/' . $payment->id . '/' . $payment->amount);
        //                 $salesInvoiceResult = $salesInvoice?->json();
        //                 if (isset($salesInvoiceResult) && !is_null($salesInvoiceResult)) {
        //                     $message .= '. ' . $salesInvoiceResult['message'] ?? '';
        //                 } else {
        //                     $payment->refresh();
        //                     $message = 'Sales Invoice with TrNo #' . $payment->orlan_tr_no . ' created successfully. If there is no TrNo, it means the Sales Invoice failed to created. Please check in Orlansoft';
        //                 }
        //             } catch (\Throwable $th) {
        //                 $message .= '. Failed to create Sales Invoice.';
        //             }
        //         } else {
        //             // create sales order

        //             try {
        //                 $salesOrder = Http::post(env('ORLANSOFT_API_URL') . 'orlan-orders/' . $order->id);
        //                 $salesOrderResult = $salesOrder?->json();
        //                 if (isset($salesOrderResult) && !is_null($salesOrderResult)) {
        //                     $message .= '. ' . $salesOrderResult['message'] ?? '';
        //                 } else {
        //                     $order->refresh();
        //                     $message = 'Sales Order with TrNo #' . $order->orlan_tr_no . ' created successfully. If there is no TrNo, it means the Sales Order failed to created. Please check in Orlansoft';
        //                 }
        //             } catch (\Throwable $th) {
        //                 $message .= '. Failed to create Sales Order.';
        //             }

        //             $order->refresh();
        //             // create sales invoice
        //             $paymentsToInsert = $order->orderPayments()->where('id', $payment->id)->orWhere(fn ($q) => $q->where('order_id', $payment->order_id)->where('created_at', '<', $payment->created_at)->where('status', 1))->orderBy('created_at')->get();
        //             if (count($paymentsToInsert) > 0) {
        //                 foreach ($paymentsToInsert as $payment) {

        //                     try {
        //                         $salesInvoice = Http::post(env('ORLANSOFT_API_URL') . 'orlan-orders/salesInvoice/' . $order->orlan_tr_no . '/' . $payment->id . '/' . $payment->amount);
        //                         $salesInvoiceResult = $salesInvoice?->json();
        //                         if (isset($salesInvoiceResult) && !is_null($salesInvoiceResult)) {
        //                             $message .= '. ' . $salesInvoiceResult['message'] ?? '';
        //                         } else {
        //                             $payment->refresh();
        //                             $message = 'Sales Invoice with TrNo #' . $payment->orlan_tr_no . ' created successfully. If there is no TrNo, it means the Sales Invoice failed to created. Please check in Orlansoft';
        //                         }
        //                     } catch (\Throwable $th) {
        //                         $message .= '. Failed to create Sales Invoice.';
        //                     }
        //                 }
        //             }
        //         }
        //     }

        //     /**
        //      * Jika status pembayaran sudah OVERPAYMENT atau SETTLEMENT,
        //      * cek dulu apakah sudah pernah terbuat sales order, jika belum create sales order
        //      * Jika payment statusnya OVERPAYMENT, kirim total_price dari order tsb.
        //      */
        //     if ($order->payment_status->in([OrderPaymentStatus::OVERPAYMENT, OrderPaymentStatus::SETTLEMENT])) {
        //         if ($order->orlan_tr_no == null || $order->orlan_tr_no == '') {
        //             // create sales order

        //             try {
        //                 $salesOrder = Http::post(env('ORLANSOFT_API_URL') . 'orlan-orders/' . $order->id);
        //                 $salesOrderResult = $salesOrder?->json();
        //                 if (isset($salesOrderResult) && !is_null($salesOrderResult)) {
        //                     $message .= '. ' . $salesOrderResult['message'] ?? '';
        //                 } else {
        //                     $order->refresh();
        //                     $message = 'Sales Order with TrNo #' . $order->orlan_tr_no . ' created successfully. If there is no TrNo, it means the Sales Order failed to created. Please check in Orlansoft';
        //                 }
        //             } catch (\Throwable $th) {
        //                 $message .= '. Failed to create Sales Order.';
        //             }
        //         }

        //         $order->refresh();
        //         // create sales invoice
        //         $total_payment = $order->payment_status->is(OrderPaymentStatus::OVERPAYMENT) ? $order->total_price : $payment->amount;

        //         try {
        //             $salesInvoice = Http::post(env('ORLANSOFT_API_URL') . 'orlan-orders/salesInvoice/' . $order->orlan_tr_no . '/' . $payment->id . '/' . $total_payment);
        //             $salesInvoiceResult = $salesInvoice?->json();
        //             if (isset($salesInvoiceResult) && !is_null($salesInvoiceResult)) {
        //                 $message .= '. ' . $salesInvoiceResult['message'] ?? '';
        //             } else {
        //                 $payment->refresh();
        //                 $message = 'Sales Invoice with TrNo #' . $payment->orlan_tr_no . ' created successfully. If there is no TrNo, it means the Sales Invoice failed to created. Please check in Orlansoft';
        //             }
        //         } catch (\Throwable $th) {
        //             $message .= '. Failed to create Sales Invoice.';
        //         }
        //     }
        // }

        return redirect()->route('admin.payments.index')->with('message', $message);
    }

    public function show(Payment $payment)
    {
        abort_if(Gate::denies('payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment->load('payment_type', 'added_by', 'approved_by', 'order');

        return view('admin.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        abort_if(Gate::denies('payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order = $payment->order;

        $payment->delete();

        $order->refreshPaymentStatus();

        return back();
    }

    public function massDestroy(MassDestroyPaymentRequest $request)
    {
        Payment::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('payment_create') && Gate::denies('payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Payment();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function createSiOrlan(Payment $payment)
    {
        $check = OrderService::validateCreateManualSI($payment);
        if ($check['status'] === false) return redirect()->back()->with('message', $check['message']);

        try {
            $salesInvoice = Http::post(env('ORLANSOFT_API_URL') . 'orlan-orders/salesInvoice/' . $payment->order->orlan_tr_no . '/' . $payment->id . '/' . $payment->amount);
            $salesInvoiceResult = $salesInvoice?->json();
            if (isset($salesInvoiceResult) && !is_null($salesInvoiceResult)) {
                $message = $salesInvoiceResult['message'] ?? '';
            } else {
                $payment->refresh();
                $message = 'Sales Invoice with TrNo #' . $payment->orlan_tr_no . ' created successfully. If there is no TrNo, it means the Sales Invoice failed to created. Please check in Orlansoft';
            }
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->back()->with('message', $message);
    }
}
