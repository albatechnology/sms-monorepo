<?php

namespace App\Http\Requests\API\V1\User;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\User;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ChangePasswordRequest extends BaseApiRequest
{
    protected ?string $model = User::class;

    public static function data(): array
    {
        return [
            RequestData::make('password', Schema::TYPE_STRING, 'MyNewPassword', 'required|min:3'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}