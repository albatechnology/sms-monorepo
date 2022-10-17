<?php

namespace App\Http\Requests\API\V1\Lead;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Enums\UserType;
use App\Models\Lead;
use App\Models\User;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class AssignLeadRequest extends BaseApiRequest
{
    protected ?string $model = Lead::class;

    public static function data(): array
    {
        return [
            RequestData::make('product_brand_ids', Schema::TYPE_ARRAY, [1, 2, 3], 'nullable|array')
                ->schema(
                    Schema::array('product_brand_ids')
                        ->items(
                            Schema::integer('id')->example(1)
                        )
                ),
            // RequestData::make('user_id', Schema::TYPE_INTEGER, 1, 'required|exists:users,id'),
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function getUser(): User
    {
        // return User::findOrFail($this->user_id);
    }
}
