<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesRevenueExport implements FromCollection, WithHeadings
{

    public function __construct(public Collection $data)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Sales',
            'Company',
            'Channel',
            'Periode',
            'Sales Order',
            'Target',
            'Achievement',
        ];
    }
}
