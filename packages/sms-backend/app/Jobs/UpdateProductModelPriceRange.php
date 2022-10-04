<?php

namespace App\Jobs;

use App\Models\ProductModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductModelPriceRange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $models = ProductModel::query()
            ->where('need_price_range_update', 1)
            ->get();

        $models->each(function (ProductModel $model) {
            $model->updatePriceRange();
        });
    }
}