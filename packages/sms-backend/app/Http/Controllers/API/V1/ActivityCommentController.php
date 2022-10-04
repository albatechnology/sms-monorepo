<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\ActivityComment\CreateActivityCommentRequest;
use App\Http\Requests\API\V1\ActivityComment\UpdateActivityCommentRequest;
use App\Http\Resources\V1\Activity\ActivityCommentResource;
use App\Models\ActivityComment;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ActivityCommentController extends BaseApiController
{

    const load_relation = ['activity_comment', 'user'];

    /**
     * Show user's activity comment.
     *
     * Show all activity comments posted by this user
     *
     */
    #[CustomOpenApi\Operation(id: 'activityCommentIndex', tags: [Tags::ActivityComment, Tags::V1])]
    #[CustomOpenApi\Parameters(model: ActivityComment::class)]
    #[CustomOpenApi\Response(resource: ActivityCommentResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(
            ActivityComment::class,
            ActivityCommentResource::class,
            fn ($query) => $query->where('user_id', tenancy()->getUser()->id)->with(['activity_comment'])
        );
    }

    /**
     * Get create ActivityComment rule
     *
     * Show the validation rules for creating activityComment
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityCommentCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreateActivityCommentRequest::frontEndRuleResponse();
    }

    /**
     * Create new ActivityComment
     *
     * Create a new activityComment
     *
     * @param CreateActivityCommentRequest $request
     * @return ActivityCommentResource
     */
    #[CustomOpenApi\Operation(id: 'activityCommentStore', tags: [Tags::ActivityComment, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateActivityCommentRequest::class)]
    #[CustomOpenApi\Response(resource: ActivityCommentResource::class, statusCode: 201)]
    public function store(CreateActivityCommentRequest $request): ActivityCommentResource
    {
        $data = array_merge(
            $request->validated(),
            [
                'user_id' => tenancy()->getUser()->id,
            ]
        );
        return $this->show(ActivityComment::create($data)->loadMissing(['activity_comment']));
    }

    /**
     * Get activityComment
     *
     * Returns activityComment by id
     *
     * @param ActivityComment $activityComment
     * @return  ActivityCommentResource
     */
    #[CustomOpenApi\Operation(id: 'activityCommentShow', tags: [Tags::ActivityComment, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ActivityCommentResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(ActivityComment $activityComment)
    {
        return new ActivityCommentResource($activityComment->loadMissing(['activity_comment']));
    }

    /**
     * Delete ActivityComment
     *
     * Delete a activityComment by its id
     *
     * @param ActivityComment $activityComment
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityCommentDestroy', tags: [Tags::ActivityComment, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function destroy(ActivityComment $activityComment)
    {
        $activityComment->checkTenantAccess()->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit activityComment rules
     *
     * Show the validation rules for editing activityComment
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityCommentEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateActivityCommentRequest::frontEndRuleResponse();
    }

    /**
     * Update a activityComment
     *
     * Update a given activityComment
     *
     * @param ActivityComment $activityComment
     * @param UpdateActivityCommentRequest $request
     * @return ActivityCommentResource
     */
    #[CustomOpenApi\Operation(id: 'activityCommentUpdate', tags: [Tags::ActivityComment, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateActivityCommentRequest::class)]
    #[CustomOpenApi\Response(resource: ActivityCommentResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function update(ActivityComment $activityComment, UpdateActivityCommentRequest $request): ActivityCommentResource
    {
        $activityComment->checkTenantAccess()->update($request->validated());
        return $this->show($activityComment->loadMissing(['activity_comment']));
    }
}
