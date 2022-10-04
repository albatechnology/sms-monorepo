<?php

namespace App\Http\Requests;

use App\Models\Catalogue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCatalogueRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('catalogue_edit');
    }

    public function rules()
    {
        return [];
    }
}
