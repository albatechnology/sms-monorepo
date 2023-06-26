<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Exceptions\LeadIsUnassignedException;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\Activity\CreateActivityRequest;
use App\Http\Requests\API\V1\Activity\UpdateActivityRequest;
use App\Http\Requests\API\V1\Activity\UploadImageOfActivityRequest;
use App\Http\Resources\V1\Activity\ActivityCommentResource;
use App\Http\Resources\V1\Activity\ActivityResource;
use App\Models\Activity;
use App\Models\ActivityBrandValue;
use App\Models\ActivityComment;
use App\Models\User;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\RequestBodies\Custom\ImageRequestBody;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ActivityController extends BaseApiController
{
    const load_relation = ['lead', 'user', 'customer', 'latestComment.user', 'order', 'brands', 'activity_brand_values'];

    /**
     * Get activity
     *
     * Returns activity by id
     *
     * @param Activity $activity
     * @return  ActivityResource
     */
    #[CustomOpenApi\Operation(id: 'activityShow', tags: [Tags::Activity, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class, statusCode: 200)]
    public function show(Activity $activity): ActivityResource
    {
        return new ActivityResource($activity->loadMissing(self::load_relation));
    }

    /**
     * Show all activity posted by user
     *
     * Sales will get all activities directly created by him.
     * Supervisor will get all activities created by its supervised sales.
     * Director will get all activities scoped to its active channel setting.
     *
     */
    #[CustomOpenApi\Operation(id: 'activityIndex', tags: [Tags::Activity, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Activity::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class, isCollection: true)]
    public function index()
    {

        $query = function ($query) {

            // we want to override the tenanted scope to ignore the active channel

            $user = tenancy()->getUser();
            $query->tenanted()->with(self::load_relation);

            if ($user->is_sales || $user->is_supervisor) {
                $query = $query->whereIn('user_id', User::descendantsAndSelf($user->id)->pluck('id'));
            }

            return $query;
        };

        return CustomQueryBuilder::buildResource(Activity::class, ActivityResource::class, $query);
    }

    /**
     * Show create product activity
     *
     * Show the validation rules for creating activity
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreateActivityRequest::frontEndRuleResponse();
    }

    /**
     * Create new Activity
     *
     * Create a new activity
     *
     * @param CreateActivityRequest $request
     * @return ActivityResource
     */
    #[CustomOpenApi\Operation(id: 'activityStore', tags: [Tags::Activity, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateActivityRequest::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class, statusCode: 201)]
    public function store(CreateActivityRequest $request): ActivityResource
    {
        $data = array_merge($request->validated(), [
            "user_id"    => tenancy()->getUser()->id
        ]);

        DB::beginTransaction();
        try {
            $activity = Activity::create($data);

            if (isset($data['estimations'])) {
                $estimations = collect($data['estimations']);
                $activity->brands()->sync($estimations->pluck('product_brand_id')->toArray());

                $estimations->each(function ($estimation) use ($activity) {
                    ActivityBrandValue::create([
                        'user_id' => auth()->id(),
                        'activity_id' => $activity->id,
                        'lead_id' => $activity->lead_id,
                        'product_brand_id' => $estimation['product_brand_id'],
                        'estimated_value' => $estimation['estimated_value'],
                    ]);
                });

                $activity->update(['estimated_value' => $estimations->sum('estimated_value') ?? 0]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new LeadIsUnassignedException();
        }

        return $this->show($activity->refresh());
    }

    /**
     * Delete Activity
     *
     * Delete a activity by its id
     *
     * @param Activity $activity
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityDestroy', tags: [Tags::Activity, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function destroy(Activity $activity)
    {
        $activity->checkTenantAccess()->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit activity rules
     *
     * Show the validation rules for editing activity
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateActivityRequest::frontEndRuleResponse();
    }

    /**
     * Update a activity
     *
     * Update a given activity
     *
     * @param Activity $activity
     * @param UpdateActivityRequest $request
     * @return ActivityResource
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'activityUpdate', tags: [Tags::Activity, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateActivityRequest::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class)]
    public function update(Activity $activity, UpdateActivityRequest $request)
    {
        // $activity->checkTenantAccess()->update($request->except('estimations'));
        // $activity->brands()->sync($request->get('brand_ids') ?? []);
        // if ($activity->child) return response()->json(['error' => 'Can\'t update activity where has order']);
        DB::beginTransaction();
        try {
            $activity->checkTenantAccess()->update($request->except('estimations'));

            // if (isset($request->estimations)) {
            //     foreach ($request->estimations as $estimation) {
            //         $activity->brands()->where('product_brand_id', $estimation['product_brand_id'])->update(['estimated_value' => $estimation['estimated_value']]);
            //     }
            //     $activity->update(['estimated_value' => collect($request->estimations)->sum('estimated_value') ?? 0]);
            // }

            if (isset($request->estimations)) {
                $estimations = collect($request->estimations);
                $activity->brands()->sync($estimations->pluck('product_brand_id')->toArray() ?? []);

                $estimations->each(function ($estimation) use ($activity) {
                    $brandValue = ActivityBrandValue::where('user_id', auth()->id())->where('lead_id', $activity->lead_id)->where('product_brand_id', $estimation['product_brand_id'])->first();

                    if($brandValue){
                        $brandValue->update(['estimated_value' => $estimation['estimated_value']]);
                    } else {
                        ActivityBrandValue::create([
                            'user_id' => $activity->user_id,
                            'lead_id' => $activity->lead_id,
                            'product_brand_id' => $estimation['product_brand_id'],
                            'estimated_value' => $estimation['estimated_value'],
                        ]);
                    }
                });
                $activity->update(['estimated_value' => $estimations->sum('estimated_value') ?? 0]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return $this->show($activity->refresh());
    }

    /**
     * Show all activity comments of an activity.
     *
     * Show all activity comments for a given activity.
     */
    #[CustomOpenApi\Operation(id: 'activityGetComments', tags: [Tags::Customer, Tags::V1, Tags::ActivityComment])]
    #[CustomOpenApi\Parameters(model: ActivityComment::class)]
    #[CustomOpenApi\Response(resource: ActivityCommentResource::class, isCollection: true)]
    public function getActivityComments(int $activity)
    {
        $query = fn ($q) => $q->where('activity_id', $activity)->with(ActivityCommentController::load_relation);
        return CustomQueryBuilder::buildResource(ActivityComment::class, ActivityCommentResource::class, $query);
    }

    public function reportPrepare(Request $request): Builder
    {
        $user = tenancy()->getUser();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
        }

        $activities = Activity::tenanted()->whereNull('activities.order_id')->doesntHave('child')->whereBetween('activities.created_at', [$startDate, $endDate]);
        // if ($request->has('company_id') && $request->company_id != '') $activities = $activities->whereHas('channel', fn ($q) => $q->where('company_id', $request->company_id));
        if ($request->has('channel_id') && $request->channel_id != '') $activities = $activities->where('activities.channel_id', $request->channel_id);

        if ($user->is_director) {
            // $activities = $activities->whereHas('user', fn ($q) => $q->where('type', 2)->where('company_id', $user->company_id));
            $activities = $activities->whereHas('user', fn ($q) => $q->where('type', 2));
        } elseif ($user->is_sales) {
            $activities = $activities->where('activities.user_id', $user->id);
        } elseif ($user->is_supervisor) {
            $activities = $activities->whereHas('user', fn ($q) => $q->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray()));
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $activities = $activities->where('activities.user_id', $request->user_id);
        }
        return $activities;
    }

    public function report(Request $request)
    {
        // $activities = $this->reportPrepare($request);
        // return response()->json($activities->count());
        $user = tenancy()->getUser();

        if($user->is_director){
            // $userIds = DB::table('users')->where('company_id', $user->company_id)->pluck('id')->all();
            $userIds = DB::table('users')->pluck('id')->all();
            $activities = ActivityBrandValue::whereIn('user_id', $userIds ?? []);
        } elseif($user->is_supervisor) {
            $userIds = User::whereDescendantOf($user->id)->whereIsSales()->get(['id'])->pluck('id')->all();
            $activities = ActivityBrandValue::whereIn('user_id', $userIds ?? []);
        } else {
            $activities = ActivityBrandValue::whereMy();
        }

        $activities = $activities->whereActive()->groupBy('lead_id')->get();
        return response()->json($activities->count() ?? 0);
    }

    public function detail(Request $request)
    {
        $user = tenancy()->getUser();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
        }

        $activities = ActivityBrandValue::whereActive();

        // if ($request->has('company_id') && $request->company_id != '') $activities = $activities->whereHas('user', fn ($q) => $q->where('company_id', $request->company_id));
        if ($request->has('channel_id') && $request->channel_id != '') $activities = $activities->whereHas('user', fn ($q) => $q->where('channel_id', $request->channel_id));

        if ($user->is_director) {
            // $activities = $activities->whereHas('user', fn ($q) => $q->where('type', 2)->where('company_id', $user->company_id));
            $activities = $activities->whereHas('user', fn ($q) => $q->where('type', 2));
        } elseif ($user->is_sales) {
            $activities = $activities->where('user_id', $user->id);
        } elseif ($user->is_supervisor) {
            $activities = $activities->whereIn('user_id', $user->getAllChildrenSales()->pluck('id')->toArray());
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $activities = $activities->where('user_id', $request->user_id);
        }

        $activities = $activities->join('users', 'users.id', '=', 'activity_brand_values.user_id')->join('channels', 'channels.id', '=', 'users.channel_id');
        $activities = $activities->selectRaw('user_id, users.name, channels.name as channel, COUNT(DISTINCT activity_brand_values.lead_id) as total_activities')->groupBy('user_id')->paginate($request->per_page ?? 15);
        return response()->json($activities);
    }

    // /**
    //  * Show all activity posted by user
    //  *
    //  * Sales will get all activities directly created by him.
    //  * Supervisor will get all activities created by its supervised sales.
    //  * Director will get all activities scoped to its active channel setting.
    //  *
    //  */
    // #[CustomOpenApi\Operation(id: 'activityDetailUser', tags: [Tags::Activity, Tags::V1])]
    // #[CustomOpenApi\Parameters(model: Activity::class)]
    // #[CustomOpenApi\Response(resource: ActivityResource::class, isCollection: true)]
    // public function detailUser(Request $request, int $user_id)
    // {
    //     $startDate = Carbon::now()->startOfMonth();
    //     $endDate = Carbon::now()->endOfMonth();
    //     if (($request->has('start_at') && $request->start_at != '') && ($request->has('end_at') && $request->end_at != '')) {
    //         $startDate = Carbon::createFromFormat('Y-m-d', $request->start_at)->startOfDay();
    //         $endDate = Carbon::createFromFormat('Y-m-d', $request->end_at)->endOfDay();
    //     }

    //     $query = function ($query) use ($user_id, $startDate, $endDate) {
    //         $query = $query->where('user_id', $user_id)->whereNull('order_id')->doesntHave('child')->whereBetween('created_at', [$startDate, $endDate]);
    //         return $query->with(self::load_relation);
    //     };

    //     return CustomQueryBuilder::buildResource(Activity::class, ActivityResource::class, $query);
    // }


    public function active(int $lead_id){
        $activityValues = ActivityBrandValue::whereMy()->where('lead_id', $lead_id)->get();
        $data = [];
        foreach($activityValues as $a){
            $data[] = [
                'product_brand' => $a->productBrand->name,
                'estimated_value' => rupiah($a->estimated_value),
            ];
        }
        return response()->json($data);
    }

    /**
     * Upload image of activity
     *
     * Upload image of activity
     *
     * @param Activity $activity
     * @param UploadImageOfActivityRequest $request
     * @return JsonResponse
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    #[CustomOpenApi\Operation(id: 'ActivityImageUpload', tags: [Tags::Activity, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Activity::class)]
    #[OpenApi\RequestBody(factory: ImageRequestBody::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function uploadImageOfActivity(
        Activity $activity,
        UploadImageOfActivityRequest $request,
    ): JsonResponse
    {
        $activity->addMedia($request->file('image'))->toMediaCollection('image');

        return response()->json(['message' => 'success']);
    }
}
