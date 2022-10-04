<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Exceptions\GenericAuthorizationException;
use App\Http\Requests\API\V1\QaMessage\CreateQaMessageRequest;
use App\Http\Requests\API\V1\QaMessage\UpdateQaMessageRequest;
use App\Http\Resources\V1\Qa\QaMessageResource;
use App\Models\QaMessage;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class QaMessageController extends BaseApiController
{

    const load_relation = ['sender', 'topic'];

    /**
     * Show all qa message.
     *
     * Show all qa message
     *
     */
    #[CustomOpenApi\Operation(id: 'QaMessageIndex', tags: [Tags::QaMessage, Tags::V1])]
    #[CustomOpenApi\Parameters(model: QaMessage::class)]
    #[CustomOpenApi\Response(resource: QaMessageResource::class, isCollection: true)]
    public function index()
    {
        $query = fn($query) => $query->where('sender_id', tenancy()->getUser()->id)->with(self::load_relation);
        return CustomQueryBuilder::buildResource(QaMessage::class, QaMessageResource::class, $query);
    }

    /**
     * Get create qa message rule
     *
     * Show the validation rules for creating qa message
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'QaMessageCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreateQaMessageRequest::frontEndRuleResponse();
    }

    /**
     * Create new Qa message
     *
     * Create a new qa message
     *
     * @param CreateQaMessageRequest $request
     * @return QaMessageResource
     */
    #[CustomOpenApi\Operation(id: 'QaMessageStore', tags: [Tags::QaMessage, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateQaMessageRequest::class)]
    #[CustomOpenApi\Response(resource: QaMessageResource::class, statusCode: 201)]
    #[CustomOpenApi\ErrorResponse(exception: GenericAuthorizationException::class)]
    public function store(CreateQaMessageRequest $request): QaMessageResource
    {
        $qaMessage = QaMessage::make(array_merge($request->validated(), ["sender_id" => tenancy()->getUser()->id]));
        $this->authorize('store', $qaMessage);
        $qaMessage->save();

        return $this->show($qaMessage->loadMissing(self::load_relation));
    }

    /**
     * Get qa message
     *
     * Returns qa message by id
     *
     * @param QaMessage $qaMessage
     * @return  QaMessageResource
     */
    #[CustomOpenApi\Operation(id: 'QaMessageShow', tags: [Tags::QaMessage, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: QaMessageResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: GenericAuthorizationException::class)]
    public function show(QaMessage $qaMessage)
    {
        $this->authorize('show', $qaMessage);
        return new QaMessageResource($qaMessage->loadMissing(self::load_relation));
    }

    /**
     * Delete Qa message
     *
     * Delete a qa message by its id
     *
     * @param QaMessage $qaMessage
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'QaMessageDestroy', tags: [Tags::QaMessage, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    #[CustomOpenApi\ErrorResponse(exception: GenericAuthorizationException::class)]
    public function destroy(QaMessage $qaMessage)
    {
        $this->authorize('destroy', $qaMessage);
        $qaMessage->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit qa message rules
     *
     * Show the validation rules for editing qa message
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'QaMessageEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateQaMessageRequest::frontEndRuleResponse();
    }

    /**
     * Update a qa message
     *
     * Update a given qa message
     *
     * @param QaMessage $qaMessage
     * @param UpdateQaMessageRequest $request
     * @return QaMessageResource
     */
    #[CustomOpenApi\Operation(id: 'QaMessageUpdate', tags: [Tags::QaMessage, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateQaMessageRequest::class)]
    #[CustomOpenApi\Response(resource: QaMessageResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: GenericAuthorizationException::class)]
    public function update(QaMessage $qaMessage, UpdateQaMessageRequest $request): QaMessageResource
    {
        $this->authorize('update', $qaMessage);
        $qaMessage->update($request->validated());
        return $this->show($qaMessage->loadMissing(self::load_relation));
    }
}