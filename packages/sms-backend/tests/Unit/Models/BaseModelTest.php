<?php

namespace Tests\Unit\Models;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class BaseModelTest
 * @package Tests\Unit\API
 */
abstract class BaseModelTest extends TestCase
{
    use RefreshDatabase;

    public ?User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->sales()->withChannel()->create();
    }
}
