<?php

namespace Tests\Unit\API\Doc;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Class ChannelDocTest
 * @package Tests\Unit\API
 */
class AuthTest extends BaseApiDocTest
{
    protected $newUser;
    protected $credential;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = null;
        $this->credential = ["email" => "test@test.com", "password" => "secret"];
        $this->newUser = User::factory()->create(
            [
                "email" => $this->credential["email"],
                "password" => Hash::make($this->credential["password"])
            ]
        );
    }

    /**
     * @group Doc
     * @return void
     */
    public function testGetToken()
    {
        $this->makeApiTest('/api/v1/auth/token', 'post', $this->credential);
    }
}
