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
use App\Http\Requests\API\V1\Lead\CreateLeadSmsRequest;
use App\Http\Requests\API\V1\Lead\UpdateLeadRequest;
use App\Http\Requests\API\V1\Lead\UpdateLeadSmsRequest;
use App\Http\Resources\V1\Lead\LeadCategoryResource;
use App\Http\Resources\V1\Lead\LeadResource;
use App\Http\Resources\V1\Lead\LeadSmsResource;
use App\Http\Resources\V1\Lead\LeadWithLatestActivityResource;
use App\Http\Resources\V1\Lead\SubLeadCategoryResource;
use App\Models\Channel;
use App\Models\Lead;
use App\Models\LeadCategory;
use App\Models\SubLeadCategory;
use App\Models\User;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use App\Services\CoreService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class LeadController extends BaseApiController
{
    const load_relation = ['customer', 'user', 'channel', 'leadCategory', 'subLeadCategory', 'latestActivity'];

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
        $query = fn ($q) => $q->customTenanted()->handled()->with(self::load_relation);
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
                'HOT' => ActivityStatus::HOT,
                'WARM' => ActivityStatus::WARM,
                'COLD' => ActivityStatus::COLD,
                default => ActivityStatus::HOT,
            };
            $q->whereHas('leadActivities', fn ($q2) => $q2->where('status', $activityStatus)->whereCreatedAtRange($startDate, $endDate));

            $userType = request()->user_type ?? null;
            $id = request()->id ?? null;

            if ($userType && $id) {
                $user = match ($userType) {
                    'bum' => User::findOrFail($id),
                    'store' => Channel::findOrFail($id),
                    'sales' => User::findOrFail($id),
                    default => $user,
                };
            }

            if ($user instanceof Channel) {
                $q->where('channel_id', $user->id);
            } elseif ($user->type->is(UserType::DIRECTOR)) {
                $companyIds = $user->company_ids ?? $user->companies->pluck('id')->all();
                $q->whereIn('company_id', $companyIds);
            } elseif ($user->type->is(UserType::SUPERVISOR)) {
                $q->whereIn('channel_id', $user->channels->pluck('id')->all());
            } else {
                // sales
                $q->where('user_id', $user->id);
            }

            if ($channelId = request()->channel_id) $q->where('channel_id', $channelId);

            if ($productBrandId = request()->product_brand_id) {
                $q->whereHas('activityBrandValues', function ($q2) use ($productBrandId) {
                    $q2->where('product_brand_id', $productBrandId);
                });
            }
            return $q->with(self::load_relation);
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
        $data = array_merge($request->validated(), [
            'channel_id' => tenancy()->getActiveTenant()->id,
            'user_id'    => tenancy()->getUser()->id,
            'status'     => LeadStatus::GREEN()
        ]);

        $lead = Lead::create($data);
        $lead->queueStatusChange();
        $lead->refresh()->loadMissing(self::load_relation);

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
        $query = function ($q) {
            return $q->myLeads()->unhandled()->assignable()->with(self::load_relation);
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
    public function assign(Lead $lead, AssignLeadRequest $request): LeadWithLatestActivityResource
    {
        try {
            app(CoreService::class)->assignLeadToUser($lead, $request->getUser());
        } catch (Exception $e) {
            throw new GenericErrorException($e->getMessage());
        }
        return $this->show($lead->refresh()->loadMissing(self::load_relation));
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
     * Show all sms user's lead.
     *
     * The leads displayed depends on the type of the authenticated user:
     * 1. Sales will see all leads that is directly under him
     * 2. Supervisor will see all of his supervised sales' leads
     * 3. Director will see all leads in his active/default channel
     * Will not return unhandled leads.
     */
    #[CustomOpenApi\Operation(id: 'leadSms', tags: [Tags::Lead, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Lead::class)]
    #[CustomOpenApi\Response(resource: LeadSmsResource::class, isCollection: true)]
    public function leadSms()
    {
        $user = auth()->user();
        $load_relations = ['userSms', 'customer', 'productBrand', 'smsChannel'];
        if ($user->type->is(UserType::SALES_SMS)) {
            $query = fn ($q) => $q->where('user_sms_id', $user->id)->with($load_relations);
        } else {
            $query = fn ($q) => $q->whereHas('userSms', fn ($q) => $q->where('supervisor_id', $user->id))->with($load_relations);
        }
        return CustomQueryBuilder::buildResource(Lead::class, LeadSmsResource::class, $query);
    }

    /**
     * Get lead sms
     *
     * Returns lead sms by id
     *
     * @param int $leadId
     * @return  LeadWithLatestActivityResource
     */
    #[CustomOpenApi\Operation(id: 'showleadSms', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: LeadSmsResource::class, statusCode: 200)]
    public function showLeadSms(int $leadId)
    {
        $user = auth()->user();
        if ($user->type->is(UserType::SALES_SMS)) {
            $lead = Lead::where('id', $leadId)->where('user_sms_id', $user->id)->first();
        } else {
            $lead = Lead::where('id', $leadId)->whereHas('userSms', fn ($q) => $q->where('supervisor_id', $user->id))->first();
        }
        if (!$lead) return response()->json(['message' => 'Data not found'], 404);
        return new LeadSmsResource($lead->loadMissing(['userSms', 'customer', 'productBrand', 'smsChannel']));
    }

    /**
     * Get order deals
     *
     * Returns order deals by lead id
     *
     * @param int $leadId
     * @return JsonResponse
     */
    #[CustomOpenApi\Operation(id: 'dealsleadSms', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function dealsSms(int $leadId)
    {
        $user = auth()->user();
        if ($user->type->is(UserType::SALES_SMS)) {
            $order = \App\Models\Order::with('lead')->where('lead_id', $leadId)->where('payment_status', \App\Enums\OrderPaymentStatus::SETTLEMENT)->whereHas('lead', fn ($q) => $q->where('user_sms_id', $user->id))->selectRaw('created_at,total_price,lead_id')->first();
        } else {
            $order = \App\Models\Order::with('lead')->where('lead_id', $leadId)->where('payment_status', \App\Enums\OrderPaymentStatus::SETTLEMENT)->whereHas('lead', fn ($q) => $q->whereHas('userSms', fn ($q) => $q->where('supervisor_id', $user->id)))->selectRaw('created_at,total_price,lead_id')->first();
        }

        if (!$order) return response()->json(['message' => 'Data not found'], 404);
        return response()->json([
            'sales' => $order->lead?->userSms?->name ?? '-',
            'channel' => $order->lead?->userSms?->smsChannel?->name ?? '-',
            'created_at' => date('d M Y', strtotime($order->created_at)),
            'total_price' => rupiah($order->total_price),
        ]);
    }

    /**
     * Create new Lead SMS
     *
     * Create a new Lead SMS. Currently only sales are allowed to perform
     * this action. This is because lead must be related to a sales.
     *
     * @param CreateLeadSmsRequest $request
     * @return LeadSmsResource
     * @throws SalesOnlyActionException
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadStoreSms', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateLeadSmsRequest::class)]
    #[CustomOpenApi\Response(resource: LeadSmsResource::class, statusCode: 201)]
    #[CustomOpenApi\ErrorResponse(exception: SalesOnlyActionException::class)]
    public function storeSms(CreateLeadSmsRequest $request)
    {
        $user = auth()->user();
        if ($user->type->is(UserType::SUPERVISOR_SMS)) return response()->json(['success' => false, 'message' => 'Only sales can create lead']);

        $customer = \App\Models\Customer::where('email', $request->email)->where('phone', $request->phone)->first();

        if (!$customer) {
            if (\App\Models\Customer::where('email', $request->email)->first()) return response()->json(['success' => false, 'message' => 'Email is already used by other customer'], 422);
            if (\App\Models\Customer::where('phone', $request->phone)->first()) return response()->json(['success' => false, 'message' => 'Phone number is already used by other customer'], 422);
        }

        $is_new_customer = 0;

        $channel_id = $user->smsChannel?->channel_id;
        if (!$channel_id) {
            return response()->json(['success' => false, 'message' => 'Channel not found'], 422);
        }

        $movesStoreLeader = User::where('type', UserType::SUPERVISOR)->where('supervisor_type_id', 1)->where('channel_id', $channel_id)->first();
        if (!$channel_id) {
            return response()->json(['success' => false, 'message' => 'Moves store leader not found'], 422);
        }

        $bum = $movesStoreLeader->supervisor;
        if (!$bum) {
            return response()->json(['success' => false, 'message' => 'Moves BUM not found'], 422);
        }

        if ($customer) {
            $customer->update([
                'user_sms_id' => $user->id
            ]);
        } else {
            $customer = new \App\Models\Customer;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->first_name = $request->name;
            $customer->source = \App\Enums\CustomerSource::SMS;
            $customer->user_sms_id = $user->id;
            if (!$customer->save()) return response()->json(['success' => false, 'message' => 'Error create customer'], 422);

            $address = \App\Models\Address::create([
                'address_line_1' => $request->address,
                'type' => \App\Enums\AddressType::ADDRESS,
                'customer_id' => $customer->id,
            ]);
            $customer->update(['default_address_id' => $address->id]);
            $is_new_customer = 1;
        }

        $lead = Lead::create([
            'label' => $request->label ?? null,
            'type' => \App\Enums\LeadType::LEADS,
            'status' => \App\Enums\LeadStatus::GREEN,
            'is_new_customer' => $is_new_customer,
            'is_unhandled' => 1,
            'user_id' => $bum->id,
            'customer_id' => $customer->id,
            'channel_id' => $channel_id,
            'interest' => $request->note ?? null,
            'user_sms_id' => $user->id,
            'sms_channel_id' => $user->channel_id,
            'product_brand_id' => $request->product_brand_id ?? null,
            'voucher' => $request->voucher ?? null,
        ]);
        if ($request->hasFile('voucher_image')) $lead->addMedia($request->file('voucher_image'))->toMediaCollection('photo');
        return new LeadSmsResource($lead->loadMissing(['userSms']));
    }

    /**
     * Update a lead SMS
     *
     * Update a given lead SMS
     *
     * @param UpdateLeadSmsRequest $request
     * @param int $id
     * @return LeadSmsResource
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadUpdateSms', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateLeadSmsRequest::class)]
    #[CustomOpenApi\Response(resource: LeadSmsResource::class)]
    public function UpdateSms(UpdateLeadSmsRequest $request, int $id)
    {
        $user = auth()->user();
        if ($user->type->is(UserType::SUPERVISOR_SMS)) return response()->json(['success' => false, 'message' => 'Only sales can update lead']);

        $lead = Lead::findOrFail($id);
        $customer = $lead->customer;
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->first_name = $request->name;
        $customer->save();

        $address = $customer->defaultCustomerAddress ? $customer->defaultCustomerAddress : $customer->customerAddresses->first();
        if (!$address) $address = new \App\Models\Address;
        $address->address_line_1 = $request->address;
        $address->type = \App\Enums\AddressType::ADDRESS;
        $address->customer_id = $customer->id;
        $address->save();
        $customer->update(['default_address_id' => $address->id]);

        $lead->update([
            'label' => $request->label ?? null,
            'interest' => $request->note ?? null,
            'product_brand_id' => $request->product_brand_id ?? null,
            'voucher' => $request->voucher ?? null,
        ]);

        if ($request->hasFile('voucher_image')) {
            if (count($lead->voucher_image) > 0) {
                foreach ($lead->voucher_image as $media) {
                    $media->delete();
                }
            }

            $lead->addMedia($request->file('voucher_image'))->toMediaCollection('photo');
        }

        return new LeadSmsResource($lead->refresh()->loadMissing(['userSms']));
    }
}
