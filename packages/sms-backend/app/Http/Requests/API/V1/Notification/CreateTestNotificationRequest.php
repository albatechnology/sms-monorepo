<?php

namespace App\Http\Requests\API\V1\Notification;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\Notification;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateTestNotificationRequest extends BaseApiRequest
{
    protected ?string $model = Notification::class;

    public static function data(): array
    {
        return [
            RequestData::make('title', Schema::TYPE_STRING, 'test title', 'nullable|string'),
            RequestData::make('body', Schema::TYPE_STRING, 'test body', 'nullable|string'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}