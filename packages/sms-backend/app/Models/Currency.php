<?php

namespace App\Models;

use App\Enums\CurrencyList;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $guarded = [];
    protected $casts = [
        'main_currency' => CurrencyList::class,
        'foreign_currency' => CurrencyList::class
    ];
}
