<?php

namespace App\Http\Requests;

use App\Enums\Import\ImportBatchType;
use App\Rules\HasCompanyAccess;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class ModelExportRequest extends FormRequest
{

    public function rules()
    {
        return [
            'type'       => [
                'required',
                new EnumValue(ImportBatchType::class)
            ],
            'start_id'   => [
                'nullable',
                'integer',
            ],
            'end_id'     => [
                'nullable',
                'integer',
            ],
            'company_id' => 'required', new HasCompanyAccess,
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(
            [
                'type' => (int)$this->type
            ]
        );
    }
}
