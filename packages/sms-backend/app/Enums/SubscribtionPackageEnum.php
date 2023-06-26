<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SubscribtionPackageEnum extends Enum
{
    const MaxUsers = 'max_users';
    const MaxCustomers = 'max_customers';
    const MaxActivities = 'max_activities';
    const MaxLeads = 'max_leads';
    const MaxOrders = 'max_orders';
    const MaxBrands = 'max_brands';
    const MaxCategories = 'max_categories';
    const MaxProducts = 'max_products';
    const CanDiscount = 'can_discount';
    const CanApproval = 'can_approval';
    const CanMultiCompanies = 'can_multi_companies';
}
