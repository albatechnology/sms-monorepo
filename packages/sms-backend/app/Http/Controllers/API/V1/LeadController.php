<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Enums\ActivityStatus;
use App\Enums\LeadStatus;
use App\Enums\UserType;
use App\Exceptions\GenericErrorException;
use App\Exceptions\SalesOnlyActionException;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\Lead\AssignLeadRequest;
use App\Http\Requests\API\V1\Lead\CreateLeadRequest;
use App\Http\Requests\API\V1\Lead\UpdateLeadRequest;
use App\Http\Resources\V1\Lead\LeadCategoryResource;
use App\Http\Resources\V1\Lead\LeadResource;
use App\Http\Resources\V1\Lead\LeadWithLatestActivityResource;
use App\Http\Resources\V1\Lead\SubLeadCategoryResource;
use App\Http\Resources\V1\Product\BaseProductBrandResource;
use App\Models\Channel;
// use App\Models\Customer;
// use App\Models\CustomerVoucher;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\ProductBrand;
use App\Models\ProductBrandLead;
use App\Models\SubLeadCategory;
use App\Models\User;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Parameters\Lead\LeadProductBrandsParameter;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use App\Services\CoreService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
// use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class LeadController extends BaseApiController
{
    const load_relation = ['customer', 'user', 'channel', 'leadCategory', 'subLeadCategory', 'latestActivity', 'productBrands'];

    /**
     * Show all user's lead.
     *
     * The leads displayed depends on the type of the authenticated user:
     * 1. Sales will see all leads that is directly under him
     * 2. Supervisor will see all of his supervised sales' leads
     * 3. Director will see all leads in his active/default channel
     * Will not return unhandled leads.
     */
    #[CustomOpenApi\Operation(id: 'leadIndex', tags: [Tags::Lead, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Lead::class)]
    #[CustomOpenApi\Response(resource: LeadResource::class, isCollection: true)]
    public function index()
    {
        // $query = fn ($q) => $q->customTenanted()->handled()->with(self::load_relation);
        $query = fn ($q) => $q->tenanted()->handled()->with(self::load_relation);
        return CustomQueryBuilder::buildResource(Lead::class, LeadResource::class, $query);
    }

    /**
     * Show all user's lead.
     *
     * The leads displayed depends on the type of the authenticated user:
     * 1. Sales will see all leads that is directly under him
     * 2. Supervisor will see all of his supervised sales' leads
     * 3. Director will see all leads in his active/default channel
     * Will not return unhandled leads.
     */
    #[CustomOpenApi\Operation(id: 'leadList', tags: [Tags::Lead, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Lead::class)]
    #[CustomOpenApi\Response(resource: LeadResource::class, isCollection: true)]
    public function list(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if (($request->has('start_date') && $request->start_date != '') && ($request->has('end_date') && $request->end_date != '')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        }

        $query = function ($q) use ($startDate, $endDate) {
            $activityStatus = match (request()->activity_status) {
                'HOT' => ActivityStatus::HOT(),
                'WARM' => ActivityStatus::WARM(),
                'COLD' => ActivityStatus::COLD(),
                default => ActivityStatus::HOT(),
            };

            // $q->whereHas('leadActivities', fn ($q) => $q->where('status', $activityStatus)->whereCreatedAtRange($startDate, $endDate));


            $q->where('last_activity_status', $activityStatus->value)->whereHas('leadActivities', fn ($q) => $q->where('status', $activityStatus->value)->whereCreatedAtRange($startDate, $endDate));

            if ($activityStatus->is(ActivityStatus::HOT())) {
                $q->whereDoesntHave('leadOrders', fn ($q) => $q->whereDeal($startDate, $endDate));
            }

            // $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', $activityStatus)->whereCreatedAtRange($startDate, $endDate));

            // if ($activityStatus == ActivityStatus::HOT) $q->whereIn('type', [1, 3, 4])->whereHas('leadOrders', fn ($q) => $q->whereNotDeal());

            $userType = request()->user_type ?? null;
            $id = request()->id ?? null;
            $user = user();

            if ($userType && $id) {
                $user = match ($userType) {
                    'hs' => User::findOrFail($id),
                    'bum' => User::findOrFail($id),
                    'store' => Channel::findOrFail($id),
                    'sales' => User::findOrFail($id),
                    'store_leader' => User::findOrFail($id),
                    default => $user,
                };
            }

            if ($user instanceof Channel) {
                $q->where('channel_id', $user->id);
            } elseif ($user->type->is(UserType::DIRECTOR)) {
                // $companyId = request()->company_id ?? $user->company_id;
                $q->whereHas('channel', fn ($q) => $q->where('subscribtion_user_id', $user->subscribtion_user_id));
            } elseif ($user->type->is(UserType::SUPERVISOR)) {
                $q->whereIn('channel_id', $user->channels->pluck('id')?->all() ?? []);
                // $q->whereHas('leadUsers', fn ($q) => $q->where('user_id', $user->id));
            } else {
                // sales
                $q->where('user_id', $user->id)->where('channel_id', $user->channel_id);
            }

            if ($channelId = request()->channel_id) $q->where('channel_id', $channelId);

            if ($productBrandId = request()->product_brand_id) {
                $q->whereHas('activityBrandValues', function ($q2) use ($productBrandId) {
                    $q2->where('product_brand_id', $productBrandId);
                });
            }
            return $q->with(['customer', 'user' => fn ($q) => $q->withTrashed(), 'channel', 'leadCategory', 'subLeadCategory', 'latestActivity']);
        };

        return CustomQueryBuilder::buildResource(Lead::class, LeadResource::class, $query);
    }

    /**
     * Show create product lead
     *
     * Show the validation rules for creating lead
     */
    #[CustomOpenApi\Operation(id: 'leadCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreateLeadRequest::frontEndRuleResponse();
    }

    /**
     * Create new Lead
     *
     * Create a new Lead. Currently only sales are allowed to perform
     * this action. This is because lead must be related to a sales. If
     * we want to allow supervisor to add a new lead, they must pick which
     * sales to assign this sales to (which is not supported yet).
     *
     * @param CreateLeadRequest $request
     * @return LeadWithLatestActivityResource
     * @throws SalesOnlyActionException
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadStore', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateLeadRequest::class)]
    #[CustomOpenApi\Response(resource: LeadWithLatestActivityResource::class, statusCode: 201)]
    #[CustomOpenApi\ErrorResponse(exception: SalesOnlyActionException::class)]
    public function store(CreateLeadRequest $request): LeadWithLatestActivityResource
    {
        // dd($request->all());
        $user = user();
        $data = array_merge($request->validated(), [
            'channel_id' => $user->type->is(UserType::SALES) ? $user->channel_id : ($request->validated()['channel_id'] ?? null),
            'user_id'    => $user->id,
            'status'     => LeadStatus::GREEN(),
            'is_unhandled' => $user->type->is(UserType::SALES) ? false : true,
        ]);

        $productBrandIds = $request->product_brand_ids ?? null;
        $productBrands = ProductBrand::where(function ($q) use ($productBrandIds) {
            if (!is_null($productBrandIds)) $q->whereIn('id', $productBrandIds);
        })->pluck('name', 'id');

        $lead = Lead::create($data);
        $lead->productBrands()->sync($productBrands?->keys()?->all() ?? []);

        // if ($lead?->channel_id && $user->type->isNot(UserType::SALES)) {
        //     $data = $lead->toArray();
        //     foreach ($productBrands as $id => $name) {
        //         $data['parent_id'] = $lead->id;
        //         $data['label'] = $lead->label . ' (' . $name . ')';
        //         $data['product_brand_id'] = $id;
        //         $data['status_history'] = null;
        //         $data['created_at'] = now();
        //         $data['updated_at'] = now();
        //         $data['has_activity'] = 0;
        //         $newLead = Lead::create($data);

        //         $newLead->productBrands()->sync([$id]);
        //     }
        // }

        if (!is_null($lead->channel_id)) $lead->queueStatusChange();
        $lead->refresh()->loadMissing(self::load_relation);

        // if ($request->vouchers && count($request->vouchers) > 0) {
        //     CoreService::createAndAssignVoucher(Customer::findOrFail($request->customer_id), $request->vouchers, $lead?->channel?->company_id ?? $user->company_id);
        // }

        return $this->show($lead);
    }

    /**
     * Get lead
     *
     * Returns lead by id
     *
     * @param Lead $lead
     * @return  LeadWithLatestActivityResource
     */
    #[CustomOpenApi\Operation(id: 'leadShow', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: LeadWithLatestActivityResource::class, statusCode: 200)]
    public function show(Lead $lead): LeadWithLatestActivityResource
    {
        return new LeadWithLatestActivityResource($lead->loadMissing(self::load_relation));
    }

    /**
     * Delete Lead
     *
     * Delete a lead by its id
     *
     * @param Lead $lead
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadDestroy', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function destroy(Lead $lead): JsonResponse
    {
        $lead->checkTenantAccess()->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit lead rules
     *
     * Show the validation rules for editing lead
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateLeadRequest::frontEndRuleResponse();
    }

    /**
     * Update a lead
     *
     * Update a given lead
     *
     * @param Lead $lead
     * @param UpdateLeadRequest $request
     * @return LeadWithLatestActivityResource
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'leadUpdate', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateLeadRequest::class)]
    #[CustomOpenApi\Response(resource: LeadWithLatestActivityResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function update(Lead $lead, UpdateLeadRequest $request): LeadWithLatestActivityResource
    {
        $lead->checkTenantAccess()->update($request->validated());
        return $this->show($lead->refresh()->loadMissing(self::load_relation));
    }

    /**
     * Show all unhandled leads.
     *
     * This endpoint only returns unhandled leads that the authenticated
     * user is able to assign to. (i.e., sales will not be able to see
     * any unhandled leads)
     */
    #[CustomOpenApi\Operation(id: 'leadUnhandledIndex', tags: [Tags::Lead, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Lead::class)]
    #[CustomOpenApi\Response(resource: LeadResource::class, isCollection: true)]
    public function unhandledIndex()
    {
        $user = user();

        $query = function ($q) use ($user) {
            $q->tenanted();

            if ($user->type->is(UserType::SALES)) {
                // 1. same channel
                // 2. sales have one of product_brand_id
                // 3. unhandled
                return $q->whereHas('productBrands', fn ($q) => $q->whereIn('product_brand_id', $user->getMyBrandIds())->where('is_available', 1))
                    ->where('channel_id', $user->channel_id)->whereParent()->unhandled()->with(self::load_relation)->groupBy('leads.id');
            } elseif ($user->type->is(UserType::SUPERVISOR) && $user->supervisor_type_id == 1) {
                return $q->where(fn ($q) => $q->whereNull('channel_id')->orWhereIn('channel_id', $user->channels->pluck('id')))->whereParent()->unhandled()->where('user_id', '!=', $user->id)->with(self::load_relation);
            } else {
                return $q->myLeads()->whereChilds()->unhandled()->assignable()->with(self::load_relation);
            }
        };
        return CustomQueryBuilder::buildResource(Lead::class, LeadResource::class, $query);
    }

    /**
     * Assign an unhandled lead
     *
     * Assign an unhandled lead
     *
     * @param Lead $lead
     * @param AssignLeadRequest $request
     * @return LeadWithLatestActivityResource
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadAssign', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: AssignLeadRequest::class)]
    #[CustomOpenApi\Response(resource: LeadWithLatestActivityResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function assign(Lead $lead, AssignLeadRequest $request)
    {
        $user = user();

        if ($user->is_sales) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_brand_ids' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "message" => "The given data was invalid.",
                    "errors" => $validator->errors()
                ], 422);
            }
        }

        $productBrandIds = $request->product_brand_ids ?? [];

        if ($user->is_sales && $lead->is_parent) {

            $checkAvaibility = ProductBrandLead::where('lead_id', $lead->id)->whereIn('product_brand_id', $productBrandIds)
                ->get()
                ->every(function ($l) {
                    return $l->is_available == 1;
                });

            if (!$checkAvaibility) throw new Exception('Invalid product brand. One of the product brands has been chosen by another sales!');

            $parentLead = $lead;
            $data = $lead->toArray();

            $data['parent_id'] = $lead->id;
            $data['label'] = $lead->label;
            $data['status_history'] = null;
            $data['created_at'] = now();
            $data['updated_at'] = now();
            $data['has_activity'] = 0;
            $lead = Lead::create($data);

            $lead->productBrands()->sync($productBrandIds);

            $parentLead->updateProductBrandsAvailable($productBrandIds);
        }

        try {
            app(CoreService::class)->assignLeadToUser($lead, $user);
        } catch (Exception $e) {
            throw new GenericErrorException($e->getMessage());
        }

        $lead->refresh();

        if (!$lead->is_parent && is_null($lead->status_change_due_at)) $lead->queueStatusChange();

        // $productBrands = $lead->productBrands;
        // if ($lead?->channel_id && $productBrands->count() && $lead->is_unhandled) {
        //     $data = $lead->toArray();
        //     foreach ($productBrands as $pb) {
        //         $data['parent_id'] = $lead->id;
        //         $data['label'] = $lead->label . ' (' . $pb->name . ')';
        //         $data['product_brand_id'] = $pb->id;
        //         $data['status_history'] = null;
        //         $data['created_at'] = now();
        //         $data['updated_at'] = now();
        //         $data['has_activity'] = 0;
        //         $newLead = Lead::create($data);

        //         $newLead->productBrands()->sync([$pb->id]);
        //     }
        // }

        return $this->show($lead->loadMissing(self::load_relation));
    }

    /**
     * Show all lead categories.
     *
     * Show all lead categories.
     */
    #[CustomOpenApi\Operation(id: 'leadCategories', tags: [Tags::Lead, Tags::V1])]
    #[CustomOpenApi\Parameters(model: LeadCategory::class)]
    #[CustomOpenApi\Response(resource: LeadCategoryResource::class, isCollection: true)]
    public function categories()
    {
        return CustomQueryBuilder::buildResource(LeadCategory::class, LeadCategoryResource::class);
    }

    /**
     * Get sub lead categories.
     *
     * Get sub lead categories.
     */
    #[CustomOpenApi\Operation(id: 'subLeadCategories', tags: [Tags::Lead, Tags::V1])]
    #[CustomOpenApi\Parameters(model: SubLeadCategory::class)]
    #[CustomOpenApi\Response(resource: SubLeadCategoryResource::class, isCollection: true)]
    public function subCategories(LeadCategory $leadCategory)
    {
        $query = fn ($q) => $q->where('lead_category_id', $leadCategory->id);
        return CustomQueryBuilder::buildResource(SubLeadCategory::class, SubLeadCategoryResource::class, $query);
    }

    /**
     * Show all leads by user leads where related with activity_brand_values value where active(order_id = null)
     *
     * Show all leads by user leads where related with activity_brand_values value where active(order_id = null)
     */
    #[CustomOpenApi\Operation(id: 'activityReport', tags: [Tags::Lead, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Lead::class)]
    #[CustomOpenApi\Response(resource: LeadResource::class, isCollection: true)]
    public function activityReport(int $user_id)
    {
        $leadIds = \Illuminate\Support\Facades\DB::table('activity_brand_values')->select('lead_id')->whereNull('order_id')->where('user_id', $user_id)->groupBy('lead_id')->pluck('lead_id');

        $query = fn ($q) => $q->whereIn('id', $leadIds ?? [])->with(self::load_relation);
        return CustomQueryBuilder::buildResource(Lead::class, LeadResource::class, $query);
    }

    /**
     * Get user available lead product brands based on user product brand ids
     *
     * Get user available lead product brands based on user product brand ids
     *
     * @return BaseProductBrandResource
     */
    #[CustomOpenApi\Operation(id: 'leadProductBrands', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: LeadProductBrandsParameter::class)]
    #[CustomOpenApi\Response(resource: BaseProductBrandResource::class)]
    public function productBrands(Lead $lead)
    {
        $user = user();
        $userProductBrandIds = [];
        if ($user->is_sales) $userProductBrandIds = $user->getMyBrandIds();

        $isAvailableProductBrands = request()->available_product_brands;

        $data = QueryBuilder::for(ProductBrand::class)
            ->whereHas('productBrandLeads', function ($q) use ($lead, $userProductBrandIds, $isAvailableProductBrands) {
                $q->where('lead_id', $lead->id)->whereIn('product_brand_id', $userProductBrandIds);
                if ($isAvailableProductBrands) $q->where('is_available', 1);
            })
            ->allowedFilters([
                // AllowedFilter::callback('avalilable_product_brands', function ($q, $value) use ($userProductBrandIds) {
                //     if ($value == 1 || $value == true) {
                //         $q->whereHas('productBrandLeads', function ($q) use ($userProductBrandIds) {
                //             $q->where('is_available', 1)->whereIn('product_brand_id', $userProductBrandIds);
                //         });
                //     }
                // }),
            ])
            ->simplePaginate();

        return BaseProductBrandResource::collection($data);
    }
}
