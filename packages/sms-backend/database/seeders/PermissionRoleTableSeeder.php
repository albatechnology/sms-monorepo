<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $admin_permissions = Permission::all();

        Role::where('title', 'admin')
            ->firstOrFail()
            ->permissions()
            ->sync($admin_permissions->pluck('id'));

        $user_permissions = $admin_permissions->filter(function ($permission) {
            return substr($permission->title, 0, 5) != 'user_' && substr($permission->title, 0, 5) != 'role_' && substr($permission->title, 0, 11) != 'permission_';
        });

        Role::where('title', 'user')
            ->firstOrFail()
            ->permissions()
            ->sync($user_permissions);
    }
}
