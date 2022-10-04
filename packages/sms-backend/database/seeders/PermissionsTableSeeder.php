<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'product_management_access',
            ],
            [
                'id'    => 18,
                'title' => 'product_category_create',
            ],
            [
                'id'    => 19,
                'title' => 'product_category_edit',
            ],
            [
                'id'    => 20,
                'title' => 'product_category_show',
            ],
            [
                'id'    => 21,
                'title' => 'product_category_delete',
            ],
            [
                'id'    => 22,
                'title' => 'product_category_access',
            ],
            [
                'id'    => 23,
                'title' => 'product_tag_create',
            ],
            [
                'id'    => 24,
                'title' => 'product_tag_edit',
            ],
            [
                'id'    => 25,
                'title' => 'product_tag_show',
            ],
            [
                'id'    => 26,
                'title' => 'product_tag_delete',
            ],
            [
                'id'    => 27,
                'title' => 'product_tag_access',
            ],
            [
                'id'    => 28,
                'title' => 'product_create',
            ],
            [
                'id'    => 29,
                'title' => 'product_edit',
            ],
            [
                'id'    => 30,
                'title' => 'product_show',
            ],
            [
                'id'    => 31,
                'title' => 'product_delete',
            ],
            [
                'id'    => 32,
                'title' => 'product_access',
            ],
            [
                'id'    => 33,
                'title' => 'product_unit_create',
            ],
            [
                'id'    => 34,
                'title' => 'product_unit_edit',
            ],
            [
                'id'    => 35,
                'title' => 'product_unit_show',
            ],
            [
                'id'    => 36,
                'title' => 'product_unit_delete',
            ],
            [
                'id'    => 37,
                'title' => 'product_unit_access',
            ],
            //            [
            //                'id'    => 38,
            //                'title' => 'flash_sale_create',
            //            ],
            //            [
            //                'id'    => 39,
            //                'title' => 'flash_sale_edit',
            //            ],
            //            [
            //                'id'    => 40,
            //                'title' => 'flash_sale_show',
            //            ],
            //            [
            //                'id'    => 41,
            //                'title' => 'flash_sale_delete',
            //            ],
            //            [
            //                'id'    => 42,
            //                'title' => 'flash_sale_access',
            //            ],
            [
                'id'    => 43,
                'title' => 'corporate_access',
            ],
            [
                'id'    => 44,
                'title' => 'company_create',
            ],
            [
                'id'    => 45,
                'title' => 'company_edit',
            ],
            [
                'id'    => 46,
                'title' => 'company_show',
            ],
            [
                'id'    => 47,
                'title' => 'company_delete',
            ],
            [
                'id'    => 48,
                'title' => 'company_access',
            ],
            [
                'id'    => 49,
                'title' => 'channel_category_create',
            ],
            [
                'id'    => 50,
                'title' => 'channel_category_edit',
            ],
            [
                'id'    => 51,
                'title' => 'channel_category_show',
            ],
            [
                'id'    => 52,
                'title' => 'channel_category_delete',
            ],
            [
                'id'    => 53,
                'title' => 'channel_category_access',
            ],
            [
                'id'    => 54,
                'title' => 'channel_create',
            ],
            [
                'id'    => 55,
                'title' => 'channel_edit',
            ],
            [
                'id'    => 56,
                'title' => 'channel_show',
            ],
            [
                'id'    => 57,
                'title' => 'channel_delete',
            ],
            [
                'id'    => 58,
                'title' => 'channel_access',
            ],
            [
                'id'    => 59,
                'title' => 'lead_create',
            ],
            [
                'id'    => 60,
                'title' => 'lead_edit',
            ],
            [
                'id'    => 61,
                'title' => 'lead_show',
            ],
            [
                'id'    => 62,
                'title' => 'lead_delete',
            ],
            [
                'id'    => 63,
                'title' => 'lead_access',
            ],
            [
                'id'    => 64,
                'title' => 'activity_create',
            ],
            [
                'id'    => 65,
                'title' => 'activity_edit',
            ],
            [
                'id'    => 66,
                'title' => 'activity_show',
            ],
            [
                'id'    => 67,
                'title' => 'activity_delete',
            ],
            [
                'id'    => 68,
                'title' => 'activity_access',
            ],
            [
                'id'    => 69,
                'title' => 'warehouse_access',
            ],
            [
                'id'    => 70,
                'title' => 'item_create',
            ],
            [
                'id'    => 71,
                'title' => 'item_edit',
            ],
            [
                'id'    => 72,
                'title' => 'item_show',
            ],
            [
                'id'    => 73,
                'title' => 'item_delete',
            ],
            [
                'id'    => 74,
                'title' => 'item_access',
            ],
            [
                'id'    => 75,
                'title' => 'item_product_unit_create',
            ],
            [
                'id'    => 76,
                'title' => 'item_product_unit_edit',
            ],
            [
                'id'    => 77,
                'title' => 'item_product_unit_show',
            ],
            [
                'id'    => 78,
                'title' => 'item_product_unit_delete',
            ],
            [
                'id'    => 79,
                'title' => 'item_product_unit_access',
            ],
            [
                'id'    => 80,
                'title' => 'management_access',
            ],
            [
                'id'    => 81,
                'title' => 'crm_access',
            ],
            [
                'id'    => 82,
                'title' => 'customer_create',
            ],
            [
                'id'    => 83,
                'title' => 'customer_edit',
            ],
            [
                'id'    => 84,
                'title' => 'customer_show',
            ],
            [
                'id'    => 85,
                'title' => 'customer_delete',
            ],
            [
                'id'    => 86,
                'title' => 'customer_access',
            ],
            [
                'id'    => 87,
                'title' => 'audit_log_show',
            ],
            [
                'id'    => 88,
                'title' => 'audit_log_access',
            ],
            [
                'id'    => 89,
                'title' => 'address_create',
            ],
            [
                'id'    => 90,
                'title' => 'address_edit',
            ],
            [
                'id'    => 91,
                'title' => 'address_show',
            ],
            [
                'id'    => 92,
                'title' => 'address_delete',
            ],
            [
                'id'    => 93,
                'title' => 'address_access',
            ],
            [
                'id'    => 94,
                'title' => 'report_access',
            ],
            [
                'id'    => 95,
                'title' => 'notification_access',
            ],
            [
                'id'    => 96,
                'title' => 'discount_create',
            ],
            [
                'id'    => 97,
                'title' => 'discount_edit',
            ],
            [
                'id'    => 98,
                'title' => 'discount_show',
            ],
            [
                'id'    => 99,
                'title' => 'discount_delete',
            ],
            [
                'id'    => 100,
                'title' => 'discount_access',
            ],
            [
                'id'    => 101,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 102,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 103,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 104,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 105,
                'title' => 'promo_create',
            ],
            [
                'id'    => 106,
                'title' => 'promo_edit',
            ],
            [
                'id'    => 107,
                'title' => 'promo_show',
            ],
            [
                'id'    => 108,
                'title' => 'promo_delete',
            ],
            [
                'id'    => 109,
                'title' => 'promo_access',
            ],
            [
                'id'    => 110,
                'title' => 'marketing_access',
            ],
            [
                'id'    => 111,
                'title' => 'banner_create',
            ],
            [
                'id'    => 112,
                'title' => 'banner_edit',
            ],
            [
                'id'    => 113,
                'title' => 'banner_show',
            ],
            [
                'id'    => 114,
                'title' => 'banner_delete',
            ],
            [
                'id'    => 115,
                'title' => 'banner_access',
            ],
            [
                'id'    => 116,
                'title' => 'finance_access',
            ],
            [
                'id'    => 117,
                'title' => 'payment_category_create',
            ],
            [
                'id'    => 118,
                'title' => 'payment_category_edit',
            ],
            [
                'id'    => 119,
                'title' => 'payment_category_show',
            ],
            [
                'id'    => 120,
                'title' => 'payment_category_delete',
            ],
            [
                'id'    => 121,
                'title' => 'payment_category_access',
            ],
            [
                'id'    => 122,
                'title' => 'payment_type_create',
            ],
            [
                'id'    => 123,
                'title' => 'payment_type_edit',
            ],
            [
                'id'    => 124,
                'title' => 'payment_type_show',
            ],
            [
                'id'    => 125,
                'title' => 'payment_type_delete',
            ],
            [
                'id'    => 126,
                'title' => 'payment_type_access',
            ],
            [
                'id'    => 127,
                'title' => 'activity_comment_create',
            ],
            [
                'id'    => 128,
                'title' => 'activity_comment_edit',
            ],
            [
                'id'    => 129,
                'title' => 'activity_comment_show',
            ],
            [
                'id'    => 130,
                'title' => 'activity_comment_delete',
            ],
            [
                'id'    => 131,
                'title' => 'activity_comment_access',
            ],
            [
                'id'    => 132,
                'title' => 'order_edit',
            ],
            [
                'id'    => 133,
                'title' => 'order_show',
            ],
            [
                'id'    => 134,
                'title' => 'order_delete',
            ],
            [
                'id'    => 135,
                'title' => 'order_access',
            ],
            [
                'id'    => 136,
                'title' => 'order_tracking_show',
            ],
            [
                'id'    => 137,
                'title' => 'order_tracking_access',
            ],
            [
                'id'    => 138,
                'title' => 'tax_invoice_create',
            ],
            [
                'id'    => 139,
                'title' => 'tax_invoice_edit',
            ],
            [
                'id'    => 140,
                'title' => 'tax_invoice_show',
            ],
            [
                'id'    => 141,
                'title' => 'tax_invoice_delete',
            ],
            [
                'id'    => 142,
                'title' => 'tax_invoice_access',
            ],
            [
                'id'    => 143,
                'title' => 'order_detail_show',
            ],
            [
                'id'    => 144,
                'title' => 'order_detail_access',
            ],
            [
                'id'    => 145,
                'title' => 'payment_management_access',
            ],
            [
                'id'    => 146,
                'title' => 'payment_create',
            ],
            [
                'id'    => 147,
                'title' => 'payment_edit',
            ],
            [
                'id'    => 148,
                'title' => 'payment_show',
            ],
            [
                'id'    => 149,
                'title' => 'payment_delete',
            ],
            [
                'id'    => 150,
                'title' => 'payment_access',
            ],
            [
                'id'    => 151,
                'title' => 'shipment_create',
            ],
            [
                'id'    => 152,
                'title' => 'shipment_edit',
            ],
            [
                'id'    => 153,
                'title' => 'shipment_show',
            ],
            [
                'id'    => 154,
                'title' => 'shipment_delete',
            ],
            [
                'id'    => 155,
                'title' => 'shipment_access',
            ],
            [
                'id'    => 156,
                'title' => 'invoice_create',
            ],
            [
                'id'    => 157,
                'title' => 'invoice_edit',
            ],
            [
                'id'    => 158,
                'title' => 'invoice_show',
            ],
            [
                'id'    => 159,
                'title' => 'invoice_delete',
            ],
            [
                'id'    => 160,
                'title' => 'invoice_access',
            ],
            [
                'id'    => 161,
                'title' => 'target_create',
            ],
            [
                'id'    => 162,
                'title' => 'target_edit',
            ],
            [
                'id'    => 163,
                'title' => 'target_show',
            ],
            [
                'id'    => 164,
                'title' => 'target_delete',
            ],
            [
                'id'    => 165,
                'title' => 'target_access',
            ],
            [
                'id'    => 166,
                'title' => 'catalogue_create',
            ],
            [
                'id'    => 167,
                'title' => 'catalogue_edit',
            ],
            [
                'id'    => 168,
                'title' => 'catalogue_show',
            ],
            [
                'id'    => 169,
                'title' => 'catalogue_delete',
            ],
            [
                'id'    => 170,
                'title' => 'catalogue_access',
            ],
            [
                'id'    => 171,
                'title' => 'stock_edit',
            ],
            [
                'id'    => 172,
                'title' => 'stock_show',
            ],
            [
                'id'    => 173,
                'title' => 'stock_access',
            ],
            [
                'id'    => 174,
                'title' => 'stock_transfer_create',
            ],
            [
                'id'    => 175,
                'title' => 'stock_transfer_show',
            ],
            [
                'id'    => 176,
                'title' => 'stock_transfer_access',
            ],
            [
                'id'    => 177,
                'title' => 'supervisor_type_create',
            ],
            [
                'id'    => 178,
                'title' => 'supervisor_type_edit',
            ],
            [
                'id'    => 179,
                'title' => 'supervisor_type_show',
            ],
            [
                'id'    => 180,
                'title' => 'supervisor_type_delete',
            ],
            [
                'id'    => 181,
                'title' => 'supervisor_type_access',
            ],
            [
                'id'    => 182,
                'title' => 'target_schedule_create',
            ],
            [
                'id'    => 183,
                'title' => 'target_schedule_edit',
            ],
            [
                'id'    => 184,
                'title' => 'target_schedule_show',
            ],
            [
                'id'    => 185,
                'title' => 'target_schedule_delete',
            ],
            [
                'id'    => 186,
                'title' => 'target_schedule_access',
            ],
            [
                'id'    => 187,
                'title' => 'product_brand_create',
            ],
            [
                'id'    => 188,
                'title' => 'product_brand_edit',
            ],
            [
                'id'    => 189,
                'title' => 'product_brand_show',
            ],
            [
                'id'    => 190,
                'title' => 'product_brand_delete',
            ],
            [
                'id'    => 191,
                'title' => 'product_brand_access',
            ],
            [
                'id'    => 192,
                'title' => 'product_model_create',
            ],
            [
                'id'    => 193,
                'title' => 'product_model_edit',
            ],
            [
                'id'    => 194,
                'title' => 'product_model_show',
            ],
            [
                'id'    => 195,
                'title' => 'product_model_delete',
            ],
            [
                'id'    => 196,
                'title' => 'product_model_access',
            ],
            [
                'id'    => 197,
                'title' => 'product_version_create',
            ],
            [
                'id'    => 198,
                'title' => 'product_version_edit',
            ],
            [
                'id'    => 199,
                'title' => 'product_version_show',
            ],
            [
                'id'    => 200,
                'title' => 'product_version_delete',
            ],
            [
                'id'    => 201,
                'title' => 'product_version_access',
            ],
            [
                'id'    => 202,
                'title' => 'product_category_code_create',
            ],
            [
                'id'    => 203,
                'title' => 'product_category_code_edit',
            ],
            [
                'id'    => 204,
                'title' => 'product_category_code_show',
            ],
            [
                'id'    => 205,
                'title' => 'product_category_code_delete',
            ],
            [
                'id'    => 206,
                'title' => 'product_category_code_access',
            ],
            [
                'id'    => 207,
                'title' => 'covering_create',
            ],
            [
                'id'    => 208,
                'title' => 'covering_edit',
            ],
            [
                'id'    => 209,
                'title' => 'covering_show',
            ],
            [
                'id'    => 210,
                'title' => 'covering_delete',
            ],
            [
                'id'    => 211,
                'title' => 'covering_access',
            ],
            [
                'id'    => 212,
                'title' => 'colour_create',
            ],
            [
                'id'    => 213,
                'title' => 'colour_edit',
            ],
            [
                'id'    => 214,
                'title' => 'colour_show',
            ],
            [
                'id'    => 215,
                'title' => 'colour_delete',
            ],
            [
                'id'    => 216,
                'title' => 'colour_access',
            ],
            [
                'id'    => 217,
                'title' => 'profile_password_edit',
            ],
            [
                'id'    => 218,
                'title' => 'profile_password_edit',
            ],
            [
                'id'    => 219,
                'title' => 'import_management_access',
            ],
            [
                'id'    => 220,
                'title' => 'import_show',
            ],
            [
                'id'    => 221,
                'title' => 'import_create',
            ],
            [
                'id'    => 222,
                'title' => 'customer_category_create',
            ],
            [
                'id'    => 223,
                'title' => 'customer_category_edit',
            ],
            [
                'id'    => 224,
                'title' => 'customer_category_show',
            ],
            [
                'id'    => 225,
                'title' => 'customer_category_delete',
            ],
            [
                'id'    => 226,
                'title' => 'customer_category_access',
            ],
            [
                'id'    => 227,
                'title' => 'report_menu_access',
            ],
            [
                'id'    => 228,
                'title' => 'report_create',
            ],
            [
                'id'    => 229,
                'title' => 'report_edit',
            ],
            [
                'id'    => 230,
                'title' => 'report_show',
            ],
            [
                'id'    => 231,
                'title' => 'report_delete',
            ],
            [
                'id'    => 232,
                'title' => 'lead_category_create',
            ],
            [
                'id'    => 233,
                'title' => 'lead_category_edit',
            ],
            [
                'id'    => 234,
                'title' => 'lead_category_show',
            ],
            [
                'id'    => 235,
                'title' => 'lead_category_delete',
            ],
            [
                'id'    => 236,
                'title' => 'lead_category_access',
            ],
            [
                'id'    => 237,
                'title' => 'brand_category_create',
            ],
            [
                'id'    => 238,
                'title' => 'brand_category_edit',
            ],
            [
                'id'    => 239,
                'title' => 'brand_category_show',
            ],
            [
                'id'    => 240,
                'title' => 'brand_category_delete',
            ],
            [
                'id'    => 241,
                'title' => 'brand_category_access',
            ],
            [
                'id'    => 242,
                'title' => 'sales_estimation_access',
            ],
            [
                'id'    => 243,
                'title' => 'sales_estimation_create',
            ],
            [
                'id'    => 244,
                'title' => 'sales_estimation_edit',
            ],
            [
                'id'    => 245,
                'title' => 'sales_estimation_show',
            ],
            [
                'id'    => 246,
                'title' => 'sales_estimation_delete',
            ],
            [
                'id'    => 247,
                'title' => 'interior_design_access',
            ],
            [
                'id'    => 248,
                'title' => 'interior_design_create',
            ],
            [
                'id'    => 249,
                'title' => 'interior_design_edit',
            ],
            [
                'id'    => 250,
                'title' => 'interior_design_show',
            ],
            [
                'id'    => 251,
                'title' => 'interior_design_delete',
            ],
            ...self::allCrudPermission('company_account')
        ];

        collect($permissions)->each(function ($data) {
            // ignore id
            Permission::firstOrCreate([
                'title' => $data['title']
            ]);
        });
    }

    protected static function allCrudPermission($key): array
    {
        $data = [
            "{$key}_create",
            "{$key}_edit",
            "{$key}_show",
            "{$key}_delete",
            "{$key}_access",
        ];

        return collect($data)->map(function ($permission) {
            return ['title' => $permission];
        })->all();
    }
}
