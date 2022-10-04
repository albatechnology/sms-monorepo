<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaxInvoiceRequest;
use App\Http\Requests\UpdateTaxInvoiceRequest;
use App\Http\Resources\Admin\TaxInvoiceResource;
use App\Models\TaxInvoice;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaxInvoiceApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('tax_invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TaxInvoiceResource(TaxInvoice::with(['customer', 'address'])->get());
    }

    public function store(StoreTaxInvoiceRequest $request)
    {
        $taxInvoice = TaxInvoice::create($request->all());

        return (new TaxInvoiceResource($taxInvoice))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(TaxInvoice $taxInvoice)
    {
        abort_if(Gate::denies('tax_invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TaxInvoiceResource($taxInvoice->load(['customer', 'address']));
    }

    public function update(UpdateTaxInvoiceRequest $request, TaxInvoice $taxInvoice)
    {
        $taxInvoice->update($request->all());

        return (new TaxInvoiceResource($taxInvoice))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(TaxInvoice $taxInvoice)
    {
        abort_if(Gate::denies('tax_invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $taxInvoice->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
