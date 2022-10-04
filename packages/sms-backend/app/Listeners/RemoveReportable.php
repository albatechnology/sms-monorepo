<?php

namespace App\Listeners;

use App\Services\ReportService;

class RemoveReportable
{
    public function __construct(protected ReportService $service)
    {
        //
    }

    public function handle($event)
    {
        $this->service->removeModelFromTargetMap($event->getReportableModel());
    }
}
