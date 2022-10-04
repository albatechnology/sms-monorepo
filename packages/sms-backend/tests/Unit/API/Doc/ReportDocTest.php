<?php

namespace Tests\Unit\API\Doc;

use App\Models\Company;
use App\Models\Report;

/**
 * Class ReportDocTest
 * @package Tests\Unit\API
 */
class ReportDocTest extends BaseApiDocTest
{
    protected Company $company;
    protected Report $report;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::first() ?? Company::factory()->create();
        $this->report  = Report::factory()->forUser($this->user)->create();

    }

    /**
     * @group Doc
     * @return void
     */
    public function testReportIndex()
    {
        $this->makeApiTest(route('reports.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testReportShow()
    {
        $this->makeApiTest(route('companies.show', [$this->report->id], false), 'get', null, 0);
    }

    // FIXME: unable to get id to show on unit test. Most likely sqlite issue
    //   with custom sql used in this endpoint
//    /**
//     * @group Doc
//     * @return void
//     */
//    public function testTargetIndex()
//    {
//        $this->makeApiTest(route('targets.index', [], false), 'get', null, 1);
//    }
}
