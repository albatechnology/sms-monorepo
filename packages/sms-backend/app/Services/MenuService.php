<?php

namespace App\Services;

use App\Classes\Menu;
use App\Classes\Submenu;

class MenuService
{
    public static function menu()
    {

        return [
            self::menuCrm(),
            self::menuCorporate(),
            self::menuProduct(),
            self::menuMarketing(),
            // self::menuWarehouse(),
            self::menuReport(),
            self::menuManagement(),
            self::menuFinance(),
            self::menuImports(),
        ];
    }

    protected static function menuCrm()
    {
        $submenu_leads = new Submenu(
            'lead_access',
            route("admin.leads.index"),
            'admin/leads',
            'fas fa-users',
            trans('cruds.lead.title'),
        );

        $submenu_unhandle_leads = new Submenu(
            'unhandle_lead_access',
            route("admin.unhandle-leads.index"),
            'admin/unhandle-leads',
            'fas fa-users',
            trans('cruds.unhandleLead.title'),
        );

        $submenu_activities = new Submenu(
            'activity_access',
            route("admin.activities.index"),
            'admin/activities',
            'fas fa-comments',
            trans('cruds.activity.title'),
        );

        // $submenu_activity_comments = new Submenu(
        //     'activity_comment_access',
        //     route("admin.activity-comments.index"),
        //     'admin/activity-comments',
        //     'fas fa-comments',
        //     trans('cruds.activityComment.title'),
        // );

        $submenu_lead_categories = new Submenu(
            'lead_category_access',
            route("admin.lead-categories.index"),
            'admin/lead-categories',
            'fas fa-tags',
            trans('cruds.leadCategory.title'),
        );

        $submenu_sub_lead_categories = new Submenu(
            'sub_lead_category_access',
            route("admin.sub-lead-categories.index"),
            'admin/sub-lead-categories',
            'fas fa-tags',
            trans('cruds.subLeadCategory.title'),
        );

        $submenu_customer = new Submenu(
            'customer_access',
            route("admin.customers.index"),
            'admin/customers',
            'fas fa-user-alt',
            trans('cruds.customer.title'),
        );

        $submenu_address = new Submenu(
            'address_access',
            route("admin.addresses.index"),
            'admin/addresses',
            'fas fa-address-book',
            trans('cruds.address.title'),
        );

        // $submenu_interior_designs = new Submenu(
        //     'interior_design_access',
        //     route("admin.interior-designs.index"),
        //     'admin/interior-design',
        //     'fas fa-building',
        //     trans('cruds.interiorDesign.title'),
        // );

        // $submenu_tax_invoice = new Submenu(
        //     'tax_invoice_access',
        //     route("admin.tax-invoices.index"),
        //     'admin/tax-invoices',
        //     'fas fa-file-invoice',
        //     trans('cruds.taxInvoice.title'),
        //     true,
        // );

        // $submenu_notification_access = new Submenu(
        //     'notification_access',
        //     route("admin.notifications.index"),
        //     'admin/notifications',
        //     'fas fa-comment-alt',
        //     trans('cruds.notification.title'),
        //     true,
        // );

        return new Menu(
            'crm_access',
            'fas fa-hands-helping',
            trans('cruds.crm.title'),
            ...[
                $submenu_leads,
                $submenu_unhandle_leads,
                $submenu_activities,
                // $submenu_activity_comments,
                $submenu_lead_categories,
                $submenu_sub_lead_categories,
                $submenu_customer,
                $submenu_address,
                // $submenu_interior_designs,
                // $submenu_tax_invoice,
                // $submenu_notification_access,
            ]
        );
    }

    protected static function menuProduct()
    {
        $categories = new Submenu(
            'product_category_access',
            route("admin.product-categories.index"),
            'admin/product-categories',
            'fas fa-tags',
            trans('cruds.productCategory.title'),
        );

        // $tags = new Submenu(
        //     'product_tag_access',
        //     route("admin.product-tags.index"),
        //     'admin/product-tags',
        //     'fas fa-tags',
        //     trans('cruds.productTag.title'),
        //     1,
        // );

        $products = new Submenu(
            'product_access',
            route("admin.products.index"),
            'admin/products',
            'fas fa-shopping-cart',
            trans('cruds.product.title'),
        );

        // $units = new Submenu(
        //     'product_unit_access',
        //     route("admin.product-units.index"),
        //     'admin/product-units',
        //     'fas fa-shopping-cart',
        //     trans('cruds.productUnit.title'),
        // );

        // $catalogue = new Submenu(
        //     'catalogue_access',
        //     route("admin.catalogues.index"),
        //     'admin/catalogues',
        //     'fas fa-book',
        //     trans('cruds.catalogue.title'),
        //     1,
        // );

        $brand_category = new Submenu(
            'brand_category_access',
            route("admin.brand-categories.index"),
            'admin/brand-categories',
            'fas fa-tag',
            trans('cruds.brandCategory.title'),
        );

        $brand = new Submenu(
            'product_brand_access',
            route("admin.product-brands.index"),
            'admin/product-brands',
            'fas fa-tag',
            trans('cruds.productBrand.title'),
        );

        // $model = new Submenu(
        //     'product_model_access',
        //     route("admin.product-models.index"),
        //     'admin/product-models',
        //     'fas fa-tag',
        //     trans('cruds.productModel.title'),
        // );

        // $version = new Submenu(
        //     'product_version_access',
        //     route("admin.product-versions.index"),
        //     'admin/product-versions',
        //     'fas fa-tag',
        //     trans('cruds.productVersion.title'),
        // );

        // $categoryCode = new Submenu(
        //     'product_category_code_access',
        //     route("admin.product-category-codes.index"),
        //     'admin/product-category-codes',
        //     'fas fa-tag',
        //     trans('cruds.productCategoryCode.title'),
        // );

        // $covering = new Submenu(
        //     'covering_access',
        //     route("admin.coverings.index"),
        //     'admin/coverings',
        //     'fas fa-gift',
        //     trans('cruds.covering.title'),
        // );

        // $colour = new Submenu(
        //     'colour_access',
        //     route("admin.colours.index"),
        //     'admin/colours',
        //     'fas fa-palette',
        //     trans('cruds.colour.title'),
        // );

        return new Menu(
            'product_management_access',
            'fas fa-shopping-cart',
            trans('cruds.productManagement.title'),
            ...[
                $categories,
                // $tags,
                $products,
                // $units,
                // $catalogue,
                $brand_category,
                $brand,
                // $model,
                // $version,
                // $categoryCode,
                // $covering,
                // $colour,
            ]
        );
    }

    protected static function menuMarketing()
    {
        $discount = new Submenu(
            'discount_access',
            route("admin.discounts.index"),
            'admin/discounts',
            'fas fa-money-bill-alt',
            trans('cruds.discount.title'),
            0,
        );

        // $promoCategory = new Submenu(
        //     'promo_category_access',
        //     route("admin.promo-categories.index"),
        //     'admin/promo-categories',
        //     'fas fa-gift',
        //     trans('cruds.promoCategory.title'),
        //     0,
        // );

        $promo = new Submenu(
            'promo_access',
            route("admin.promos.index"),
            'admin/promos',
            'fas fa-gift',
            trans('cruds.promo.title'),
            0,
        );

        // $voucher = new Submenu(
        //     'voucher_access',
        //     route("admin.vouchers.index"),
        //     'admin/vouchers',
        //     'fas fa-gift',
        //     trans('cruds.voucher.title'),
        //     0,
        // );

        // $banner = new Submenu(
        //     'banner_access',
        //     route("admin.banners.index"),
        //     'admin/banners',
        //     'fas fa-flag',
        //     trans('cruds.banner.title'),
        //     1,
        // );

        return new Menu(
            'marketing_access',
            'fas fa-percent',
            trans('cruds.marketing.title'),
            ...[
                // $promoCategory,
                $promo,
                // $voucher,
                $discount,
                // $banner,
            ]
        );
    }

    protected static function menuCorporate()
    {
        // $company = new Submenu(
        //     'company_access',
        //     route("admin.companies.index"),
        //     'admin/companies',
        //     'fas fa-building',
        //     trans('cruds.company.title'),
        //     0,
        // );

        // $account = new Submenu(
        //     'company_account_access',
        //     route("admin.company-accounts.index"),
        //     'admin/company-accounts',
        //     'fas fa-piggy-bank',
        //     trans('cruds.companyAccount.title'),
        //     0,
        // );

        // $channelCategory = new Submenu(
        //     'channel_category_access',
        //     route("admin.channel-categories.index"),
        //     'admin/channel-categories',
        //     'fas fa-tags',
        //     trans('cruds.channelCategory.title'),
        //     0,
        // );

        $channel = new Submenu(
            'channel_access',
            route("admin.channels.index"),
            'admin/channels',
            'fas fa-store-alt',
            trans('cruds.channel.title'),
            0,
        );

        return new Menu(
            'corporate_access',
            'fas fa-building',
            trans('cruds.corporate.title'),
            ...[
                // $company,
                // $account,
                // $channelCategory,
                $channel,
            ]
        );
    }

    protected static function menuWarehouse()
    {
        $shipment = new Submenu(
            'shipment_access',
            route("admin.shipments.index"),
            'admin/shipments',
            'fas fa-truck',
            trans('cruds.shipment.title'),
            0,
        );

        $stock = new Submenu(
            'stock_access',
            route("admin.stocks.index"),
            'admin/stocks',
            'fas fa-box-open',
            trans('cruds.stock.title'),
            0,
        );

        $transfer = new Submenu(
            'stock_transfer_access',
            route("admin.stock-transfers.index"),
            'admin/stock-transfers',
            'fas fa-exchange-alt',
            trans('cruds.stockTransfer.title'),
            0,
        );

        return new Menu(
            'warehouse_access',
            'fas fa-warehouse',
            trans('cruds.warehouse.title'),
            ...[
                $shipment,
                $stock,
                $transfer
            ]
        );
    }

    protected static function menuReport()
    {
        $activity = new Submenu(
            'report_activity_access',
            route("admin.reports.activity"),
            'admin/reports/activity',
            'fas fa-comments',
            trans('cruds.report.activity'),
            0,
        );

        $activity_follow_up = new Submenu(
            'activity_follow_up_access',
            route("admin.reports.activityFollowUp"),
            'admin/reports/activity-follow-up',
            'fas fa-project-diagram',
            trans('cruds.report.activity_follow_up'),
            0,
        );


        $report = new Submenu(
            'report_access',
            route("admin.reports.index"),
            'admin/reports',
            'fas fa-book',
            trans('cruds.report.title'),
            0,
        );


        $target = new Submenu(
            'target_access',
            route("admin.targets.index"),
            'admin/targets',
            'fas fa-star',
            trans('cruds.target.title'),
            0,
        );

        $newTarget = new Submenu(
            'new_target_access',
            route("admin.new-targets.index"),
            'admin/new-targets',
            'fas fa-star',
            'New Target',
            0,
        );

        $sales_estimations = new Submenu(
            'sales_estimation_access',
            route("admin.salesEstimation.index"),
            'admin/sales-estimations',
            'fas fa-user',
            trans('cruds.salesEstimation.title'),
            0,
        );

        $followup_per_channel = new Submenu(
            'followup_per_channel_access',
            route("admin.followup-per-channels.index"),
            'admin/followup-per-channels',
            'fas fa-user',
            trans('cruds.followupPerChannels.title'),
            0,
        );

        return new Menu(
            'report_menu_access',
            'fas fa-book',
            trans('cruds.report.title'),
            ...[
                $report,
                $target,
                $newTarget,
                $sales_estimations,
                $activity,
                $activity_follow_up,
                $followup_per_channel,
            ]
        );
    }

    protected static function menuManagement()
    {

        // $target = new Submenu(
        //     'target_access',
        //     route("admin.targets.index"),
        //     'admin/targets',
        //     'fas fa-chart-line',
        //     trans('cruds.target.title'),
        //     1,
        // );

        // $schedule = new Submenu(
        //     'target_schedule_access',
        //     route("admin.target-schedules.index"),
        //     'admin/target-schedules',
        //     'fas fa-calendar-alt',
        //     trans('cruds.targetSchedule.title'),
        //     1,
        // );

        $supervisorType = new Submenu(
            'supervisor_type_access',
            route("admin.supervisor-types.index"),
            'admin/supervisor-types',
            'fas fa-chalkboard-teacher',
            trans('cruds.supervisorType.title'),
            0,
        );

        // $currency = new Submenu(
        //     'currency_access',
        //     route("admin.currencies.index"),
        //     'admin/currencies',
        //     'fas fa-money-bill-alt',
        //     trans('cruds.currency.title'),
        //     0,
        // );

        return new Menu(
            'management_access',
            'fas fa-cogs',
            trans('cruds.management.title'),
            ...[
                // $target,
                // $schedule,
                $supervisorType,
                // $currency,
            ]
        );
    }

    protected static function menuFinance()
    {
        $order = new Submenu(
            'order_access',
            route("admin.orders.index"),
            'admin/orders',
            'fas fa-cart-arrow-down',
            trans('cruds.order.title'),
            0,
        );

        // $orderDetail = new Submenu(
        //     'order_detail_access',
        //     route("admin.order-details.index"),
        //     'admin/order-details',
        //     'fas fa-cart-arrow-down',
        //     trans('cruds.orderDetail.title'),
        //     0,
        // );

        $paymentCategory = new Submenu(
            'payment_category_access',
            route("admin.payment-categories.index"),
            'admin/payment-categories',
            'fas fa-tags',
            trans('cruds.paymentCategory.title'),
            0,
        );

        $paymentType = new Submenu(
            'payment_type_access',
            route("admin.payment-types.index"),
            'admin/payment-types',
            'fas fa-tags',
            trans('cruds.paymentType.title'),
            0,
        );

        $payment = new Submenu(
            'payment_access',
            route("admin.payments.index"),
            'admin/payments',
            'fas fa-hand-holding-usd',
            trans('cruds.payment.title'),
            0,
        );

        return new Menu(
            'finance_access',
            'fas fa-dollar-sign',
            trans('cruds.finance.title'),
            ...[
                $order,
                // $orderDetail,
                $payment,
                $paymentCategory,
                $paymentType,
            ]
        );
    }

    protected static function menuImports()
    {
        $import = new Submenu(
            'import_management_access',
            route("admin.import-batches.index"),
            'admin/import',
            'fas fa-file-csv',
            trans('cruds.importBatch.title'),
            0,
        );

        $export = new Submenu(
            'import_management_access',
            route("admin.exports.index"),
            'admin/exports',
            'fas fa-file-csv',
            trans('cruds.export.title'),
            0,
        );

        return new Menu(
            'import_management_access',
            'fas fa-file-csv',
            trans('cruds.import.title'),
            ...[
                $import,
                $export,
            ]
        );
    }
}
