<?php

namespace App\Http\Requests\API\V1\OrderDetail;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\OrderDetail;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UploadOrderDetailImageRequest extends BaseApiRequest
{
    protected ?string $model = OrderDetail::class;

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
