<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUsersTableSeeder extends Seeder
{
    public function run()
    {
        // super Admin
        $superAdminRole = Role::create([
            'name' => 'super-admin',
            'guard_name' => 'web',
            'subscribtion_user_id' => 1
        ]);

        $superAdmin = User::create([
            'subscribtion_user_id' => 1,
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRole->id,
            'model_type' => 'user',
            'model_id' => $superAdmin->id,
            'subscribtion_user_id' => 1
        ]);

        // $superAdmin->assignRole($superAdminRole);

        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions(Permission::all());

        $admin = User::create([
            'subscribtion_user_id' => 1,
            'name' => 'Admin ALBA',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $adminRole->id,
            'model_type' => 'user',
            'model_id' => $admin->id,
            'subscribtion_user_id' => 1
        ]);
        // $admin->assignRole($adminRole);

        // DB::table('model_has_roles')->insert([
        //     'role_id' => $superAdminRole->id,
        //     'model_type' => get_class($superAdmin),
        //     'model_id' => $superAdmin->id,
        //     'subscribtion_user_id' => 1
        // ]);

        // $admin->roles()->save(Role::whereAdmin()->first());

        // // Assign all channels to admin
        // $channels = Channel::all();
        // $admin->channels()->sync($channels->pluck('id'));

        // // Assign predefined API token
        // $data = [
        //     [
        //         'token'            => '079cc199113afac7acd23803d73cc3f63a79abe57ecf7c36208465aa164714aa',
        //         'plain_text_token' => 'gKXmQpniLD8Yy4TN8X5eeo72pcDdRrBJEUo0FxVE',
        //         'tokenable_id'     => $admin->id,
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
