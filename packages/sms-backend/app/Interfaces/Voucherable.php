<?php


namespace App\Interfaces;

use App\Enums\VoucherError;
use App\Models\Voucher;
use Illuminate\Support\Collection;

/**
 * Apply to model that has price and is Voucherable such as
 * cart, cartItemLines, order, orderDetail
 *
 * Interface Voucherable
 * @package App\Interfaces
 */
interface Voucherable
{
    /**
     * Get collection of VoucherableLine models from this
     * Voucherable model
     * @return Collection
     */
    // public function getVoucherableLines(): ?Collection;

    // public function updatePricesFromItemLine();

    public function getCustomerId(): int;

    public function getId(): ?int;

    public function getTotalPriceVoucher(): int;

    public function setTotalPriceVoucher(int $price);

    public function getSumTotalVoucher(): int;
    public function getTotalVoucher(): int;

    public function setTotalVoucher(int $price);

    public function setSumTotalVoucher(int $price);
    public function setVoucher(Voucher $Voucher);

    public function getVoucher(): ?Voucher;
    public function getOrderVouchers();

    public function setVoucherError(VoucherError $error);

    public function getVoucherError(): ?VoucherError;

    /**
     * Reset Voucher price, total price, and remove Voucher
     * @return void
     */
    public function resetVoucher();

    /**
     * reset Voucher prices without actually removing the Voucher itself
     * @return mixed
     */
    public function resetVoucherPrices();
}
