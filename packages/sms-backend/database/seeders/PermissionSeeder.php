<?php

namespace Database\Seeders;

use App\Helpers\PermissionsHelper;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = PermissionsHelper::getAllPermissions();

        $permissions->each(function ($permission, $key) {
            if (is_array($permission)) {
                $headSubPermissions = Permission::firstOrCreate([
                    'name' => $key,
                    // 'guard_name' => $guard
                ]);

                PermissionsHelper::generateChilds($headSubPermissions, $permission);
            } else {
                Permission::firstOrCreate([
                    'name' => $permission,
                    // 'guard_name' => $guard
                ]);
            }
        });
    }
}
