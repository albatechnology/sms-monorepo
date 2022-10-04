<?php

namespace App\Http\Requests\API\V1\ActivityComment;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\ActivityComment;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateActivityCommentRequest extends BaseApiRequest
{
    protected ?string $model = ActivityComment::class;

    public static function data(): array
    {
        return [
            RequestData::make('content', Schema::TYPE_STRING, 'My Comment', 'required|string|min:1|max:100'),
            RequestData::make('activity_comment_id', Schema::TYPE_INTEGER, 1, 'nullable|exists:activity_comments,id'),
            RequestData::make('activity_id', Schema::TYPE_INTEGER, 1, 'required|exists:activities,id'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}