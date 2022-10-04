<?php

namespace Tests\Unit\API\Doc;

use App\Models\Activity;

/**
 * Class ActivityTest
 * @package Tests\Unit\API
 */
class ActivityDocTest extends BaseApiDocTest
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
     */
    public function testActivityIndex()
    {
        $this->makeApiTest(route('activities.index', [], false), 'get', null, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityCreate()
    {
        $this->makeApiTest(route('activities.create', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityStore()
    {
        $data = Activity::factory()->make()->toApiArray();
        $this->makeApiTest(route('activities.store', [], false), 'post', $data, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityShow()
    {
        $this->makeApiTest(route('activities.show', [$this->activity->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityDestroy()
    {
        $activity = Activity::factory()->create();
        $this->makeApiTest(route('activities.destroy', [$activity->id], false), 'delete', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityEdit()
    {
        $this->makeApiTest(route('activities.edit', [$this->activity->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityUpdate()
    {
        $data = $this->activity->toApiArray();
        $this->makeApiTest(route('activities.update', [$this->activity->id], false), 'put', $data, 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityGetComments()
    {
        $data = $this->activity->toApiArray();
        $this->makeApiTest(route('activities.comments', [$this->activity->id], false), 'get', $data, 0);
    }
}