<?php

namespace Tests\Unit\API\Doc;

use App\Models\Company;
use App\Models\CompanyAccount;

/**
 * Class CompanyTest
 * @package Tests\Unit\API
 */
class CompanyDocTest extends BaseApiDocTest
{
    protected Company $company;
    protected Company $companyWithAccount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::first() ?? Company::factory()->create();
        $account       = CompanyAccount::factory()->create(['company_id' => $this->company->id]);
        $this->company->update(['company_account_id' => $account->id]);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCompanyIndex()
    {
        $this->makeApiTest(route('companies.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCompanyShow()
    {
        $this->makeApiTest(route('companies.show', [$this->company->id], false), 'get', null, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCompanyAccountIndex()
    {
        $this->makeApiTest(route('company-accounts.index', [], false), 'get', null, 0);
    }
}
