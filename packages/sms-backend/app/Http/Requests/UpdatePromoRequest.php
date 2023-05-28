<?php

namespace App\Http\Requests;

// use App\Rules\HasCompanyAccess;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePromoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('promo_edit');
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
                'nullable',
                'date',
            ],
            'end_time'              => [
                'nullable',
                'date',
                'after:start_time'
            ],
            // 'promo_category_id' => [
            //     'required',
            //     'exists:promo_categories,id',
            // ],
            // 'company_id'            => [
            //     'nullable',
            //     new HasCompanyAccess(),
            // ],
        ];
    }
}
