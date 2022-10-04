<?php

namespace Tests\Unit\API\Business;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class BaseApiTest
 * @package Tests\Unit\API\Business
 */
abstract class BaseApiTest extends TestCase
{
    use RefreshDatabase;

    public ?User $user;

    /**
     * Visit the given URI with a GET request.
     *
     * @param string $uri
     * @param array $headers
     * @return TestResponse
     */
    public function get($uri, array $headers = [])
    {
        $headers = array_merge(['Accept' => 'application/json'], $headers);

        return parent::get($uri, $headers);
    }

    /**
     * Visit the given URI with a GET request, expecting a JSON response.
     *
     * @param string $uri
     * @param array $headers
     * @return TestResponse
     */
    public function getJson($uri, array $headers = [])
    {
        $headers = array_merge(['Accept' => 'application/json'], $headers);

        return parent::getJson($uri, $headers);
    }

    /**
     * Visit the given URI with a POST request.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        $headers = array_merge(['Accept' => 'application/json'], $headers);

        return parent::post($uri, $data, $headers);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->sales()->withChannel()->create();
        Sanctum::actingAs($this->user, ['*']);
        //$this->withoutMiddleware(Authenticate::class);
    }
}
