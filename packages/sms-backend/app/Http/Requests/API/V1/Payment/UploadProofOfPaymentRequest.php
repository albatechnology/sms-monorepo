<?php

namespace App\Http\Requests\API\V1\Payment;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\Payment;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UploadProofOfPaymentRequest extends BaseApiRequest
{
    protected ?string $model = Payment::class;

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