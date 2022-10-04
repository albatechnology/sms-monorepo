<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyInvoiceRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Invoice::with(['order', 'fulfilled_by'])->select(sprintf('%s.*', (new Invoice)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'invoice_show';
                $editGate      = 'invoice_edit';
                $deleteGate    = 'invoice_delete';
                $crudRoutePart = 'invoices';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->addColumn('order_reference', function ($row) {
                return $row->order ? $row->order->reference : '';
            });

            $table->editColumn('status', function ($row) {
                return $row->status ? Invoice::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('files', function ($row) {
                if (!$row->files) {
                    return '';
                }

                $links = [];

                foreach ($row->files as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });
            $table->addColumn('fulfilled_by_name', function ($row) {
                return $row->fulfilled_by ? $row->fulfilled_by->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'order', 'files', 'fulfilled_by']);

            return $table->make(true);
        }

        $orders = Order::get();
        $users  = User::get();

        return view('admin.invoices.index', compact('orders', 'users'));
    }

    public function create()
    {
        abort_if(Gate::denies('invoice_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orders = Order::all()->pluck('reference', 'id')->prepend(trans('global.pleaseSelect'), '');

        $fulfilled_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.invoices.create', compact('orders', 'fulfilled_bies'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = Invoice::create($request->all());

        foreach ($request->input('files', []) as $file) {
            $invoice->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $invoice->id]);
        }

        return redirect()->route('admin.invoices.index');
    }

    public function edit(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $fulfilled_bies = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $invoice->load('order', 'fulfilled_by');

        return view('admin.invoices.edit', compact('fulfilled_bies', 'invoice'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $invoice->update($request->all());

        if (count($invoice->files) > 0) {
            foreach ($invoice->files as $media) {
                if (!in_array($media->file_name, $request->input('files', []))) {
                    $media->delete();
                }
            }
        }

        $media = $invoice->files->pluck('file_name')->toArray();

        foreach ($request->input('files', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $invoice->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
            }
        }

        return redirect()->route('admin.invoices.index');
    }

    public function show(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->load('order', 'fulfilled_by');

        return view('admin.invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->delete();

        return back();
    }

    public function massDestroy(MassDestroyInvoiceRequest $request)
    {
        Invoice::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('invoice_create') && Gate::denies('invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Invoice();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
