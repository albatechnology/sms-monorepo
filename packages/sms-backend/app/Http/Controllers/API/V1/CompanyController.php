<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\Company\CompanyAccountResource;
use App\Http\Resources\V1\Company\CompanyResource;
use App\Models\Company;
use App\Models\CompanyAccount;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class CompanyController extends BaseApiController
{
    /**
     * Get all Companies
     *
     * Show companies
     *
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'companyIndex', tags: [Tags::Company, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Company::class)]
    #[CustomOpenApi\Response(resource: CompanyResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(Company::class, CompanyResource::class, fn ($q) => $q->with(['media', 'companyAccount'])->tenanted());
    }

    /**
     * Get Company
     *
     * Returns company by id
     *
     * @param Company $company
     * @return  CompanyResource
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'companyShow', tags: [Tags::Company, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: CompanyResource::class, statusCode: 200)]
    public function show(Company $company)
    {
        return new CompanyResource($company->checkTenantAccess()->loadMissing(['media', 'companyAccount']));
    }


    /**
     * Get all company account
     *
     * Show companies
     *
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'companyAccountIndex', tags: [Tags::Company, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Company::class)]
    #[CustomOpenApi\Response(resource: CompanyAccountResource::class, isCollection: true)]
    public function accountsIndex()
    {
        return CustomQueryBuilder::buildResource(CompanyAccount::class, CompanyAccountResource::class, fn ($q) => $q->tenanted());
    }
}
