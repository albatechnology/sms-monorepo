<?php

namespace App\Http\Requests;

use App\Models\Item;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateItemRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('item_edit');
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
