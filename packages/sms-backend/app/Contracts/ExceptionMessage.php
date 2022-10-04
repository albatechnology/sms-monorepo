<?php

namespace App\Contracts;

/**
 * Contain custom exception messages
 *
 * Class Errors
 */
class ExceptionMessage
{
    const DiscountableNeedDiscount = 'DI01: Discountable model must have a discount to record discount use.';
    const DiscountableNeedCustomer = 'DI02: Discountable model must have a customer_id to record discount use.';
    const DiscountableNeedId       = 'DI03: Discountable model must have an id to record discount use.';

    const ImportBatchTypeMissingImporterClass = 'IM01: ImportBatch type "%s" does not have a valid importer class.';
    const ImportFileMissingFromRequest        = 'IM02: Import file "%s" not found on request.';
}
