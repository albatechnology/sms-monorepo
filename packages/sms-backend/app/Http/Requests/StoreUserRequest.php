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
            'orlan_user_id' => 'nullable|string|unique:users,orlan_user_id',
            'name'        => [
                'string',
                'required',
            ],
            'email'              => [
                'required',
                'unique:users',
            ],
            'password'           => [
                'required',
                'min:3'
            ],
            'roles.*'            => [
                'integer',
            ],
            'roles'              => [
                'nullable',
                'array',
            ],
            'type'               => [
                'required',
                new EnumValue(UserType::class, 0)
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
            'company_id'         => [
                'nullable',
                'exists:companies,id'
            ],
            'company_ids'         => [
                'nullable',
                'array',
            ],
            'channels.*'         => [
                'integer',
            ],
            'channels'           => [
                'array',
            ],
        ];
    }
}
