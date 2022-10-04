<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsActivityReport implements FromCollection, WithMapping, WithHeadings
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
            $data->lead?->id ?? '-',
            $data->lead?->label ?? '-',
            $data->created_at ?? '-',
            implode(' ', [$data->customer?->first_name, $data->customer?->last_name]) ?? '-',
            \App\Enums\ActivityFollowUpMethod::fromValue($data->follow_up_method)->description ?? '-',
            $data->channel?->name ?? '-',
            $data->user?->name ?? '-',
            \App\Enums\ActivityStatus::fromValue($data->status)->description ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Lead ID',
            'Label',
            'Date',
            'Name',
            'Follow Up',
            'Store Location',
            'Sales',
            'Status',
        ];
    }
}
