<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\SupervisorDoesNotExistException;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\User\ChangePasswordRequest;
use App\Http\Resources\V1\User\SupervisorTypeResource;
use App\Http\Resources\V1\User\UserResource;
use App\Models\Channel;
use App\Models\SupervisorType;
use App\Models\User;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class UserController extends BaseApiController
{
    public const load_relation = ['supervisorType', 'company'];

    /**
     * Get logged in user detail
     *
     * Get the user resource of the currently logged in user
     *
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'userMe', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: UserResource::class)]
    public function me(): UserResource
    {
        return new UserResource(auth()->user()->loadMissing(self::load_relation));
    }

    /**
     * Get detail of supervisor
     *
     * Get the detail of logged in user's supervisor (direct parent)
     *
     * @return mixed
     * @throws SupervisorDoesNotExistException
     */
    #[CustomOpenApi\Operation(id: 'userSupervisor', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: UserResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: SupervisorDoesNotExistException::class)]
    public function supervisor(): UserResource
    {
        if (!$supervisor = auth()->user()->supervisor) throw new SupervisorDoesNotExistException();
        return new UserResource($supervisor->loadMissing(self::load_relation));
    }

    /**
     * Get list supervisor types
     *
     * Get list supervisor types
     *
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'userSupervisorTypes', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: SupervisorTypeResource::class, isCollection: true)]
    public function supervisorTypes()
    {
        return CustomQueryBuilder::buildResource(SupervisorType::class, SupervisorTypeResource::class);
    }

    /**
     * Set default channel
     *
     * Set the default channel for this user. Default channel must be set
     * before user can access tenanted resources.
     *
     * @param Channel $channel
     * @return mixed
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'userSetDefaultChannel', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function channel(Channel $channel)
    {
        if (!tenancy()->hasTenantId($channel->id)) {
            throw new UnauthorisedTenantAccessException();
        }

        auth('sanctum')->user()->update(['channel_id' => $channel->id]);

        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Get user detail
     *
     * Currently allow access to all users on the system
     *
     * @param User $user
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'userShow', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: UserResource::class)]
    public function show(User $user): UserResource
    {
        return new UserResource($user->loadMissing(self::load_relation));
    }

    /**
     * Show all users.
     *
     * Show all users registered in the system. This is currently unfiltered, but in the
     * future we may filter to limit user visibility.
     */
    #[CustomOpenApi\Operation(id: 'userIndex', tags: [Tags::User, Tags::V1])]
    #[CustomOpenApi\Parameters(model: User::class)]
    #[CustomOpenApi\Response(resource: UserResource::class, isCollection: true)]
    public function index()
    {
        $query = fn($q) => $q->with(self::load_relation);
        return CustomQueryBuilder::buildResource(User::class, UserResource::class, $query);
    }

    /**
     * Show all supervised users.
     *
     * Show all users that is supervised by this user (all child and grandchild nodes).
     * Does not include data of currently logged in user and data of supervisor.
     *
     */
    #[CustomOpenApi\Operation(id: 'userSupervised', tags: [Tags::User, Tags::V1])]
    #[CustomOpenApi\Parameters(model: User::class)]
    #[CustomOpenApi\Response(resource: UserResource::class, isCollection: true)]
    public function supervised()
    {
        $filter = fn($query) => $query->whereDescendantOf(auth()->user())->with(self::load_relation);
        return CustomQueryBuilder::buildResource(User::class, UserResource::class, $filter);
    }

    /**
     * Show list of available users to look into the report.
     *
     * Front end should filter by company id
     *
     */
    #[CustomOpenApi\Operation(id: 'userListForReport', tags: [Tags::User, Tags::V1])]
    #[CustomOpenApi\Parameters(model: User::class)]
    #[CustomOpenApi\Response(resource: UserResource::class, isCollection: true)]
    public function indexUserForReport()
    {
        $filter = function ($query) {

           $query->with(self::load_relation);

            // full access
            if (user()->is_admin || user()->is_director) {
                return $query;
            }

            // otherwise, return self and the supervised
            return $query->whereIn('id', User::descendantsAndSelf(user()->id)->pluck('id'));
        };

        return CustomQueryBuilder::buildResource(
            User::class,
            UserResource::class,
            $filter
        );
    }


    /**
     * Change password
     *
     * Change password of currently logged in user
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    #[CustomOpenApi\Operation(id: 'userChangePassword', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: ChangePasswordRequest::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function changePassword(ChangePasswordRequest $request)
    {
        auth()->user()->update($request->validated());
        return GenericSuccessMessageResponse::getResponse();
    }
}
