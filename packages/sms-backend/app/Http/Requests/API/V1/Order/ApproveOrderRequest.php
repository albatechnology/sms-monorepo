<?php

namespace App\Http\Requests\API\V1\Order;

use App\Classes\DocGenerator\BaseApiRequest;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ApproveOrderRequest extends BaseApiRequest
{
    public static function getSchemas(): array
    {
        return [
            Schema::string('comment')->example('Comment on activity'),
            Schema::string('reject')->example(true),
        ];
    }

    protected static function data()
    {
        return [];
    }

    public function rules(): array
    {
        return [
            'comment' => 'nullable|string',
            'reject' => 'nullable|boolean',
        ];
    }
}
