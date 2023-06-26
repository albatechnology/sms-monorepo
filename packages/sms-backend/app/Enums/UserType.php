<?php

namespace App\Enums;

/**
 * @method static static DEFAULT()
 * @method static static SALES()
 * @method static static SUPERVISOR()
 * @method static static DIRECTOR()
 */
final class UserType extends BaseEnum
{
    // Default user dont have any access on mobile app
    const DEFAULT = 1;

    // Sales are the main target for majority on feature on the mobile app
    const SALES = 2;

    // User can be assigned to a supervisor. Supervisor get certain unique
    // feature access on mobile app, as well as downline target/reporting
    const SUPERVISOR = 3;

    // The board of director have access to all companies
    const DIRECTOR = 4;
    // const DIGITAL_MARKETING = 5;
    // const SALES_REFERRAL = 8;
}
