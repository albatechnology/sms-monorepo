<?php

namespace Tests\Unit\API\Doc;

use App\Models\Activity;
use App\Models\ActivityComment;

/**
 * Class ActivityCommentDocTest
 * @package Tests\Unit\API
 */
class ActivityCommentDocTest extends BaseApiDocTest
{
    protected Activity $activity;
    protected ActivityComment $activity_comment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activity = Activity::factory()->create();
        $this->activity_comment = ActivityComment::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityIndex()
    {
        $this->makeApiTest(route('activity-comments.index', [], false), 'get', null, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityCreate()
    {
        $this->makeApiTest(route('activity-comments.create', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityStore()
    {
        $data = ActivityComment::factory()->make()->toApiArray();
        $this->makeApiTest(route('activity-comments.store', [], false), 'post', $data, 1);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityShow()
    {
        $this->makeApiTest(route('activity-comments.show', [$this->activity->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityDestroy()
    {
        $model = ActivityComment::factory()->create();
        $this->makeApiTest(route('activity-comments.destroy', [$model->id], false), 'delete', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityEdit()
    {
        $this->makeApiTest(route('activity-comments.edit', [$this->activity->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testActivityUpdate()
    {
        $data = $this->activity_comment->toApiArray();
        $this->makeApiTest(route('activity-comments.update', [$this->activity->id], false), 'put', $data, 0);
    }
}