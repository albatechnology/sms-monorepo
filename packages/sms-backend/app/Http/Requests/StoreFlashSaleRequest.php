<?php

namespace App\Http\Requests;

use App\Models\FlashSale;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreFlashSaleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('flash_sale_create');
    }

    public function rules()
    {
        return [
            'name'       => [
                'string',
                'required',
            ],
            'value'      => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'start_time' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'end_time'   => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'company_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
