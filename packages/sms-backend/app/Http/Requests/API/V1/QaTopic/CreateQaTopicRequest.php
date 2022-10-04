<?php

namespace App\Http\Requests\API\V1\QaTopic;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\QaTopic;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateQaTopicRequest extends BaseApiRequest
{
    protected ?string $model = QaTopic::class;

    public static function data(): array
    {
        return [
            RequestData::make('subject', Schema::TYPE_STRING, 'New QA topic', 'required|string'),
            RequestData::makeArrayType('users', Schema::integer()->example(1)->description('User ids to include in this topic.'))->addRule('required|array'),
            RequestData::make('users.*', Schema::TYPE_INTEGER, 1, 'required|exists:users,id')->parent('users'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}