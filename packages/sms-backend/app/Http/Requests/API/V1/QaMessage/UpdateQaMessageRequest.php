<?php

namespace App\Http\Requests\API\V1\QaMessage;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\QaMessage;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateQaMessageRequest extends BaseApiRequest
{
    protected ?string $model = QaMessage::class;

    public static function data(): array
    {
        return [
            RequestData::make('content', Schema::TYPE_STRING, 'Test Message', 'required|string|min:1'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}