<?php

namespace Tests\Unit\API\Doc;

use App\Models\QaTopic;

/**
 * Class QaTopicTest
 * @package Tests\Unit\API
 */
class QaTopicDocTest extends BaseApiDocTest
{
    protected QaTopic $QaTopic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->QaTopic = QaTopic::factory()->withMessages()->create(["creator_id" => $this->user->id]);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaTopicIndex()
    {
        $this->makeApiTest(route('qa-topics.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaTopicCreate()
    {
        $this->makeApiTest(route('qa-topics.create', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaTopicStore()
    {
        $data = QaTopic::factory()->make()->toApiArray();
        $data = array_merge($data, ['users' => [1]]);

        $this->makeApiTest(route('qa-topics.store', [], false), 'post', $data, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaTopicShow()
    {
        $this->makeApiTest(route('qa-topics.show', [$this->QaTopic->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaTopicDestroy()
    {
        $QaTopic = QaTopic::factory()->create();
        $this->makeApiTest(route('qa-topics.destroy', [$QaTopic->id], false), 'delete', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaTopicEdit()
    {
        $this->makeApiTest(route('qa-topics.edit', [$this->QaTopic->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaTopicUpdate()
    {
        $data = $this->QaTopic->toApiArray();
        $data = array_merge($data, ['users' => [1]]);
        $this->makeApiTest(route('qa-topics.update', [$this->QaTopic->id], false), 'put', $data, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaTopicGetMessages()
    {
        $this->makeApiTest(route('qa-topics.qa-messages', [$this->QaTopic->id], false), 'get', [], 0);
    }
}