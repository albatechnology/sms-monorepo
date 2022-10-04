<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Exceptions\GenericAuthorizationException;
use App\Http\Requests\API\V1\QaTopic\CreateQaTopicRequest;
use App\Http\Requests\API\V1\QaTopic\UpdateQaTopicRequest;
use App\Http\Resources\V1\Qa\QaMessageResource;
use App\Http\Resources\V1\Qa\QaTopicResource;
use App\Http\Resources\V1\Qa\QaTopicWithUsersResource;
use App\Models\QaMessage;
use App\Models\QaTopic;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class QaTopicController extends BaseApiController
{
    const load_relation = ['creator', 'latestMessage.sender'];

    /**
     * Show all qa topic.
     *
     * Show all qa topic
     *
     */
    #[CustomOpenApi\Operation(id: 'QaTopicIndex', tags: [Tags::QaTopic, Tags::V1])]
    #[CustomOpenApi\Parameters(model: QaTopic::class)]
    #[CustomOpenApi\Response(resource: QaTopicResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(QaTopic::class, QaTopicResource::class, fn($q) => $q->involved()->with(self::load_relation));
    }

    /**
     * Get create qa topic rule
     *
     * Show the validation rules for creating qa topic
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'QaTopicCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreateQaTopicRequest::frontEndRuleResponse();
    }

    /**
     * Create new Qa topic
     *
     * Create a new qa topic
     *
     * @param CreateQaTopicRequest $request
     * @return QaTopicResource
     */
    #[CustomOpenApi\Operation(id: 'QaTopicStore', tags: [Tags::QaTopic, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateQaTopicRequest::class)]
    #[CustomOpenApi\Response(resource: QaTopicResource::class, statusCode: 201)]
    public function store(CreateQaTopicRequest $request): QaTopicResource
    {
        $topic = QaTopic::create(
            [
                'creator_id' => user()->id,
                'subject'    => $request->subject,
            ]
        );

        $user_ids = collect($request->users)->push(user()->id)->values()->unique();
        $topic->users()->syncWithoutDetaching($user_ids);

        return new QaTopicResource($topic->loadMissing([...self::load_relation, 'subscribers'])->refresh());
    }

    /**
     * Get qa topic
     *
     * Returns qa topic by id
     *
     * @param QaTopic $qaTopic
     * @return  QaTopicWithUsersResource
     */
    #[CustomOpenApi\Operation(id: 'QaTopicShow', tags: [Tags::QaTopic, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: QaTopicWithUsersResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: GenericAuthorizationException::class)]
    public function show(QaTopic $qaTopic): QaTopicWithUsersResource
    {
        $this->authorize('show', $qaTopic);
        return new QaTopicWithUsersResource($qaTopic->loadMissing([...self::load_relation, 'users']));
    }

    /**
     * Delete Qa topic
     *
     * Delete a qa topic by its id
     *
     * @param QaTopic $qaTopic
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'QaTopicDestroy', tags: [Tags::QaTopic, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    #[CustomOpenApi\ErrorResponse(exception: GenericAuthorizationException::class)]
    public function destroy(QaTopic $qaTopic)
    {
        $this->authorize('destroy', $qaTopic);
        $qaTopic->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit qa topic rules
     *
     * Show the validation rules for editing qa topic
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'QaTopicEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateQaTopicRequest::frontEndRuleResponse();
    }

    /**
     * Update a qa topic
     *
     * Update a given qa topic
     *
     * @param QaTopic $qaTopic
     * @param UpdateQaTopicRequest $request
     * @return QaTopicResource
     * @throws AuthorizationException
     */
    #[CustomOpenApi\Operation(id: 'QaTopicUpdate', tags: [Tags::QaTopic, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateQaTopicRequest::class)]
    #[CustomOpenApi\Response(resource: QaTopicWithUsersResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: GenericAuthorizationException::class)]
    public function update(QaTopic $qaTopic, UpdateQaTopicRequest $request): QaTopicWithUsersResource
    {
        $this->authorize('update', $qaTopic);
        $qaTopic->update(['subject' => $request->subject]);

        $user_ids = collect($request->users)->push(user()->id)->values()->unique();
        $qaTopic->users()->sync($user_ids);

        return $this->show($qaTopic->loadMissing(self::load_relation)->refresh());
    }

    /**
     * Get all Qa Messages of a Qa Topic
     *
     * Get all Qa Messages of a Qa Topic, this include Qa messages posted by
     * users other than the authenticated user.
     *
     * @param int $topic
     * @return QaTopicResource
     */
    #[CustomOpenApi\Operation(id: 'QaTopicGetQaMessages', tags: [Tags::QaTopic, Tags::QaMessage, Tags::V1])]
    #[CustomOpenApi\Parameters(model: QaMessage::class)]
    #[CustomOpenApi\Response(resource: QaMessageResource::class, isCollection: true)]
    public function messages(int $topic)
    {
        $query = fn($query) => $query->where('topic_id', $topic)->with(QaMessageController::load_relation);
        return CustomQueryBuilder::buildResource(QaMessage::class, QaMessageResource::class, $query);
    }
}