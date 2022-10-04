<?php

namespace App\Http\Requests;

use App\Models\Item;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('item_create');
    }

    public function rules()
    {
        return [
            'name'       => [
                'string',
                'required',
            ],
            'code'       => [
                'string',
                'nullable',
            ],
            'company_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
