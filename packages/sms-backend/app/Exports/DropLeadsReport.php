<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DropLeadsReport implements FromCollection, WithMapping, WithHeadings
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
            $data->sales?->name,
            implode(' ', [$data->customer?->first_name, $data->customer?->last_name]) ?? '-',
            $data->channel?->name ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Sales',
            'Customer',
            'Channel',
        ];
    }
}
