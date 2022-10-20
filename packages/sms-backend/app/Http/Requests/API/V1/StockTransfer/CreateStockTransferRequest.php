<?php

namespace App\Http\Requests\API\V1\StockTransfer;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\RequestData;
use App\Enums\StockTransferStatus;
use App\Models\StockTransfer;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CreateStockTransferRequest extends BaseApiRequest
{
    protected ?string $model = StockTransfer::class;

    public static function data(): array
    {
        return [
            RequestData::make('amount', Schema::TYPE_INTEGER, 1, 'required|integer'),
            RequestData::make('company_id', Schema::TYPE_INTEGER, 1, 'required|exists:companies,id'),
            RequestData::make('from_channel_id', Schema::TYPE_INTEGER, 1, 'required|exists:channels,id'),
            RequestData::make('to_channel_id', Schema::TYPE_INTEGER, 1, 'required|exists:channels,id'),
            RequestData::make('product_id', Schema::TYPE_INTEGER, 1, 'required|exists:products,id'),
            RequestData::make('cart_id', Schema::TYPE_INTEGER, 1, 'nullable|exists:carts,id'),
            RequestData::makeEnum('status', StockTransferStatus::class, true),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
