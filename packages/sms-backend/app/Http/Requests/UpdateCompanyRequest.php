<?php

namespace App\Http\Requests;

use App\Enums\LeadStatus;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('company_edit');
    }

    public function rules()
    {
        $leadStatusOptionRule = collect(\App\Enums\LeadStatus::getInstances())
            ->filter(function (LeadStatus $enum) {
                return $enum->nextStatus() !== null;
            })
            ->keyBy(function (LeadStatus $enum) {
                return 'options_lead_status_duration_days_' . $enum->value;
            })
            ->map(function (LeadStatus $enum) {
                return [
                    'required',
                    'integer',
                    'min:1'
                ];
            })
            ->all();

        return array_merge([
            'name'               => [
                'string',
                'required',
            ],
            'company_account_id' => [
                'integer',
                Rule::exists('company_accounts', 'id')
                    ->where('company_id', request()->route('company')->id),
            ],
        ], $leadStatusOptionRule);
    }
}
