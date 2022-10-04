<?php

namespace App\Http\Requests;

use App\Models\TaxInvoice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTaxInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('tax_invoice_edit');
    }

    public function rules()
    {
        return [
            'customer_id'  => [
                'required',
                'integer',
            ],
            'company_name' => [
                'string',
                'required',
            ],
            'npwp'         => [
                'string',
                'nullable',
            ],
            'address_id'   => [
                'required',
                'integer',
            ],
            'email'        => [
                'string',
                'nullable',
            ],
        ];
    }
}
