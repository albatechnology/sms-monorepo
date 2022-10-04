<?php

namespace App\Http\Requests;

use App\Models\Catalogue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCatalogueRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('catalogue_create');
    }

    public function rules()
    {
        return [
            'catalogue.*' => [
                'required',
            ],
        ];
    }
}
