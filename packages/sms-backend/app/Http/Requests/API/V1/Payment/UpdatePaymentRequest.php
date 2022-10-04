<?php

namespace App\Http\Requests\API\V1\Payment;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\Payment;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdatePaymentRequest extends BaseApiRequest
{
    protected ?string $model = Payment::class;

    public static function data(): array
    {
        return [
            RequestData::make('reference', Schema::TYPE_STRING, 'My Payment', 'nullable|min:1|max:100'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}