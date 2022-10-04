<?php

namespace App\Models;

use App\Enums\StockHistoryType;
use App\Interfaces\Tenanted;
use App\Traits\IsCompanyTenanted;

/**
 * @mixin IdeHelperStockHistory
 */
class StockHistory extends BaseModel implements Tenanted
{
    use IsCompanyTenanted;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'stock_id',
        'quantity',
        'type',
        'order_detail_id',
        'stock_transfer_id',
        'user_id',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'stock_id'          => 'integer',
        'quantity'          => 'integer',
        'order_detail_id'   => 'integer',
        'stock_transfer_id' => 'integer',
        'user_id'           => 'integer',
        'company_id'        => 'integer',
        'type'              => StockHistoryType::class,
    ];

}