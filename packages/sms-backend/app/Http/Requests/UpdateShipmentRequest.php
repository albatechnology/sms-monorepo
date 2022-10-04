<?php

namespace App\Http\Requests;

use App\Enums\ShipmentStatus;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('shipment_edit');
    }

    public function rules()
    {
        return [
            'status'    => [
                'required',
                new EnumValue(ShipmentStatus::class, 0)
            ],
            'reference' => [
                'string',
                'nullable',
            ],
            'note'      => [
                'string',
                'nullable',
            ],
        ];
    }
}
