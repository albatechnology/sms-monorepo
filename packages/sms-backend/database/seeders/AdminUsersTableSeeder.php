<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\PersonalAccessToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUsersTableSeeder extends Seeder
{
    public function run()
    {
        // Admin
        $admin = User::factory()->create(
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@melandas.id',
                'password'       => bcrypt('password'),
                'remember_token' => null,
                'channel_id'     => 1
            ],
        );

        $admin->roles()->save(Role::whereAdmin()->first());

        // Assign all channels to admin
        $channels = Channel::all();
        $admin->channels()->sync($channels->pluck('id'));

        // Assign predefined API token
        $data = [
            [
                'token'            => '079cc199113afac7acd23803d73cc3f63a79abe57ecf7c36208465aa164714aa',
                'plain_text_token' => 'gKXmQpniLD8Yy4TN8X5eeo72pcDdRrBJEUo0FxVE',
                'tokenable_id'     => $admin->id,
            ],
        ];

        foreach ($data as $entry) {
            PersonalAccessToken::forceCreate(
                [
                    "tokenable_type" => "App\Models\User",
                    "name"           => "default",
                    "abilities"      => ["*",],
                ] + $entry
            );
        }

    }
}
