<?php

namespace App\Http\Requests;

use App\Enums\UserType;
use App\Models\SupervisorType;
use App\Models\User;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_edit');
    }

    public function rules()
    {
        $user = User::findOrFail(request()->route('user')->id);

        return [
            'orlan_user_id' => 'nullable|string|unique:users,orlan_user_id,' . $user->id,
            'name'     => [
                'string',
                'required',
            ],
            'email'    => [
                'required',
                'unique:users,email,' . $user->id,
            ],
            'password' => [
                'nullable',
                'min:3'
            ],
            'roles.*'  => [
                'integer',
            ],
            'roles'    => [
                'nullable',
                'array',
            ],
            'type'     => [
                'required',
                new EnumValue(UserType::class, 0)
            ],

            'supervisor_type_id' => [
                'required_if:type,' . UserType::SUPERVISOR,
                'exists:supervisor_types,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->type->isNot(UserType::SUPERVISOR)) {
                        return;
                    }

                    if (empty($value)) {
                        $fail('Supervisor type is required');
                    }

                    $type = SupervisorType::find($value);
                    if (!$type) {
                        $fail('Invalid supervisor type.');
                    }
                }
            ],
            'supervisor_id'      => [
                'nullable',
                function ($attribute, $value, $fail) use ($user) {
                    $supervisor = User::find($value);

                    if (!$supervisor) {
                        $fail('Supervisor account not found');
                    }

                    if (!$supervisor->is_supervisor) {
                        $fail('Invalid supervisor selected (user not supervisor).');
                    }

                    if ($supervisor->company_id != $user->company_id) {
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
