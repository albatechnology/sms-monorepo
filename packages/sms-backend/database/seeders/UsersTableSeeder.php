<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Channel;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Director
        $director = User::factory()->create(
            [
                'name'           => 'Director',
                'email'          => 'director@melandas.id',
                'password'       => bcrypt('password'),
                'remember_token' => null,
                'type'           => UserType::DIRECTOR,
                'company_id'     => 1,
                'channel_id'     => 1
            ],
        );

        // Supervisor
        $supervisor = User::factory()->supervisor()->create(
            [
                'name'           => 'Supervisor',
                'email'          => 'supervisor@melandas.id',
                'password'       => bcrypt('password'),
                'remember_token' => null,
                'channel_id'     => 1
            ],
        );

        // Sales
        $sales = User::factory()->supervisedBy($supervisor)->create(
            [
                'name'           => 'Sales',
                'email'          => 'sales@melandas.id',
                'password'       => bcrypt('password'),
                'remember_token' => null,
                'type'           => UserType::SALES,
                'channel_id'     => 1
            ],
        );

        // Assign all channels to all users
        $channels = Channel::all();
        User::all()->each(function (User $user) use ($channels) {
            $user->channels()->sync($channels->pluck('id'));
        });

        // Assign predefined API token
        $data = [
            [
                'token'            => 'cf3472ae6a525e9994d800bb14b0d8f01a43fef0d60b9dcaa7d6892af9b599cb',
                'plain_text_token' => 'mVYN3Bc7YM1dzvhE2yFRb6dSNtjAWQ2zUBHd5x5X',
                'tokenable_id'     => $sales->id,
            ],
            [
                'token'            => '295028081ec4b355afb921ecbff5476d6444bdb53b3a761cf2eab5c236312837',
                'plain_text_token' => 'ObC9Wj9DOaOsKiZIHWCgU6DThng0uNDjIlWVSiFa',
                'tokenable_id'     => $supervisor->id,
            ],
            [
                'token'            => 'e474ee9b34ec735fd60d6c48baf180c720e73edf3db7a9ace7e41baeb569e1bf',
                'plain_text_token' => 'TEAfxtdo5SviaXZr2wlfzKGY3pzP4dlikPipKJfN',
                'tokenable_id'     => $director->id,
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
