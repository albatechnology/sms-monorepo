<?php

namespace App\Providers;

use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Events\OrderDealReversal;
use App\Events\OrderIsDeal;
use App\Events\OrderPaymentDownPayment;
use App\Events\OrderPaymentSettlement;
use App\Events\PaymentApproved;
use App\Events\PaymentDisapproved;
use App\Events\SendExpoNotification;
use App\Events\ShipmentUpdated;
use App\Listeners\MonitorNotification;
use App\Listeners\RecordReportTarget;
use App\Listeners\RefreshOrderPaymentStatus;
use App\Listeners\RemoveReportable;
use App\Listeners\SendExpoNotificationProcess;
use App\Listeners\SetOrderStatusToWarehouse;
use App\Listeners\UpdateLeadToClosed;
use App\Listeners\UpdateLeadToProspect;
use App\Listeners\UpdateOrderShippingStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class              => [
            SendEmailVerificationNotification::class,
        ],

        // notification
        NotificationSent::class        => [
            MonitorNotification::class,
        ],
        SendExpoNotification::class => [
            SendExpoNotificationProcess::class,
        ],

        // order
        OrderCreated::class            => [
            UpdateLeadToProspect::class,
        ],
        OrderCancelled::class          => [
            RemoveReportable::class,
        ],
        OrderPaymentDownPayment::class => [
        ],
        OrderIsDeal::class             => [
            RecordReportTarget::class,
        ],
        // order that is previously deal is no longer deal
        OrderDealReversal::class       => [
            RemoveReportable::class,
        ],
        OrderPaymentSettlement::class  => [
            UpdateLeadToClosed::class,
            // SetOrderStatusToWarehouse::class,
        ],

        // payment
        PaymentApproved::class         => [
            RecordReportTarget::class,
            RefreshOrderPaymentStatus::class,
        ],
        PaymentDisapproved::class      => [
            RemoveReportable::class,
            RefreshOrderPaymentStatus::class,
        ],

        // shipment
        ShipmentUpdated::class         => [
            UpdateOrderShippingStatus::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
