<?php

namespace App\Listeners;

use App\Services\ReportService;

class RecordReportTarget
{

    public function __construct(protected ReportService $service)
    {
        //
    }

    public function handle($event)
    {
        $this->service->registerTargetMap($event->getReportableModel());
    }
}
