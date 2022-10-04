<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportLeadsHot implements FromCollection, WithMapping, WithHeadings
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
            $data->lead_id,
            $data->label ?? '-',
            $data->name ?? '-',
            $data->channel ?? '-',
            $data->bum ?? '-',
            $data->estimated_value ?? 0,
            $data->quotation ?? 0,
        ];
    }

    public function headings(): array
    {
        return [
            'Lead ID',
            'Lead Label',
            'Sales',
            'Channel',
            'BUM',
            'Estimated Value',
            'Quotation',
        ];
    }
}
