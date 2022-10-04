<?php


namespace App\Interfaces;

/**
 * Interface Reportable
 * @package App\Interfaces
 */
interface ReportableScope
{
    public function getReportLabel(): string;

    public function scopeWhereCompanyId($query, int $id);

    public function scopeWhereSupervisorTypeLevel($query, int $id);
}
