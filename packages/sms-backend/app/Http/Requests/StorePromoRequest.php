<?php

namespace App\Http\Requests;

// use App\Rules\HasCompanyAccess;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StorePromoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('promo_create');
    }

    public function rules()
    {
        return [
            'name'                  => [
                'string',
                'required',
            ],
            'description'           => [
                'string',
                'nullable',
            ],
            'start_time'            => [
                'required',
                'date',
            ],
            'end_time'              => [
                'required',
                'date',
                'after:start_time'
            ],
            // 'promo_category_id' => [
            //     'required',
            //     'exists:promo_categories,id',
            // ],
            // 'company_id'            => [
            //     'required',
            //     new HasCompanyAccess(),
            // ],
        ];
    }
}
