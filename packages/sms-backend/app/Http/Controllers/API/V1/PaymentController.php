<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\Payment\CreatePaymentRequest;
use App\Http\Requests\API\V1\Payment\UpdatePaymentRequest;
use App\Http\Requests\API\V1\Payment\UploadProofOfPaymentRequest;
use App\Http\Resources\V1\Payment\PaymentCategoryResource;
use App\Http\Resources\V1\Payment\PaymentResource;
use App\Http\Resources\V1\Payment\PaymentTypeResource;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\PaymentType;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\RequestBodies\Custom\ImageRequestBody;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class PaymentController extends BaseApiController
{
    const load_relation = ['added_by', 'payment_type'];

    /**
     * Show all payment.
     *
     * Show all payment
     *
     */
    #[CustomOpenApi\Operation(id: 'PaymentIndex', tags: [Tags::Payment, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Payment::class)]
    #[CustomOpenApi\Response(resource: PaymentResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(Payment::class, PaymentResource::class, fn($query) => $query->with(self::load_relation)->tenanted());
    }

    /**
     * Get create payment rule
     *
     * Show the validation rules for creating payment
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'PaymentCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreatePaymentRequest::frontEndRuleResponse();
    }

    /**
     * Create new Payment
     *
     * Create a new payment
     *
     * @param CreatePaymentRequest $request
     * @return PaymentResource|JsonResponse
     */
    #[CustomOpenApi\Operation(id: 'PaymentStore', tags: [Tags::Payment, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreatePaymentRequest::class)]
    #[CustomOpenApi\Response(resource: PaymentResource::class, statusCode: 201)]
    public function store(CreatePaymentRequest $request)
    {
        $response = app(OrderService::class)->makeOrderPayment(
            $request->get('amount'),
            $request->get('payment_type_id'),
            $request->get('order_id'),
            $request->get('reference'),
        );

        if ($response instanceof MessageBag) {
            return response()->json($response->toArray(), 422);
        }

        return $this->show($response->loadMissing(self::load_relation));
    }

    /**
     * Get payment
     *
     * Returns payment by id
     *
     * @param Payment $payment
     * @return  PaymentResource
     */
    #[CustomOpenApi\Operation(id: 'PaymentShow', tags: [Tags::Payment, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: PaymentResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        return new PaymentResource($payment->loadMissing(self::load_relation)->checkTenantAccess());
    }

    /**
     * Show edit payment rules
     *
     * Show the validation rules for editing payment
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'PaymentEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdatePaymentRequest::frontEndRuleResponse();
    }

    /**
     * Update a payment
     *
     * Update a given payment
     *
     * @param Payment $payment
     * @param UpdatePaymentRequest $request
     * @return PaymentResource
     */
    #[CustomOpenApi\Operation(id: 'PaymentUpdate', tags: [Tags::Payment, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdatePaymentRequest::class)]
    #[CustomOpenApi\Response(resource: PaymentResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function update(Payment $payment, UpdatePaymentRequest $request): PaymentResource
    {
        $this->authorize('update', $payment);
        $payment->checkTenantAccess()->update($request->validated());
        return $this->show($payment->loadMissing(self::load_relation));
    }

    /**
     * Show all payment category.
     *
     * Show all payment category
     *
     */
    #[CustomOpenApi\Operation(id: 'PaymentCategoryIndex', tags: [Tags::Payment, Tags::V1])]
    #[CustomOpenApi\Parameters(model: PaymentCategory::class)]
    #[CustomOpenApi\Response(resource: PaymentCategoryResource::class, isCollection: true)]
    public function indexPaymentCategory()
    {
        return CustomQueryBuilder::buildResource(PaymentCategory::class, PaymentCategoryResource::class, fn($query) => $query->tenanted());
    }

    /**
     * Upload proof of payment
     *
     * Upload proof of payment
     *
     * @param Payment $payment
     * @param UploadProofOfPaymentRequest $request
     * @return JsonResponse
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    #[CustomOpenApi\Operation(id: 'PaymentProofUpload', tags: [Tags::Payment, Tags::V1])]
    #[CustomOpenApi\Parameters(model: PaymentCategory::class)]
    #[OpenApi\RequestBody(factory: ImageRequestBody::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function uploadProofOfPayment(
        Payment $payment,
        UploadProofOfPaymentRequest $request,
    ): JsonResponse
    {
        $payment->addMedia($request->file('image'))
            ->toMediaCollection(Payment::PROOF_COLLECTION, 's3-private');

        return response()->json(['message' => 'success']);
    }


    /**
     * Show all payment types.
     *
     * Show all payment types
     *
     */
    #[CustomOpenApi\Operation(id: 'PaymentTypeIndex', tags: [Tags::Payment, Tags::V1])]
    #[CustomOpenApi\Parameters(model: PaymentType::class)]
    #[CustomOpenApi\Response(resource: PaymentTypeResource::class, isCollection: true)]
    public function indexPaymentType()
    {
        return CustomQueryBuilder::buildResource(PaymentType::class, PaymentTypeResource::class, fn($query) => $query->tenanted());
    }
}
