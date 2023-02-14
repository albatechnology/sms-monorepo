<?php

namespace App\Http\Requests\API\V1\Activity;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\Activity;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UploadImageOfActivityRequest extends BaseApiRequest
{
    protected ?string $model = Activity::class;

    public static function data(): array
    {
        return [
            RequestData::make('image', Schema::TYPE_STRING, null, 'required|image|mimes:jpeg,png,jpg,svg|max:10240'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
