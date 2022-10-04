<?php

namespace App\Http\Requests;

use App\Models\StockTransfer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stock_transfer_create');
    }

    public function rules()
    {
        return [
            'stock_from_id'   => [
                'required',
                'integer',
            ],
            'stock_to_id'     => [
                'required',
                'integer',
            ],
            'requested_by_id' => [
                'required',
                'integer',
            ],
            'amount'          => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'status'    => [
                'required',
                'integer',
            ],
            'cart_id'    => [
                'nullable',
                'integer',
            ],
            'item_from_id'    => [
                'required',
                'integer',
            ],
            'item_to_id'      => [
                'required',
                'integer',
            ],
            'item_code'       => [
                'string',
                'required',
            ],
        ];
    }
}
