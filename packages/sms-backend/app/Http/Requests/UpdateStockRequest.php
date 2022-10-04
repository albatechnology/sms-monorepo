<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_edit');
    }

    public function rules()
    {
        return [
            'increment' => [
                'required',
                'integer',
                'min:-2147483647',
                'max:2147483647',
            ],
            'cut_indent' => 'nullable',
            // 'increment_indent' => [
            //     'required',
            //     'integer',
            //     'min:-2147483647',
            //     'max:2147483647',
            // ],
        ];
    }
}
