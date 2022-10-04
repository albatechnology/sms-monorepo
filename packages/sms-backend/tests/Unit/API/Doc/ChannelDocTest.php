<?php

namespace Tests\Unit\API\Doc;

use App\Models\Channel;

/**
 * Class ChannelDocTest
 * @package Tests\Unit\API
 */
class ChannelDocTest extends BaseApiDocTest
{
    protected Channel $channel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->user->channel;
    }

    /**
     * @group Doc
     * @return void
     * @throws \Exception
     */
    public function testIndexChannel()
    {
        $this->makeApiTest(route('channels.index', [], false), 'get', null, 0);
    }

    /**
     * @group Doc
     * @return void
     * @throws \Exception
     */
    public function testChannelShow()
    {
        $this->makeApiTest(route('channels.show', [$this->channel->id], false), 'get', null, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testChannelDefault()
    {
        $this->makeApiTest(route('channels.default', [], false), 'get', null, 0);
    }
}
