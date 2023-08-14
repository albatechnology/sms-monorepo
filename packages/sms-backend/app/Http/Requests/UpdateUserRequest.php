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
            'role'  => [
                'required',
                'integer',
                'exists:roles,id'
            ],
            'type'     => [
                'required',
                new EnumValue(UserType::class, 0)
            ],
            'company_id'         => [
                'nullable',
                'exists:companies,id'
            ],
            // 'company_ids'         => [
            //     'nullable',
            //     'array',
            // ],
            // 'channel_ids.*'         => [
            //     'integer',
            //     'exists:channels,id',
            // ],
            'channel_ids'           => [
                'array',
            ],
            'channel_id'         => [
                'nullable',
                'exists:channels,id',
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
            // 'product_brand_ids'         => [
            //     'nullable',
            //     'array',
            // ],
            // 'product_brand_ids.*'         => [
            //     'integer',
            //     'exists:product_brands,id'
            // ],
        ];
    }
}
