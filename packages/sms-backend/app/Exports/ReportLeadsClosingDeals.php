<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportLeadsClosingDeals implements FromCollection, WithMapping, WithHeadings
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
            $data->invoice_number ?? '-',
            $data->name ?? '-',
            $data->channel ?? '-',
            $data->bum ?? '-',
            $data->invoice_price ?? 0,
            $data->amount_paid ?? 0,
        ];
    }

    public function headings(): array
    {
        return [
            'Lead ID',
            'Lead Label',
            'Invoice Number',
            'Sales',
            'Channel',
            'BUM',
            'Invoice Price',
            'Amount Paid',
        ];
    }
}
