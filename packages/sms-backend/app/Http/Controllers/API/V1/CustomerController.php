<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Classes\DocGenerator\RequestData;
use App\Http\Requests\API\V1\CreateAddressRequest;
use App\Http\Requests\API\V1\Customer\CreateCustomerRequest;
use App\Http\Requests\API\V1\Customer\CreateCustomerWithAddressRequest;
use App\Http\Requests\API\V1\Customer\UpdateCustomerRequest;
use App\Http\Resources\V1\Activity\ActivityResource;
use App\Http\Resources\V1\Customer\CustomerResource;
use App\Http\Resources\V1\Customer\CustomerVouchersResource;
use App\Http\Resources\V1\Lead\LeadResource;
use App\Models\Activity;
use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerVoucher;
use App\Models\Lead;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class CustomerController extends BaseApiController
{
    /**
     * Show all customer.
     *
     * Show all Customer stored globally in the application.
     *
     */
    #[CustomOpenApi\Operation(id: 'customerIndex', tags: [Tags::Customer, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Customer::class)]
    #[CustomOpenApi\Response(resource: CustomerResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(Customer::class, CustomerResource::class, fn ($q) => $q->tenanted());
    }

    /**
     * Show create customer rule
     *
     * Show the validation rules for creating customer
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'customerCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create()
    {
        return CreateCustomerRequest::frontEndRuleResponse();
    }

    /**
     * Create new Customer
     *
     * Show the validation rules for creating customer
     *
     * @param CreateCustomerRequest $request
     * @return CustomerResource
     */
    #[CustomOpenApi\Operation(id: 'customerStore', tags: [Tags::Customer, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateCustomerRequest::class)]
    #[CustomOpenApi\Response(resource: CustomerResource::class, statusCode: 201)]
    public function store(CreateCustomerRequest $request): CustomerResource
    {
        return $this->show(Customer::create($request->validated())->refresh());
    }

    /**
     * Get Customer
     *
     * Returns customer by id
     *
     * @param Customer $customer
     * @return  CustomerResource
     */
    #[CustomOpenApi\Operation(id: 'customerShow', tags: [Tags::Customer, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: CustomerResource::class, statusCode: 200)]
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Delete Customer
     *
     * Delete a customer by its id
     *
     * @param Customer $customer
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'customerDelete', tags: [Tags::Customer, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit customer rules
     *
     * Show the validation rules for editing customer
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'customerEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateCustomerRequest::frontEndRuleResponse();
    }

    /**
     * Update a customer
     *
     * Update a given customer
     *
     * @param Customer $customer
     * @param UpdateCustomerRequest $request
     * @return CustomerResource
     */
    #[CustomOpenApi\Operation(id: 'customerUpdate', tags: [Tags::Customer, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateCustomerRequest::class)]
    #[CustomOpenApi\Response(resource: CustomerResource::class)]
    public function update(Customer $customer, UpdateCustomerRequest $request): CustomerResource
    {
        $customer->update($request->validated());
        return $this->show($customer->refresh());
    }


    /**
     * Create new customer with address
     *
     * Create a new customer with address. This will assign the address as the
     * customer's default address id as well
     *
     * @param CreateCustomerRequest $request
     * @return CustomerResource
     */
    #[CustomOpenApi\Operation(id: 'customerStoreWithAddress', tags: [Tags::Customer, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateCustomerWithAddressRequest::class)]
    #[CustomOpenApi\Response(resource: CustomerResource::class, statusCode: 201)]
    public function storeWithAddress(CreateCustomerWithAddressRequest $request): CustomerResource
    {
        $data = $request->validated();

        $addressData = collect($data)
            ->filter(function ($value, $key) {
                $keys = collect(CreateAddressRequest::data())->map(fn (RequestData $d) => $d->getKey());
                return in_array($key, $keys->values()->all(), 0);
            })
            ->all();

        $customerData = collect($data)
            ->filter(function ($value, $key) {
                $keys = collect(CreateCustomerRequest::data())->map(fn (RequestData $d) => $d->getKey());
                return in_array($key, $keys->values()->all(), 0);
            })
            ->all();

        $customer = Customer::create($customerData);
        $address  = Address::create(array_merge($addressData, ["customer_id" => $customer->id]));
        $customer->update(["default_address_id" => $address->id]);

        return $this->show($customer->refresh());
    }

    /**
     * Show create customer with address rule
     *
     * Show the validation rules for creating customer with address
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'customerCreateWithAddress', tags: [Tags::Customer, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function createWithAddress(): JsonResponse
    {
        return CreateCustomerWithAddressRequest::frontEndRuleResponse();
    }

    /**
     * Show all leads of a customer.
     *
     * Show all leads of a given customer
     */
    #[CustomOpenApi\Operation(id: 'customerGetLeads', tags: [Tags::Customer, Tags::V1, Tags::Lead])]
    #[CustomOpenApi\Parameters(model: Lead::class)]
    #[CustomOpenApi\Response(resource: LeadResource::class, isCollection: true)]
    public function getCustomerLeads(int $customer)
    {
        $query = fn ($q) => $q->tenanted()->where('customer_id', $customer)->with(LeadController::load_relation);
        return CustomQueryBuilder::buildResource(Lead::class, LeadResource::class, $query);
    }

    /**
     * Show all activities of a customer.
     *
     * Show all activities of a given customer
     */
    #[CustomOpenApi\Operation(id: 'customerGetActivities', tags: [Tags::Customer, Tags::V1, Tags::Activity])]
    #[CustomOpenApi\Parameters(model: Activity::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class, isCollection: true)]
    public function getCustomerActivities(int $customer)
    {
        $query = fn ($q) => $q->tenanted()->where('customer_id', $customer)->with(ActivityController::load_relation);
        return CustomQueryBuilder::buildResource(Activity::class, ActivityResource::class, $query);
    }

    public function getCustomerByPhone()
    {
        request()->validate(['phone' => 'required']);
        $customer = Customer::tenanted()->where('phone', request()->phone)->first();

        return response()->json(['success' => $customer ? true : false, 'customer' => $customer]);
    }

    /**
     * Show all customer vouchers.
     *
     * Show all customer vouchers.
     *
     */
    #[CustomOpenApi\Operation(id: 'CustomerVouchers', tags: [Tags::Customer, Tags::V1])]
    #[CustomOpenApi\Parameters(model: CustomerVoucher::class)]
    #[CustomOpenApi\Response(resource: CustomerVouchersResource::class, isCollection: true)]
    public function customerVouchers()
    {
        $query = fn ($q) => $q->with('voucher');
        return CustomQueryBuilder::buildResource(CustomerVoucher::class, CustomerVouchersResource::class, $query, useDefaultSort: false);
    }
    // /**
    //  * Show all customer vouchers.
    //  *
    //  * Show all customer vouchers.
    //  *
    //  */
    // #[CustomOpenApi\Operation(id: 'CustomerVouchers', tags: [Tags::Customer, Tags::V1])]
    // #[CustomOpenApi\Parameters(model: Customer::class)]
    // #[CustomOpenApi\Response(resource: CustomerVouchersResource::class, isCollection: true)]
    // public function customerVouchers(Customer $customer)
    // {
    //     $query = fn ($q) => $q->where('customer_id', $customer->id)->with('voucher');
    //     return CustomQueryBuilder::buildResource(CustomerVoucher::class, CustomerVouchersResource::class, $query, useDefaultSort: false);
    // }
}
