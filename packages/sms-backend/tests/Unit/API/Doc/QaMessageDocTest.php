<?php

namespace Tests\Unit\API\Doc;

use App\Models\QaMessage;

/**
 * Class QaMessageTest
 * @package Tests\Unit\API
 */
class QaMessageDocTest extends BaseApiDocTest
{
    protected QaMessage $QaMessage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->QaMessage = QaMessage::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaMessageIndex()
    {
        $this->makeApiTest(route('qa-messages.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaMessageCreate()
    {
        $this->makeApiTest(route('qa-messages.create', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaMessageStore()
    {
        $data = QaMessage::factory()->make()->toApiArray();
        $this->makeApiTest(route('qa-messages.store', [], false), 'post', $data, 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaMessageShow()
    {
        $this->makeApiTest(route('qa-messages.show', [$this->QaMessage->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaMessageDestroy()
    {
        $QaMessage = QaMessage::factory()->create();
        $this->makeApiTest(route('qa-messages.destroy', [$QaMessage->id], false), 'delete', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaMessageEdit()
    {
        $this->makeApiTest(route('qa-messages.edit', [$this->QaMessage->id], false), 'get', [], 0);
    }

    /**
     * @group Doc
     * @return void
     */
    public function testQaMessageUpdate()
    {
        $data = $this->QaMessage->toApiArray();
        $this->makeApiTest(route('qa-messages.update', [$this->QaMessage->id], false), 'put', $data, 0);
    }
}