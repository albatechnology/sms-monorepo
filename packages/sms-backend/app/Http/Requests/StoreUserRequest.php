<?php

namespace App\Http\Requests;

use App\Enums\UserType;
use App\Models\User;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_create');
    }

    public function rules()
    {
        return [
            'name'        => [
                'string',
                'required',
            ],
            'email'              => [
                'required',
                'unique:users,email',
            ],
            'password'           => [
                'required',
                'min:3'
            ],
            'role'            => [
                'required',
                'integer',
                'exists:roles,id'
            ],
            'type'               => [
                'required',
                new EnumValue(UserType::class, 0)
            ],
            // 'company_id'         => [
            //     'nullable',
            //     'exists:companies,id'
            // ],
            // 'company_ids'         => [
            //     'nullable',
            //     'array',
            // ],
            'channel_ids'           => [
                'nullable',
                'array',
            ],
            'channel_ids.*'         => [
                'integer',
                'exists:channels,id',
            ],
            'channel_id'         => [
                'nullable',
                'exists:channels,id',
            ],
            'supervisor_type_id' => [
                'required_if:type,' . UserType::SUPERVISOR,
                'exists:supervisor_types,id'
            ],
            'supervisor_id'      => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $supervisor = User::find($value);

                    if (!$supervisor) {
                        $fail('Supervisor account not found');
                    }

                    if (!$supervisor->is_supervisor) {
                        $fail('Invalid supervisor selected (user not supervisor).');
                    }

                    if ($supervisor->company_id != (request()->get('company_id') ?? 0)) {
                        $fail('Cannot select supervisor from different company');
                    }
                }
            ],
            'product_brand_ids'         => [
                'nullable',
                'array',
            ],
            'product_brand_ids.*'         => [
                'integer',
                'exists:product_brands,id'
            ],
        ];
    }
}
