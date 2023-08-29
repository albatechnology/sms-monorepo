<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Enums\OrderStatus;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\Order\ApproveOrderRequest;
use App\Http\Requests\API\V1\Order\CloneOrderRequest;
use App\Http\Requests\API\V1\Order\CreateOrderRequest;
use App\Http\Requests\API\V1\Order\PreviewOrderRequest;
use App\Http\Requests\API\V1\Order\UpdateOrderRequest;
use App\Http\Resources\V1\Order\OrderResource;
use App\Models\Order;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\Pipes\Order\AddAdditionalDiscount;
use App\Pipes\Order\AddAdditionalFees;
use App\Pipes\Order\ApplyDiscount;
// use App\Pipes\Order\ApplyVouchers;
// use App\Pipes\Order\CalculateCartDemand;
use App\Pipes\Order\CheckExpectedOrderPrice;
use App\Pipes\Order\FillOrderAttributes;
use App\Pipes\Order\FillOrderRecord;
use App\Pipes\Order\MakeOrderLines;
use App\Pipes\Order\Update\UpdateAdditionalDiscount;
use App\Pipes\Order\Update\UpdateApplyDiscount;
// use App\Pipes\Order\Update\UpdateApplyVouchers;
use App\Pipes\Order\Update\UpdateOrderLines;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use PDF;
use Illuminate\Http\Request;

#[OpenApi\PathItem]
class OrderController extends BaseApiController
{
    const load_relation = ['customer', 'order_details', 'user', 'channel', 'approvedBy'];

    /**
     * Get order
     *
     * Returns order by id
     *
     * @param Order $order
     * @return  OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderShow', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(Order $order)
    {
        $order->payment_status_for_invoice = $order->getPaymentStatusForInvoice();
        return new OrderResource($order->loadMissing(self::load_relation)->checkTenantAccess());
    }

    /**
     * Show all order.
     *
     * Show all order
     *
     */
    #[CustomOpenApi\Operation(id: 'OrderIndex', tags: [Tags::Order, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Order::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(Order::class, OrderResource::class, fn ($query) => $query->with(self::load_relation)->tenanted());
    }

    /**
     * Create new Order
     *
     * Create a new order
     *
     * @param CreateOrderRequest $request
     * @return OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderStore', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateOrderRequest::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 201)]
    public function store(CreateOrderRequest $request): OrderResource
    {
        $order = app(OrderService::class)->processOrder(Order::make(['raw_source' => $request->all()]));
        $order->refresh()->loadMissing(self::load_relation);

        return new OrderResource($order);
    }

    /**
     * Order preview update
     *
     * Creates a dummy order for preview update purposes. Use this endpoint to check
     * how the order will look like with the discount applied.
     *
     * @param Order $order
     * @param UpdateOrderRequest $request
     * @return OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderPreviewUpdate', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateOrderRequest::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 200)]
    public function previewUpdate(UpdateOrderRequest $request, Order $order): OrderResource
    {
        // if ($order->shipment_status->isNot(\App\Enums\OrderShipmentStatus::NONE) || $order->payment_status->isNot(\App\Enums\OrderPaymentStatus::NONE)) {
        if ($order->payment_status->isNot(\App\Enums\OrderPaymentStatus::NONE)) {
            return $this->show($order->refresh()->loadMissing(self::load_relation));
        }

        $order = app(Pipeline::class)
            ->send($order)
            ->through(
                [
                    UpdateOrderLines::class,
                    UpdateApplyDiscount::class,
                    // UpdateApplyVouchers::class,
                    // CalculateCartDemand::class,
                    UpdateAdditionalDiscount::class,
                    AddAdditionalFees::class,
                ]
            )
            ->thenReturn();

        $order->loadMissing(self::load_relation);

        return new OrderResource($order);
    }

    /**
     * Update an Order
     *
     * Update an order
     *
     * @param Order $order
     * @param UpdateOrderRequest $request
     * @return OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderUpdate', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateOrderRequest::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 201)]
    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        // if ($order->shipment_status->isNot(\App\Enums\OrderShipmentStatus::NONE) || $order->payment_status->isNot(\App\Enums\OrderPaymentStatus::NONE)) {
        //     return $this->show($order->refresh()->loadMissing(self::load_relation));
        // }

        $order = app(Pipeline::class)
            ->send($order)
            ->through(
                [
                    UpdateOrderLines::class,
                    UpdateApplyDiscount::class,
                    // UpdateApplyVouchers::class,
                    // CalculateCartDemand::class,
                    UpdateAdditionalDiscount::class,
                    AddAdditionalFees::class,
                    CheckExpectedOrderPrice::class,
                    \App\Pipes\Order\Admin\SaveOrder::class,
                    \App\Pipes\Order\SendDiscountApprovalNotification::class,
                ]
            )
            ->thenReturn();

        $order->refresh()->loadMissing(self::load_relation);

        return new OrderResource($order);
    }

    /**
     * Clone an order
     *
     * This will clone and cancel a given order.
     * Newly cloned order will be returned as response.
     *
     * @param Order $order
     * @param CloneOrderRequest $request
     * @return OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderClone', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CloneOrderRequest::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 201)]
    public function clone(Order $order, CloneOrderRequest $request): OrderResource
    {
        app(OrderService::class)->cancelOrder($order);

        $newOrder = app(OrderService::class)->cloneOrder($order, $request->all());
        $newOrder = app(OrderService::class)->processOrder($newOrder);
        $newOrder->loadMissing(self::load_relation);

        return new OrderResource($newOrder);
    }

    /**
     * Order preview
     *
     * Creates a dummy order for preview purposes. Use this endpoint to check
     * how the order will look like with the discount applied.
     *
     * @param PreviewOrderRequest $request
     * @return OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderPreview', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: PreviewOrderRequest::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 200)]
    public function preview(PreviewOrderRequest $request): OrderResource
    {
        $order = app(Pipeline::class)
            ->send(Order::make(['raw_source' => $request->all()]))
            ->through(
                [
                    FillOrderAttributes::class,
                    FillOrderRecord::class,
                    MakeOrderLines::class,
                    ApplyDiscount::class,
                    // ApplyVouchers::class,
                    // CalculateCartDemand::class,
                    AddAdditionalDiscount::class,
                    AddAdditionalFees::class,
                    CheckExpectedOrderPrice::class,
                ]
            )
            ->thenReturn();

        $order->loadMissing(self::load_relation);

        return new OrderResource($order);
    }

    /**
     * Show all orders waiting for approval
     *
     * This endpoint only return orders that can be approved by the authenticated user.
     *
     */
    #[CustomOpenApi\Operation(id: 'OrderIndexWaitingApproval', tags: [Tags::Order, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Order::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, isCollection: true)]
    public function indexWaitingApproval()
    {
        $query = function ($query) {
            return $query->with(self::load_relation)
                ->tenanted()
                ->where('status', OrderStatus::QUOTATION)
                // ->where('company_id', auth()->user()->company_id)
                ->waitingApproval()
                ->canBeApprovedBy(user())
                ->approvalSendToMe();
        };

        return CustomQueryBuilder::buildResource(Order::class, OrderResource::class, $query);
    }

    /**
     * Show all orders approval
     *
     * This endpoint return approval discount orders that related with the authenticated user.
     *
     */
    #[CustomOpenApi\Operation(id: 'OrderListApproval', tags: [Tags::Order, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Order::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, isCollection: true)]
    public function listApproval()
    {
        $query = function ($query) {
            $user = auth()->user();
            $q = $query->tenanted()->with(self::load_relation)
                ->where('status', OrderStatus::QUOTATION)
                // ->where('company_id', auth()->user()->company_id)
                ->requiredApproval()
                ->canBeApprovedBy($user);

            if ($user->is_supervisor) {
                $q = $q->whereIn('channel_id', $user->channels->pluck('id')->all());
            }

            if (isset(request()->filter['approval_status']) && request()->filter['approval_status'] === 'WAITING_APPROVAL') $q->approvalSendToMe();
            return $q;
        };

        return CustomQueryBuilder::buildResource(Order::class, OrderResource::class, $query);
    }

    /**
     * Approve order
     *
     * Approve order
     *
     * @param Order $order
     * @return mixed
     * @throws \Exception
     */
    #[CustomOpenApi\Operation(id: 'OrderApprove', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: ApproveOrderRequest::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function approve(ApproveOrderRequest $request, Order $order)
    {
        return $this->show(app(OrderService::class)->approveOrder($order, user(), $request->all()));
    }

    /**
     * Request Approve order
     *
     * Request Approve order
     *
     * @param Order $order
     * @return JsonResponse
     * @throws \Exception
     */
    #[CustomOpenApi\Operation(id: 'OrderRequestApprove', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateOrderRequest::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function requestApproval(UpdateOrderRequest $request, Order $order)
    {
        $order = app(Pipeline::class)
            ->send($order)
            ->through(
                [
                    UpdateOrderLines::class,
                    UpdateApplyDiscount::class,
                    // CalculateCartDemand::class,
                    UpdateAdditionalDiscount::class,
                    AddAdditionalFees::class,
                    CheckExpectedOrderPrice::class,
                    \App\Pipes\Order\Admin\SaveOrder::class,
                ]
            )
            ->thenReturn();

        $user = auth()->user();
        if ($order->approval_send_to->is(\App\Enums\UserType::SUPERVISOR)) {
            // $users = \App\Models\User::where('type', \App\Enums\UserType::SUPERVISOR)->where('company_id', $order->company_id)->where('supervisor_type_id', 2)->has('notificationDevices')->get();
            // $users = \App\Models\User::where('type', \App\Enums\UserType::SUPERVISOR)->where('company_id', $order->company_id)->where('supervisor_type_id', $order->approval_supervisor_type_id)->has('notificationDevices')->get();
            $users = \App\Models\User::where('type', \App\Enums\UserType::SUPERVISOR)->where('supervisor_type_id', $order->approval_supervisor_type_id)->has('notificationDevices')->get();
        } else {
            // $users = \App\Models\User::where('type', \App\Enums\UserType::DIRECTOR)->where('company_id', $order->company_id)->has('notificationDevices')->get();
            $users = \App\Models\User::where('type', \App\Enums\UserType::DIRECTOR)->has('notificationDevices')->get();
        }

        // $message = 'Notification not sent to director';
        if (count($users) > 0) {
            foreach ($users as $receipent) {
                $type = \App\Enums\NotificationType::DiscountApproval();
                $link = config("notification-link.{$type->key}") ?? 'no-link';

                \App\Events\SendExpoNotification::dispatch([
                    'receipents' => $receipent,
                    'badge_for' => $receipent,
                    'title' => $user->name . " from " . $user->channel->name . " Has Request a New Approval",
                    'body' => $user->name .  ' has request a new discount approval of ' . number_format($order->additional_discount) . ' on invoice ' . $order->invoice_number,
                    'code' => $type->key,
                    'link' => $link,
                ]);

                // $message = 'Notification successfully sent to ' . $user->name;
            }
        }
        // $order->update([
        //     'approval_status' => \App\Enums\OrderApprovalStatus::WAITING_APPROVAL,
        //     'approved_by' => null,
        //     'approval_send_to' => $approval_send_to,
        // ]);

        return new OrderResource($order->refresh());
        // return response()->json(['message' => $message]);
    }

    /**
     * Cancel an order
     *
     * This will cancel an order.
     *
     * @param Order $order
     * @return OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderCancel', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 201)]
    public function cancel(Order $order)
    {
        if ($order->orderPayments->count() > 0) {
            return response()->json(['message' => 'Tidak dapat membatalkan order yang sudah memiliki pembayaran!'], 400);
            // throw new Exception('Tidak dapat membatalkan order yang sudah memiliki pembayaran!');
        }

        $canceledOrder = app(OrderService::class)->cancelOrder($order);

        return new OrderResource($canceledOrder);
    }

    // export quotation
    public function exportQuotation(Request $request)
    {
        if (!$request->type || !$request->order_id) {
            return response()->json([
                'error' => 'Type and order id cannot be null.'
            ], 422);
        }

        $user = auth()->user();

        $params['logo'] = public_path("images/logo/logo-trek.png");
        $params['type'] = $request->type;
        $params['order'] = Order::with('order_details')->findOrFail($request->order_id);
        $params['user'] = $user;
        $pdf = PDF::loadView('api.quotation.exportPdf', ['params' => $params])->setPaper('a3', 'potrait');

        return $pdf->download("$request->type.pdf");
    }
}
