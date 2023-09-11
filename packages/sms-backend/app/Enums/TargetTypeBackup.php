<?php

namespace App\Enums;

use App\Models\Activity;
use App\Models\Order;
use App\Models\ProductBrand;
use App\Models\ProductModel;
use App\Pipes\Reportable\ActivityCount;
use App\Pipes\Reportable\DealsBrandPrice;
use App\Pipes\Reportable\DealsInvoicePrice;
use App\Pipes\Reportable\DealsOrderCount;
use App\Pipes\Reportable\DealsPaymentPrice;
use App\Pipes\Reportable\OrderSettlementCount;

/**
 * @method static static DEALS_INVOICE_PRICE()
 * @method static static DEALS_PAYMENT_PRICE()
 * @method static static DEALS_BRAND_PRICE()
 * @method static static DEALS_MODEL_PRICE()
 * @method static static DEALS_ORDER_COUNT()
 * @method static static DEALS_BRAND_COUNT()
 * @method static static DEALS_MODEL_COUNT()
 * @method static static ACTIVITY_COUNT()
 * @method static static ACTIVITY_COUNT_CLOSED()
 * @method static static ORDER_SETTLEMENT_COUNT()
 */
final class TargetTypeBackup extends BaseEnum
{
    public const DEALS_INVOICE_PRICE = 'DEALS_INVOICE_PRICE';
    public const DEALS_PAYMENT_PRICE = 'DEALS_PAYMENT_PRICE';
    public const DEALS_BRAND_PRICE   = 'DEALS_BRAND_PRICE';
    public const DEALS_MODEL_PRICE   = 'DEALS_MODEL_PRICE';
    public const DEALS_ORDER_COUNT   = 'DEALS_ORDER_COUNT';
    public const DEALS_BRAND_COUNT      = 'DEALS_BRAND_COUNT';
    public const DEALS_MODEL_COUNT      = 'DEALS_MODEL_COUNT';
    public const ACTIVITY_COUNT         = 'ACTIVITY_COUNT';
    public const ACTIVITY_COUNT_CLOSED  = 'ACTIVITY_COUNT_CLOSED';
    public const ORDER_SETTLEMENT_COUNT = 'ORDER_SETTLEMENT_COUNT';

    public static function getDescription($value): string
    {
        // Deal are order with payment status down_payment or settlement, and not cancelled

        return match ($value) {
            self::DEALS_INVOICE_PRICE => 'Calculate the total invoice price from all orders that has reached a deal',
            self::DEALS_PAYMENT_PRICE => 'Calculate the total confirmed payment made against all orders that has reached a deal',
            self::DEALS_BRAND_PRICE => 'Calculate the total net price of all order detail from a selected brand.',
            self::DEALS_MODEL_PRICE => 'Calculate the total net price of all order detail from a selected model.',
            self::DEALS_ORDER_COUNT => 'Calculate the number of deal order made.',
            self::DEALS_BRAND_COUNT => 'Calculate the total quantity of product unit purchased as a deal for a given brand.',
            self::DEALS_MODEL_COUNT => 'Calculate the total quantity of product unit purchased as a deal for a given model.',
            self::ACTIVITY_COUNT => 'Calculate the number of activity',
            self::ACTIVITY_COUNT_CLOSED => 'Calculate the number of activity with status closed',
            self::ORDER_SETTLEMENT_COUNT => 'Calculate the number order that has been fully paid',
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
                self::DEALS_INVOICE_PRICE,
                self::DEALS_PAYMENT_PRICE,
                self::DEALS_ORDER_COUNT,
                self::ACTIVITY_COUNT,
                self::ORDER_SETTLEMENT_COUNT,
                self::DEALS_BRAND_PRICE,
            ]
        );
    }

    public function getChartType(): TargetChartType
    {
        return match ($this->value) {
            self::DEALS_INVOICE_PRICE,
            self::DEALS_PAYMENT_PRICE,
            self::DEALS_ORDER_COUNT,
            self::ACTIVITY_COUNT_CLOSED,
            self::ORDER_SETTLEMENT_COUNT => TargetChartType::SINGLE(),

            self::ACTIVITY_COUNT,
            self::DEALS_BRAND_PRICE,
            self::DEALS_MODEL_PRICE,
            self::DEALS_BRAND_COUNT,
            self::DEALS_MODEL_COUNT => TargetChartType::MULTIPLE()
        };
    }

    public function isPrice(): bool
    {
        return in_array(
            $this->value,
            [
                self::DEALS_INVOICE_PRICE,
                self::DEALS_PAYMENT_PRICE,
                self::DEALS_BRAND_PRICE,
                self::DEALS_MODEL_PRICE,
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
            self::DEALS_INVOICE_PRICE => 1,
            self::DEALS_PAYMENT_PRICE => 2,
            self::DEALS_ORDER_COUNT => 3,
            self::ORDER_SETTLEMENT_COUNT => 4,
            self::ACTIVITY_COUNT => 5,
            self::ACTIVITY_COUNT_CLOSED => 6,
            self::DEALS_BRAND_PRICE => 7,
            self::DEALS_MODEL_PRICE => 8,
            self::DEALS_BRAND_COUNT => 9,
            self::DEALS_MODEL_COUNT => 10,
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
            OrderSettlementCount::class,
            DealsBrandPrice::class,
        ];
    }

    public function getTargetLineModelClass(): ?string
    {
        return match ($this->value) {
            self::DEALS_BRAND_COUNT,
            self::DEALS_BRAND_PRICE => ProductBrand::class,
            self::DEALS_MODEL_COUNT,
            self::DEALS_MODEL_PRICE => ProductModel::class,
            default => null,
        };
    }

    public function getBaseModel(): ?string
    {
        return match ($this->value) {
            self::DEALS_INVOICE_PRICE,
            self::DEALS_PAYMENT_PRICE,
            self::DEALS_BRAND_PRICE,
            self::DEALS_MODEL_PRICE,
            self::DEALS_ORDER_COUNT,
            self::DEALS_BRAND_COUNT,
            self::ORDER_SETTLEMENT_COUNT,
            self::DEALS_MODEL_COUNT => Order::class,
            self::ACTIVITY_COUNT,
            self::ACTIVITY_COUNT_CLOSED => Activity::class,
            default => null,
        };
    }
}
