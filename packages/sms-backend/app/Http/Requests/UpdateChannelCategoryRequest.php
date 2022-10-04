<?php

namespace App\Http\Requests;

use App\Models\ChannelCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateChannelCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('channel_category_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'code' => [
                'string',
                'nullable',
            ],
        ];
    }
}
