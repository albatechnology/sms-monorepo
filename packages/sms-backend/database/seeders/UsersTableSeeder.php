<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Channel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $channel = Channel::create([
            // 'company_id' => $company->id,
            'subscribtion_user_id' => 2,
            'name' => 'Channel Starter 1'
        ]);

        $admin = User::create([
            'subscribtion_user_id' => 2,
            'name' => 'Admin Starter',
            'email' => 'adminstarter@gmail.com',
            'password' => bcrypt('12345678'),
            'type' => UserType::DEFAULT,
        ]);
        $admin->channels()->sync([$channel->id]);
        // $admin->assignRole($adminRole);
        DB::table('model_has_roles')->insert([
            'role_id' => 2,
            'model_type' => 'user',
            'model_id' => $admin->id,
            'subscribtion_user_id' => 2
        ]);

        $userRole = Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        // Director
        $director = User::create(
            [
                'subscribtion_user_id' => 2,
                'name'           => 'Director',
                'email'          => 'director@gmail.com',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'type'           => UserType::DIRECTOR,
                'channel_id'     => 1
            ],
        );
        $director->channels()->sync([$channel->id]);
        DB::table('model_has_roles')->insert([
            'role_id' => $userRole->id,
            'model_type' => 'user',
            'model_id' => $director->id,
            'subscribtion_user_id' => 2
        ]);

        $bum = User::create(
            [
                'subscribtion_user_id' => 2,
                'name'           => 'BUM',
                'email'          => 'bum@gmail.com',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'type'           => UserType::SUPERVISOR,
                'channel_id'     => 1,
                'supervisor_id' => null,
                'supervisor_type_id' => 2
            ],
        );
        $bum->channels()->sync([$channel->id]);
        DB::table('model_has_roles')->insert([
            'role_id' => $userRole->id,
            'model_type' => 'user',
            'model_id' => $bum->id,
            'subscribtion_user_id' => 2
        ]);

        $storeLeader = User::create(
            [
                'subscribtion_user_id' => 2,
                'name'           => 'Store Leader',
                'email'          => 'storeleader@gmail.com',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'type'           => UserType::SUPERVISOR,
                'channel_id'     => 1,
                'supervisor_id' => $bum->id,
                'supervisor_type_id' => 1
            ],
        );
        $storeLeader->channels()->sync([$channel->id]);
        DB::table('model_has_roles')->insert([
            'role_id' => $userRole->id,
            'model_type' => 'user',
            'model_id' => $storeLeader->id,
            'subscribtion_user_id' => 2
        ]);

        $sales = User::create(
            [
                'subscribtion_user_id' => 2,
                'name'           => 'Sales',
                'email'          => 'sales@gmail.com',
                'password'       => bcrypt('12345678'),
                'remember_token' => null,
                'type'           => UserType::SALES,
                'channel_id'     => 1,
                'supervisor_id' => $storeLeader->id
            ],
        );
        $sales->channels()->sync([$channel->id]);
        DB::table('model_has_roles')->insert([
            'role_id' => $userRole->id,
            'model_type' => 'user',
            'model_id' => $sales->id,
            'subscribtion_user_id' => 2
        ]);

        $channel = Channel::create([
            // 'company_id' => $company->id,
            'subscribtion_user_id' => 3,
            'name' => 'Channel basic 1'
        ]);

        $admin = User::create([
            'subscribtion_user_id' => 3,
            'name' => 'Admin basic',
            'email' => 'adminbasic@gmail.com',
            'password' => bcrypt('12345678'),
            'type' => UserType::DEFAULT,
        ]);
        $admin->channels()->sync([$channel->id]);
        DB::table('model_has_roles')->insert([
            'role_id' => 2,
            'model_type' => 'user',
            'model_id' => $admin->id,
            'subscribtion_user_id' => 3
        ]);

        // Assign predefined API token
        // $data = [
        //     [
        //         'token'            => 'cf3472ae6a525e9994d800bb14b0d8f01a43fef0d60b9dcaa7d6892af9b599cb',
        //         'plain_text_token' => 'mVYN3Bc7YM1dzvhE2yFRb6dSNtjAWQ2zUBHd5x5X',
        //         'tokenable_id'     => $sales->id,
        //     ],
        //     [
        //         'token'            => '295028081ec4b355afb921ecbff5476d6444bdb53b3a761cf2eab5c236312837',
        //         'plain_text_token' => 'ObC9Wj9DOaOsKiZIHWCgU6DThng0uNDjIlWVSiFa',
        //         'tokenable_id'     => $supervisor->id,
        //     ],
        //     [
        //         'token'            => 'e474ee9b34ec735fd60d6c48baf180c720e73edf3db7a9ace7e41baeb569e1bf',
        //         'plain_text_token' => 'TEAfxtdo5SviaXZr2wlfzKGY3pzP4dlikPipKJfN',
        //         'tokenable_id'     => $director->id,
        //     ],
        // ];

        // foreach ($data as $entry) {
        //     PersonalAccessToken::forceCreate(
        //         [
        //             "tokenable_type" => "App\Models\User",
        //             "name"           => "default",
        //             "abilities"      => ["*",],
        //         ] + $entry
        //     );
        // }

    }
}
