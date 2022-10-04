<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportLeadsExport implements FromView
{
    public function __construct(public $datas)
    {
    }
    public function view(): View
    {
        return view('exports.reportLeadsExportNew', [
            'datas' => $this->datas
        ]);
    }
}
