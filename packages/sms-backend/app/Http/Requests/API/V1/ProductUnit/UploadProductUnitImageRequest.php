<?php

namespace App\Http\Requests\API\V1\ProductUnit;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\ProductUnit;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UploadProductUnitImageRequest extends BaseApiRequest
{
    protected ?string $model = ProductUnit::class;

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
