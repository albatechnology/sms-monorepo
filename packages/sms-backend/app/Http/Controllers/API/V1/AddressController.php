<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Http\Requests\API\V1\CreateAddressRequest;
use App\Http\Requests\API\V1\UpdateAddressRequest;
use App\Http\Resources\V1\Address\AddressResource;
use App\Models\Address;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class AddressController extends BaseApiController
{

    /**
     * Get address
     *
     * Returns address by id
     *
     * @param Address $address
     * @return  AddressResource
     */
    #[CustomOpenApi\Operation(id: 'addressShow', tags: [Tags::Address, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: AddressResource::class, statusCode: 200)]
    public function show(Address $address)
    {
        return new AddressResource($address);
    }

    /**
     * Show all address.
     *
     * Show all address
     *
     */
    #[CustomOpenApi\Operation(id: 'addressIndex', tags: [Tags::Address, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Address::class)]
    #[CustomOpenApi\Response(resource: AddressResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(Address::class, AddressResource::class);
    }

    /**
     * Show create product address
     *
     * Show the validation rules for creating address
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'addressCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreateAddressRequest::frontEndRuleResponse();
    }

    /**
     * Create new Address
     *
     * Show the validation rules for creating address
     *
     * @param CreateAddressRequest $request
     * @return AddressResource
     */
    #[CustomOpenApi\Operation(id: 'addressStore', tags: [Tags::Address, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateAddressRequest::class)]
    #[CustomOpenApi\Response(resource: AddressResource::class, statusCode: 201)]
    public function store(CreateAddressRequest $request): AddressResource
    {
        return $this->show(Address::create($request->validated())->refresh());
    }

    /**
     * Delete Address
     *
     * Delete a address by its id
     *
     * @param Address $address
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'addressDestroy', tags: [Tags::Address, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function destroy(Address $address)
    {
        $address->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit address rules
     *
     * Show the validation rules for editing address
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'addressEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateAddressRequest::frontEndRuleResponse();
    }

    /**
     * Update a address
     *
     * Update a given address
     *
     * @param Address $address
     * @param UpdateAddressRequest $request
     * @return AddressResource
     */
    #[CustomOpenApi\Operation(id: 'addressUpdate', tags: [Tags::Address, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateAddressRequest::class)]
    #[CustomOpenApi\Response(resource: AddressResource::class)]
    public function update(Address $address, UpdateAddressRequest $request): AddressResource
    {
        $address->update($request->validated());
        return $this->show($address->refresh());
    }
}