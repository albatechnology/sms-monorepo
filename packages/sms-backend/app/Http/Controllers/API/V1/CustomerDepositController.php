<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\CustomerDeposit\CreateCustomerDepositRequest;
use App\Http\Resources\V1\CustomerDeposit\CustomerDepositResource;
use App\Models\CustomerDeposit;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class CustomerDepositController extends BaseApiController
{
    // const load_relation = ['added_by', 'payment_type'];

    // /**
    //  * Show all CustomerDeposit posted by user
    //  *
    //  * Sales will get all activities directly created by him.
    //  * Supervisor will get all activities created by its supervised sales.
    //  * Director will get all activities scoped to its active channel setting.
    //  *
    //  */
    // #[CustomOpenApi\Operation(id: 'customerDepositIndex', tags: [Tags::CustomerDeposit, Tags::V1])]
    // #[CustomOpenApi\Parameters(model: CustomerDeposit::class)]
    // #[CustomOpenApi\Response(resource: CustomerDepositResource::class, isCollection: true)]
    // public function index()
    // {

    //     $query = function ($query) {

    //         // we want to override the tenanted scope to ignore the active channel

    //         $user = tenancy()->getUser();

    //         if ($user->is_sales || $user->is_supervisor) {
    //             $query = $query->whereIn('user_id', User::descendantsAndSelf($user->id)->pluck('id'));
    //         }

    //         return $query->with(self::load_relation);
    //     };

    //     return CustomQueryBuilder::buildResource(CustomerDeposit::class, CustomerDepositResource::class, $query);
    // }

    /**
     * Create new CustomerDeposit
     *
     * Create a new CustomerDeposit
     *
     * @param CreateCustomerDepositRequest $request
     * @return CustomerDepositResource
     */
    #[CustomOpenApi\Operation(id: 'customerDepositStore', tags: [Tags::CustomerDeposit, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateCustomerDepositRequest::class)]
    #[CustomOpenApi\Response(resource: CustomerDepositResource::class, statusCode: 201)]
    public function store(CreateCustomerDepositRequest $request): CustomerDepositResource
    {
        $data = array_merge($request->validated(), [
            "user_id"    => tenancy()->getUser()->id,
        ]);

        $customerDeposit = CustomerDeposit::create($data);

        return $this->show($customerDeposit->refresh());
    }
}
