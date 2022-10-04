<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ReportBrands implements FromView
{
    public function __construct(public $datas)
    {
    }
    public function view(): View
    {
        return view('exports.reportBrandExport', [
            'datas' => $this->datas,
            'total_colspan' => count($this->datas ? $this->datas[0]['product_brands'] : []) ?? 0,
        ]);
    }
}
