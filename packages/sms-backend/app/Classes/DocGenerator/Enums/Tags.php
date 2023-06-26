<?php

namespace App\Classes\DocGenerator\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static V1()
 * @method static static Address()
 * @method static static Activity()
 * @method static static ActivityComment()
 * @method static static Auth()
 * @method static static Cart()
 * @method static static Company()
 * @method static static Channel()
 * @method static static Discount()
 * @method static static Dummy()
 * @method static static Lead()
 * @method static static Order()
 * @method static static OrderDetail()
 * @method static static Product()
 * @method static static PushNotification()
 * @method static static ProductUnit()
 * @method static static Report()
 * @method static static ProductTag()
 * @method static static Promo()
 * @method static static ProductCategory()
 * @method static static QaTopic()
 * @method static static QaMessage()
 * @method static static User()
 * @method static static Voucher()
 */
final class Tags extends Enum
{
    public const Dummy = 'Dummy';
    public const V1    = 'V1';

    public const Activity         = 'Activity';
    public const ActivityComment  = 'ActivityComment';
    public const Address          = 'Address';
    public const Auth             = 'Auth';
    public const Cart             = 'Cart';
    public const CartDemand       = 'CartDemand';
    public const Channel          = 'Channel';
    public const Company          = 'Company';
    public const InteriorDesign   = 'InteriorDesign';
    public const Customer         = 'Customer';
    public const CustomerDeposit  = 'CustomerDeposit';
    public const Discount         = 'Discount';
    public const Lead             = 'Lead';
    public const Order            = 'Order';
    public const OrderDetail      = 'OrderDetail';
    public const Payment          = 'Payment';
    public const PushNotification = 'PushNotification';
    public const Product          = 'Product';
    public const ProductCategory  = 'ProductCategory';
    public const ProductUnit      = 'ProductUnit';
    public const ProductTag       = 'ProductTag';
    public const Promo            = 'Promo';
    public const PromoCategory    = 'PromoCategory';
    public const QaTopic          = 'QaTopic';
    public const QaMessage        = 'QaMessage';
    public const Rule             = 'Rule';
    public const Report           = 'Report';
    public const Stock            = 'Stock';
    public const Target           = 'Target';
    public const User             = 'User';
    public const Voucher         = 'Voucher';

    public static function getDescription($value): string
    {
        return match ($value) {
            self::Dummy => 'Unfinished API that returns static value rather than data driven results',
            self::V1 => 'All version 1 API',
            //self::V2 => 'All version 2 API',
            self::User => 'Application users',
            self::Auth => 'Application auth',
            self::Company => 'Application company',
            self::Channel => 'Application channel',
            self::Product => 'Application product',
            self::Address => 'Customer address',
            self::Voucher => 'Application Voucher',
            self::Rule => 'Endpoint that provides validation rule for another endpoint',
            default => self::getKey($value),
        };
    }

    public static function openApiTags(): array
    {
        return collect(self::getInstances())
            ->map(fn ($d) => ['name' => $d->value, 'description' => $d->description])
            ->values()
            ->toArray();
    }
}
