<?php

namespace App\Jobs;

use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GenerateProductBarcode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public array $data;
    public Export $export;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($export, $data = [])
    {
        $this->export = $export;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $products = DB::table('products')->select('products.id', 'products.name');
        if (isset($this->data['brand_category_id']) && $this->data['brand_category_id'] != '' && !isset($this->data['product_brand_ids'])) {
            $products = $products->join('product_brands', 'products.product_brand_id', '=', 'product_brands.id')
                ->join('product_brand_categories', 'product_brands.id', '=', 'product_brand_categories.brand_category_id')
                ->where('product_brand_categories.brand_category_id', $this->data['brand_category_id']);
        }
        if (isset($this->data['product_brand_ids']) && $this->data['product_brand_ids'] != '') {
            $products = $products->whereIn('products.product_brand_id', $this->data['product_brand_ids']);
        }

        $output = \PDF::loadView('admin.products.generateBarcode', ['products' => $products->get()])->output();

        $name = $this->export->file_name;
        $disk = Storage::disk('public');
        if ($disk->put($name, $output)) {
            $this->export->update(['status' => 1]);
            $this->export->addMedia($disk->path($name))->toMediaCollection('exports');
            return;
        }
        $this->export->update(['status' => 2]);
        return;
    }
}
