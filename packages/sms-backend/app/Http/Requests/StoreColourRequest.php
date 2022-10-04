<?php

namespace App\Http\Requests;

use Gate;
use App\Rules\HasCompanyAccess;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreColourRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('colour_create');
    }

    public function rules()
    {
        $companyId = (int)($this->get('company_id') ?? 0);

        return [
            'name'              => [
                'string',
                'required',
            ],
            'company_id'      => 'required', new HasCompanyAccess,
            'product_id'      => [
                'required',
                Rule::exists('products', 'id')
                    ->where('company_id', $companyId)
            ],
        ];
    }
}
