<?php

namespace App\Http\Requests;

use App\Models\TaxInvoice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyTaxInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('tax_invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:tax_invoices,id',
        ];
    }
}
