<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCustomerDepositRequest;
use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\UpdateDepositRequest;
use App\Models\CustomerDeposit;
use App\Models\Order;
use App\Models\PaymentCategory;
use App\Models\PaymentType;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CustomerDepositController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('customer_deposit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CustomerDeposit::with(['payment_type.payment_category', 'user', 'approved_by', 'customer', 'lead'])->select(sprintf('%s.*', (new CustomerDeposit)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'customer_deposit_show';
                $editGate      = 'customer_deposit_edit';
                $crudRoutePart = 'customer-deposits';

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
                return $row?->customer?->fullName ?? '';
            });
            $table->addColumn('lead', function ($row) {
                return $row?->lead?->label ?? '';
            });
            $table->editColumn('value', function ($row) {
                return helper()->formatRupiah($row->value) ?: "";
            });
            $table->addColumn('payment_category_name', function ($row) {
                return $row?->payment_type?->payment_category?->name ?? '';
            });
            $table->addColumn('payment_type_name', function ($row) {
                return $row?->payment_type?->name ?? '';
            });
            $table->addColumn('user_name', function ($row) {
                return $row->user->name ?? '';
            });
            $table->addColumn('approved_by_name', function ($row) {
                return $row->approved_by->name ?? '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status?->description ?? '';
            });

            $table->rawColumns(['actions', 'placeholder', 'payment_type', 'user', 'approved_by', 'proof']);

            return $table->make(true);
        }

        $payment_categories = PaymentCategory::tenanted()->get();

        return view('admin.customerDeposits.index', compact('payment_categories'));
    }

    public function create()
    {
        abort_if(Gate::denies('customer_deposit_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment_types = PaymentType::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.customerDeposits.create', compact('payment_types'));
    }

    public function store(StoreDepositRequest $request)
    {
        $order = Order::where('invoice_number', $request->get('invoice_number'))->firstOrFail();
        $data  = array_merge(
            $request->validated(),
            [
                'user_id' => user()->id,
                'order_id'    => $order->id,
                'company_id'  => $order->company_id,
            ]
        );

        $deposit = CustomerDeposit::create($data);

        foreach ($request->input('proof', []) as $file) {
            $deposit->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('proof');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $deposit->id]);
        }

        $order->refreshdepositStatus();

        return redirect()->route('admin.customerDeposits.index');
    }

    public function edit(CustomerDeposit $customerDeposit)
    {
        abort_if(Gate::denies('customer_deposit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $approved_bies = User::tenanted()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $customerDeposit->load('approved_by');

        return view('admin.customerDeposits.edit', compact('approved_bies', 'customerDeposit'));
    }

    public function update(UpdateDepositRequest $request, CustomerDeposit $deposit)
    {
        $deposit->update($request->validated());

        if (count($deposit->proof) > 0) {
            foreach ($deposit->proof as $media) {
                if (!in_array($media->file_name, $request->input('proof', []))) {
                    $media->delete();
                }
            }
        }

        $media = $deposit->proof->pluck('file_name')->toArray();

        foreach ($request->input('proof', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $deposit->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('proof');
            }
        }

        $deposit->order->refreshdepositStatus();

        return redirect()->route('admin.customerDeposits.index');
    }

    public function show(CustomerDeposit $customerDeposit)
    {
        abort_if(Gate::denies('customer_deposit_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customerDeposit->load('payment_type.payment_category', 'user', 'approved_by', 'customer', 'lead');

        return view('admin.customerDeposits.show', compact('customerDeposit'));
    }

    public function destroy(CustomerDeposit $deposit)
    {
        abort_if(Gate::denies('customer_deposit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order = $deposit->order;

        $deposit->delete();

        $order->refreshdepositStatus();

        return back();
    }

    public function massDestroy(MassDestroyCustomerDepositRequest $request)
    {
        CustomerDeposit::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('customer_deposit_create') && Gate::denies('customer_deposit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new CustomerDeposit();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
