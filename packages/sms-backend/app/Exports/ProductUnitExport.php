<?php

namespace App\Exports;

use App\Models\ProductUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductUnitExport implements FromCollection, WithHeadings
{
    protected array $request = [];
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ProductUnit::whereHas('product.brand', fn ($q) => $q->where('id', $this->request['product_brand_id']))->where('company_id', $this->request['company_id'])->whereBetween('id', [$this->request['start_id'], $this->request['end_id']])->select('id','name','description','price','sku','is_active')->orderBy('id','asc')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'description',
            'price',
            'sku',
            'is_active',
        ];
    }
}
