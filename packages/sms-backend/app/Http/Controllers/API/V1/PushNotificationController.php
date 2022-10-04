<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\ExpoMessage;
use App\Enums\NotificationType;
use App\Http\Requests\API\V1\Notification\CreateTestNotificationRequest;
use App\Http\Resources\V1\Notification\NotificationResource;
use App\Models\Activity;
use App\Models\Notification;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class PushNotificationController extends BaseApiController
{

    public function __construct(protected PushNotificationService $service)
    {
    }

    /**
     * Subscribe device code to push notification
     *
     * Subscribe device code to push notification
     *
     * @param string $code
     * @return JsonResponse
     */
    #[CustomOpenApi\Operation(id: 'pushNotificationSubscribe', tags: [Tags::PushNotification, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function subscribe(string $code): JsonResponse
    {
        $this->service->subscribeCode($code, user());
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Unsubscribe device code from push notification
     *
     * Unsubscribe device code from push notification
     *
     * @param string $code
     * @return JsonResponse
     */
    #[CustomOpenApi\Operation(id: 'pushNotificationUnsubscribe', tags: [Tags::PushNotification, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function unsubscribe(string $code): JsonResponse
    {
        $this->service->unsubscribeCode($code);
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show all notifications
     *
     * Show all notifications
     *
     */
    #[CustomOpenApi\Operation(id: 'pushNotificationIndex', tags: [Tags::PushNotification, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Notification::class)]
    #[CustomOpenApi\Response(resource: NotificationResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(
            Notification::class,
            NotificationResource::class,
        );
    }

    /**
     * Clear all notifications
     *
     * Set all current user notification as read
     *
     * @return JsonResponse
     * @throws \Exception
     */
    #[CustomOpenApi\Operation(id: 'pushNotificationClear', tags: [Tags::PushNotification, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function clear(): JsonResponse
    {
        Notification::query()
            ->where('notifiable_id', user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // send a reset badge notification
        $message = ExpoMessage::create()->addRecipients(user());
        app(PushNotificationService::class)->clearBadge($message);

        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Send a test notification
     *
     * Send a test notification
     *
     * @param CreateTestNotificationRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    #[CustomOpenApi\Operation(id: 'pushNotificationTest', tags: [Tags::PushNotification, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateTestNotificationRequest::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function test(CreateTestNotificationRequest $request): JsonResponse
    {
        // use ActivityReminder as dummy notification
        $type     = NotificationType::ActivityReminder();
        $activity = Activity::where('user_id', user()->id)->first();
        $link     = $activity ? sprintf(config("notification-link.{$type->key}"), $activity->id) : 'no-link';

        $message = ExpoMessage::create()
            ->addRecipients(user())
            ->setBadgeFor(user())
            ->title($request->get('title') ?? 'Test Notification')
            ->body($request->get('body') ?? 'This is a test notification')
            ->code($type->key)
            ->link($link);

        app(PushNotificationService::class)->notify($message);

        return GenericSuccessMessageResponse::getResponse();
    }
}