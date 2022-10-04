<?php

namespace App\Http\Resources\V1\Company;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\ResourceData;

class CompanyResource extends BaseResource
{
    public static function data(): array
    {
        return array_merge(BaseCompanyResource::data(), [
            ResourceData::image('logo'),
            ResourceData::makeRelationship('company_account', CompanyAccountResource::class, 'companyAccount'),
        ]);
    }
}
