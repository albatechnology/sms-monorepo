<?php


namespace App\Traits;

use App\Interfaces\Reportable;

trait ReportableEvent
{
    /**
     * @return Reportable
     */
    public function getReportableModel(): Reportable
    {
        return $this->model;
    }
}
