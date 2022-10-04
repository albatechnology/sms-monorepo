<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class BaseModelTest
 * @package Tests\Unit\API
 */
abstract class BaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    public Company $company;
    public Channel $channel;
    public User $sales;
    public Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $seeds = [
            'PermissionsTableSeeder',
            'RolesTableSeeder',
            'PermissionRoleTableSeeder',
            'SupervisorTypeSeeder',
            'DefaultCompanySeeder',
            'DefaultChannelSeeder',
            'AdminUsersTableSeeder',
            'UsersTableSeeder',
        ];

        foreach ($seeds as $seed) {
            $this->artisan(sprintf('db:seed --class=%s', $seed));
        }

        $this->company = Company::first();
        $this->channel = Channel::first();

        $users       = User::all();
        $this->sales = $users->first(fn(User $user) => $user->is_sales);

        $this->customer = Customer::factory()->withAddress()->create();
    }
}
