<?php

namespace App\Http\Requests;

use App\Models\Channel;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateChannelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('channel_edit');
    }

    public function rules()
    {
        $channel = $this->route('channel');
        return [
            'orlan_id' => 'required|string',
            'orlan_tr_type' => 'required|string|max:5',
            'orlan_tr_type_as' => 'required|string|max:5',
            'orlan_tr_type_sa' => 'required|string|max:5',
            'name'                => [
                'string',
                'required',
            ],
            'channel_category_id' => [
                'required',
                'integer',
            ],
            'company_id'          => [
                'required',
                'integer',
            ],
            'sms_channel_ids' => [
                'nullable',
                'array',
            ],
            'sms_channel_ids.*' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
