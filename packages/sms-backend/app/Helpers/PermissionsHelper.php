<?php

namespace App\Helpers;

use App\Models\Permission;

class PermissionsHelper
{
    public static function getAllPermissions()
    {
        return collect(static::adminPermissions())
            ->mergeRecursive(static::superAdminPermissions());
    }

    public static function getAdminPermissionsData(): array
    {
        $persmissions = self::adminPermissions();

        $data = [];
        foreach ($persmissions as $key => $persmission) {
            if (is_array($persmission)) {
                $data[] = $key;
                foreach ($persmission as $key => $persmission) {
                    if (is_array($persmission)) {
                        $data[] = $key;

                        foreach ($persmission as $p) {
                            $data[] = $p;
                        }
                    } else {
                        $data[] = $persmission;
                    }
                }
            } else {
                $data[] = $persmission;
            }
        }
        return $data;
    }

    public static function adminPermissions(): array
    {
        return [
            'dashboard_access',

            'user_management_access' => [
                'user_access' => [
                    'user_create',
                    'user_edit',
                    'user_delete',
                ],

                'role_access' => [
                    'role_create',
                    'role_edit',
                    'role_delete',
                ],
            ],

            'corporate_management_access' => [
                // 'companies_access' => [
                //     'companies_create',
                //     'companies_edit',
                //     'companies_delete',
                // ],
                'channel_access' => [
                    'channel_create',
                    'channel_edit',
                    'channel_delete',
                ],
            ],

            'crm_access' => [
                'lead_access' => [
                    'lead_create',
                    'lead_edit',
                    'lead_delete',
                ],

                'lead_category_access' => [
                    'lead_category_create',
                    'lead_category_edit',
                    'lead_category_delete',
                ],

                'sub_lead_category_access' => [
                    'sub_lead_category_create',
                    'sub_lead_category_edit',
                    'sub_lead_category_delete',
                ],

                'activity_access' => [
                    'activity_create',
                    'activity_edit',
                    'activity_delete',
                ],

                'customer_access' => [
                    'customer_create',
                    'customer_edit',
                    'customer_delete',
                ],

                'address_access' => [
                    'address_create',
                    'address_edit',
                    'address_delete',
                ],
            ],

            'product_management_access' => [
                'brand_category_access' => [
                    'brand_category_create',
                    'brand_category_edit',
                    'brand_category_delete',
                ],

                'product_brand_access' => [
                    'product_brand_create',
                    'product_brand_edit',
                    'product_brand_delete',
                ],

                'product_category_access' => [
                    'product_category_create',
                    'product_category_edit',
                    'product_category_delete',
                ],

                'product_access' => [
                    'product_create',
                    'product_edit',
                    'product_delete',
                ],
            ],

            'marketing_access' => [
                'promo_access' => [
                    'promo_create',
                    'promo_edit',
                    'promo_delete',
                ],

                'discount_access' => [
                    'discount_create',
                    'discount_edit',
                    'discount_delete',
                ],
            ],

            'finance_access' => [
                'order_access' => [
                    'order_create',
                    'order_edit',
                    'order_delete',
                ],

                'payment_access' => [
                    'payment_create',
                    'payment_edit',
                    'payment_delete',
                ],

                'payment_category_access' => [
                    'payment_category_create',
                    'payment_category_edit',
                    'payment_category_delete',
                ],

                'payment_type_access' => [
                    'payment_type_create',
                    'payment_type_edit',
                    'payment_type_delete',
                ],
            ],
        ];
    }

    public static function superAdminPermissions(): array
    {
        return [
            'user_management_access' => [
                'permission_access' => [
                    'permission_create',
                    'permission_edit',
                    'permission_delete',
                ],
            ],
        ];
    }

    public static function generateChilds(Permission $headSubPermissions, array $subPermissions)
    {
        collect($subPermissions)->each(function ($permission, $key) use ($headSubPermissions) {
            if (is_array($permission)) {
                $hsp = Permission::firstOrCreate([
                    'name' => $key,
                    // 'guard_name' => $guard,
                    'parent_id' => $headSubPermissions->id
                ]);

                self::generateChilds($hsp, $permission);
            } else {
                $hsp = Permission::firstOrCreate([
                    'name' => $permission,
                    // 'guard_name' => $guard,
                    'parent_id' => $headSubPermissions->id
                ]);
            }

            return;
        });
    }
}
