<?php

namespace App\Services;

use App\Classes\ExpoMessage;
use App\Models\NotificationDevice;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;


class PushNotificationService
{
    public const EXPO_URL = 'https://exp.host/--/api/v2/push/send';

    public bool $sendAsBulk;

    public function __construct(protected Client $client)
    {
        $this->sendAsBulk = config('core.notification.mode') === 'bulk';
    }

    /**
     * Send notification
     *
     * @param ExpoMessage $message
     * @throws GuzzleException
     * @throws \Exception
     */
    public function notify(ExpoMessage $message): void
    {
        if ($this->sendAsBulk) {
            $this->sendBulk($message->toData());
        } else {
            $this->sendSingle($message->toSingleData());
        }
    }

    /**
     * Send expo message in a single request
     * @param array $data
     * @throws GuzzleException
     */
    protected function sendBulk(array $data): void
    {
        $this->client->post(self::EXPO_URL, $data);
    }

    /**
     * Send expo message as one request per device
     * @param array $allData
     * @throws GuzzleException
     */
    protected function sendSingle(array $allData): void
    {
        foreach ($allData as $data) {
            $this->client->post(self::EXPO_URL, $data);
        }
    }

    /**
     * @param ExpoMessage $message
     * @throws GuzzleException
     * @throws \Exception
     */
    public function clearBadge(ExpoMessage $message): void
    {
        if ($this->sendAsBulk) {
            $this->sendBulk($message->toClearBadge());
        } else {
            $this->sendSingle($message->toSingleClearBadge());
        }

    }

    /**
     * Takes an exponent push token code and subscribe
     * @param string $code
     * @param User $user
     * @return NotificationDevice|null
     */
    public function subscribeCode(string $code, User $user): ?NotificationDevice
    {
        return NotificationDevice::updateOrCreate(
            ['code' => $code],
            ['user_id' => $user->id],
        );
    }

    /**
     * unsubscribe a given exponent token code
     * @param string $code
     */
    public function unsubscribeCode(string $code): void
    {
        NotificationDevice::query()
            ->where('code', $code)
            ->delete();
    }
}
