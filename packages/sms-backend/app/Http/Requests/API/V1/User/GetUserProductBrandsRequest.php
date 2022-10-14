<?php

namespace App\Http\Requests\API\V1\User;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Models\Channel;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class GetUserProductBrandsRequest extends BaseApiRequest
{
    protected ?string $model = Channel::class;

    public static function data(): array
    {
        return [
            RequestData::make('company_id', Schema::TYPE_INTEGER, 1, 'nullable|exists:companies,id'),
            RequestData::make('lead_id', Schema::TYPE_INTEGER, 1, 'nullable|exists:leads,id'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
