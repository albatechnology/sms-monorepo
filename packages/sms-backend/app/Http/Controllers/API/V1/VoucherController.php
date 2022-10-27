<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Requests\API\V1\Voucher\CreateVoucherRequest;
use App\Http\Resources\V1\Voucher\VoucherResource;
use App\Models\Customer;
use App\Models\Voucher;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\Services\CoreService;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class VoucherController extends BaseApiController
{
    /**
     * Show all Voucher.
     *
     * Show all Voucher stored globally in the application by company.
     *
     */
    #[CustomOpenApi\Operation(id: 'VoucherIndex', tags: [Tags::Voucher, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Voucher::class)]
    #[CustomOpenApi\Response(resource: VoucherResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(Voucher::class, VoucherResource::class, fn ($q) => $q->tenanted());
    }

    /**
     * Get Voucher
     *
     * Return Voucher by id
     *
     * @param Voucher $voucher
     * @return  VoucherResource
     */
    #[CustomOpenApi\Operation(id: 'VoucherShow', tags: [Tags::Voucher, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: VoucherResource::class, statusCode: 200)]
    public function show(Voucher $voucher)
    {
        return new VoucherResource($voucher);
    }

    /**
     * Create new Voucher
     *
     * Show the validation rules for creating Voucher
     *
     * @param CreateVoucherRequest $request
     * @return VoucherResource
     */
    #[CustomOpenApi\Operation(id: 'VoucherStore', tags: [Tags::Voucher, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateVoucherRequest::class)]
    #[CustomOpenApi\Response(resource: VoucherResource::class, isCollection: true, statusCode: 201)]
    public function store(CreateVoucherRequest $request)
    {
        if ($request->customer_id) {
            CoreService::createAndAssignVoucher(Customer::findOrFail($request->customer_id), $request->vouchers, user()->company_id);
        } else {
            foreach ($request->vouchers as $v) {
                CoreService::createVoucher($v['id'], $v['value'], user()->company_id);
            }
        }

        return VoucherResource::collection(Voucher::whereIn('id', collect($request->vouchers)->pluck('id'))->get());
    }
}
