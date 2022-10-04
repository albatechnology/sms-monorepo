<?php

namespace Tests\Unit\API\Doc;

use App\Models\Channel;
use App\Models\User;

/**
 * Class UserTest
 * @package Tests\Unit\API
 */
class UserDocTest extends BaseApiDocTest
{
    protected Channel $channel;

    protected function setUp(): void
    {
        parent::setUp();
        $supervisor = User::factory()->supervisor()->create();
        $this->user = User::factory()->supervisedBy($supervisor)->withChannel()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testUserShowMe()
    {
        $this->makeApiTest(route('users.me', [], false), 'get', null, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testUserShowSupervisor()
    {
        $this->makeApiTest(route('users.supervisor', [], false), 'get', null, 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testUserSetDefaultChannel()
    {
        $this->makeApiTest(route('users.channel', ['channel' => $this->user->channels->first()->id], false), 'put', null, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testUserShow()
    {
        $this->makeApiTest(route('users.show', [$this->user->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testUserIndex()
    {
        $this->makeApiTest(route('users.index', [], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testIndexUserForReport()
    {
        $this->makeApiTest(route('users.forReport', [], false), 'get', [], 1);
    }


    /**
     * @group Doc
     * @return void
     */
    public function testSupervisorTypeIndex()
    {
        $this->makeApiTest(route('users.supervisor.types', [], false), 'get', null, 1);
    }
}
