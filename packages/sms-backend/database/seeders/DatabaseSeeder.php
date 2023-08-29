<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            SupervisorTypeSeeder::class,
            PermissionSeeder::class,
            SubscribtionPackageSeeder::class,
            AdminUsersTableSeeder::class,
            // ChannelSeeder::class,
            UsersTableSeeder::class,
            // PermissionsTableSeeder::class,
            // RolesTableSeeder::class,
            // PermissionRoleTableSeeder::class,
            // RoleUserTableSeeder::class,

            ProductSeeder::class,
            CustomerSeeder::class,
            LeadSeeder::class,
            PaymentSeeder::class,

            // Ensure non empty company
            // DefaultCompanySeeder::class,
            // DefaultChannelSeeder::class,
        ]);

        //         $this->call([
        //             PermissionsTableSeeder::class,
        //             RolesTableSeeder::class,
        //             PermissionRoleTableSeeder::class,
        //             SupervisorTypeSeeder::class,

        //             // Ensure non empty company
        //             DefaultCompanySeeder::class,
        //             DefaultChannelSeeder::class,
        //         ]);

        //         $this->callOnce([
        //             AdminUsersTableSeeder::class,

        //             // fix old order when deal_at is added
        //             OrderDealAtSeeder::class,

        //             // new table, seed target type priority with default values
        //             TargetTypePrioritySeeder::class,

        //             ReligionSeeder::class,
        //         ]);

        //         if (!App::environment('production')) {
        //             $this->callOnce([
        //                 CompanySeeder::class,
        //                 ChannelSeeder::class,
        //                 UsersTableSeeder::class,
        //                 RoleUserTableSeeder::class,
        //                 CustomerSeeder::class,

        //                 ProductTagSeeder::class,
        //                 ProductCategorySeeder::class,
        //                 ProductSeeder::class,
        //                 LeadSeeder::class,
        //                 ProductListSeeder::class,
        //                 DiscountSeeder::class,

        //                 PaymentCategorySeeder::class,
        //                 PaymentTypeSeeder::class,
        //             ]);
        //         }
    }

    /**
     * Seeder that should only ever be called once.
     * We check seeders table for seeded class and record
     * them once seeding is completed.
     *
     * @param $classes
     */
    public function callOnce($classes)
    {
        $seeded = \App\Models\Seeder::all();

        $classes = collect($classes)
            ->filter(function ($class_name) use ($seeded) {
                return !$seeded->contains(function (\App\Models\Seeder $seed) use ($class_name) {
                    // compare by qualified as well as direct class name
                    return $seed->seeders === $class_name || $seed->seeders === Str::of($class_name)->afterLast('\\');
                });
            })
            ->values()
            ->all();

        $this->call($classes);

        collect($classes)->each(function ($class_name) {
            return \App\Models\Seeder::create(['seeders' => $class_name]);
        });
    }
}
