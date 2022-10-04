<?php

namespace App\Http\Requests\API\V1\ActivityComment;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\ActivityComment;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateActivityCommentRequest extends BaseApiRequest
{
    protected ?string $model = ActivityComment::class;

    public static function data(): array
    {
        return [
            RequestData::make('content', Schema::TYPE_STRING, 'My Comment', 'required|string|min:1|max:100'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}