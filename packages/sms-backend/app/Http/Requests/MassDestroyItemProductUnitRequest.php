<?php

namespace App\Http\Requests;

use App\Models\ItemProductUnit;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyItemProductUnitRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('item_product_unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:item_product_units,id',
        ];
    }
}
