<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderPaymentStatus;
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
use Gate;
use Illuminate\Http\Request;
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
                'subscribtion_user_id'  => $order->subscribtion_user_id,
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
}
