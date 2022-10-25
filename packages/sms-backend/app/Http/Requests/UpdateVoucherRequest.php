<?php

namespace App\Http\Requests;

use App\Enums\voucherType;
use App\Rules\HasCompanyAccess;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVoucherRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('voucher_create');
    }

    public function rules()
    {
        $voucher = $this->route('voucher');

        return [
            'description'                  => [
                'string',
                'nullable',
            ],
            'code'              => [
                'nullable',
                'unique:vouchers,code,' . $voucher->id,
                'string',
                'min:4',
            ],
            'value'                        => [
                'required',
                'numeric',
                'min:0',
            ],
            'start_time'                   => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'end_time'                     => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'after:start_time'
            ],
            'is_active'                    => [
                'boolean'
            ],
            'min_order_price'              => [
                'nullable',
                'integer',
                'min:0',
            ],
            'company_id'                   => [
                'required',
                new HasCompanyAccess(),
            ],
        ];
    }
}
