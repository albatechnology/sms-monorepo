<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadFailedExport implements FromArray, WithHeadings
{
    private array $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [$this->data];
    }

    public function headings(): array
    {
        return [
            'assign_to_id',
            'label',
            'interest',
            'lead_category_id',
            'sub_lead_category_id',
            'customer_id',

            'name_title_id',
            'first_name',
            'last_name',
            'date_of_birth',
            'email',
            'phone',
            'description',
            'address_type',
            'address_line_1',
            'address_line_2',
            'address_line_3',

            'country',
            'province',
            'city',
            'postcode',
            'address_phone',
        ];
    }
}
