<?php

namespace App\Http\Requests;

use App\Enums\UserType;
use App\Models\User;
use BenSampo\Enum\Rules\EnumValue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSmsUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'required',
                'unique:users',
            ],
            'password' => [
                'nullable',
                'min:6'
            ],
            'type' => [
                'required',
                new EnumValue(UserType::class, 0)
            ],
            'supervisor_id' => [
                'required_if:type,' . UserType::SALES_SMS,
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $supervisor = User::find($value);
                    if ($supervisor->type->isNot(UserType::SUPERVISOR_SMS)) $fail('Invalid supervisor selected (user not supervisor).');
                }
            ],
            'channel_id' => [
                'required_if:type,' . UserType::SUPERVISOR_SMS,
                'exists:companies,id'
            ]
        ];
    }
}
