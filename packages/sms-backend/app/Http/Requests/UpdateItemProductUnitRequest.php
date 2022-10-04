<?php

namespace App\Http\Requests;

use App\Models\ItemProductUnit;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateItemProductUnitRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('item_product_unit_edit');
    }

    public function rules()
    {
        return [
            'product_unit_id' => [
                'required',
                'integer',
            ],
            'item_id'         => [
                'required',
                'integer',
            ],
            'uom'             => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
