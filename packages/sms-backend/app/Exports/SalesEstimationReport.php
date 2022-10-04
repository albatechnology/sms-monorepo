<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesEstimationReport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(public $datas)
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->datas;
    }

    public function map($data): array
    {
        return [
            $data->created_at ?? '-',
            $data->sales ?? '-',
            $data->customer ?? '-',
            $data->brand ?? '-',
            $data->estimated_value ?? 0,
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Sales',
            'Customer Name',
            'Brand',
            'Estimated Value',
        ];
    }
}
