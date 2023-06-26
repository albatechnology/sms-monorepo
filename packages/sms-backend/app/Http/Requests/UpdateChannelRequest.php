<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChannelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('channel_edit');
    }

    public function rules()
    {
        return [
            'name'                => [
                'string',
                'required',
            ],
            // 'channel_category_id' => [
            //     'required',
            //     'integer',
            // ],
            // 'company_id'          => [
            //     'required',
            //     'integer',
            // ],
        ];
    }
}
