<?php

namespace Tests\Unit\API\Doc;

use App\Models\Activity;

/**
 * Class PushNotificationDocTest
 * @package Tests\Unit\API
 */
class PushNotificationDocTest extends BaseApiDocTest
{
    protected Activity $activity;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activity = Activity::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     * @throws \Exception
     */
    public function testPushNotification()
    {
        $code = 'test';
        $this->makeApiTest(route('push-notification.subscribe', [$code], false), 'put', null);
        $this->makeApiTest(route('push-notification.unsubscribe', [$code], false), 'put', null, 1);
    }

}