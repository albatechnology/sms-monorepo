<?php

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ModelExport implements FromQuery, WithHeadings
{
    use Exportable;

    const without = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function headings(): array
    {
        return array_diff(Schema::getColumnListing($this->table), self::without);
    }

    public function __construct(public Builder $query, public string $table)
    {
    }

    public function query()
    {
        return $this->query->select($this->headings());
    }
}
