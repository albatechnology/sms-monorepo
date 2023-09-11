<?php

namespace App\Enums;

use App\Models\Activity;
use App\Models\Order;
use App\Pipes\Reportable\ActivityCount;
use App\Pipes\Reportable\DealsInvoicePrice;
use App\Pipes\Reportable\DealsOrderCount;
use App\Pipes\Reportable\DealsPaymentPrice;

/**
 * @method static static DEALS_ORDER_PRICE()
 * @method static static DEALS_ORDER_COUNT()
 * @method static static ACTIVITY_COUNT()
 * @method static static NEW_LEAD_COUNT()
 */
final class TargetType extends BaseEnum
{
    public const DEALS_ORDER_PRICE = 'DEALS_ORDER_PRICE';
    public const DEALS_ORDER_COUNT = 'DEALS_ORDER_COUNT';
    public const ACTIVITY_COUNT = 'ACTIVITY_COUNT';
    public const LEAD_COUNT = 'LEAD_COUNT';
    public const NEW_LEAD_COUNT = 'NEW_LEAD_COUNT';

    public static function getDescription($value): string
    {
        // Deal are order with payment status down_payment or settlement, and not cancelled

        return match ($value) {
            self::DEALS_ORDER_PRICE => 'Calculate the total invoice price from all orders that has reached a deal',
            self::DEALS_ORDER_COUNT => 'Calculate the number of deal order made.',
            self::ACTIVITY_COUNT => 'Calculate the number of activity',
            self::LEAD_COUNT => 'Calculate the number of lead',
            self::NEW_LEAD_COUNT => 'Calculate the number of new lead',
            default => self::getKey($value),
        };
    }

    public static function getDefaultInstances(): array
    {
        return collect(self::getInstances())
            ->filter(function ($targetType) {
                return $targetType->isDefault();
            })
            ->all();
    }

    /**
     * Specify target that should be created by default.
     * Any target that does not depend on a specific model row
     * such as product brand should be default
     * @return bool
     */
    public function isDefault()
    {
        return $this->in(
            [
                self::DEALS_ORDER_PRICE,
                self::DEALS_ORDER_COUNT,
                self::ACTIVITY_COUNT,
                self::LEAD_COUNT,
                self::NEW_LEAD_COUNT
            ]
        );
    }

    public function getChartType(): TargetChartType
    {
        return match ($this->value) {
            self::DEALS_ORDER_PRICE,
            self::DEALS_ORDER_COUNT => TargetChartType::SINGLE(),

            self::ACTIVITY_COUNT => TargetChartType::MULTIPLE()
        };
    }

    public function isPrice(): bool
    {
        return in_array(
            $this->value,
            [
                self::DEALS_ORDER_PRICE,
            ],
            true
        );
    }

    /**
     * Get order for sorting for api response of target
     */
    public function getPriority(): int
    {
        return match ($this->value) {
            self::DEALS_ORDER_PRICE => 1,
            self::DEALS_ORDER_COUNT => 3,
            self::ACTIVITY_COUNT => 5,
            default => 99,
        };
    }

    public static function allReportablePipes(): array
    {
        return [
            DealsInvoicePrice::class,
            DealsOrderCount::class,
            DealsPaymentPrice::class,
            ActivityCount::class,
            // OrderSettlementCount::class,
            // DealsBrandPrice::class,
        ];
    }

    public function getTargetLineModelClass(): ?string
    {
        return match ($this->value) {
            default => null,
        };
    }

    public function getBaseModel(): ?string
    {
        return match ($this->value) {
            self::DEALS_ORDER_PRICE,
            self::DEALS_ORDER_COUNT => Order::class,
            self::ACTIVITY_COUNT => Activity::class,
            default => null,
        };
    }
}
