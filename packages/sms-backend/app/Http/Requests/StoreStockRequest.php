<?php

namespace App\Http\Requests;

use App\Models\Stock;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreStockRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_create');
    }

    public function rules()
    {
        return [
            'channel_id' => [
                'required',
                'integer',
            ],
            'stock'      => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'item_id'    => [
                'required',
                'integer',
            ],
        ];
    }
}
