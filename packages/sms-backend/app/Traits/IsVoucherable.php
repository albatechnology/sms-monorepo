<?php


namespace App\Traits;

use App\Enums\VoucherError;
use App\Interfaces\Voucherable;
// use App\Interfaces\VoucherableLine;
use App\Models\Voucher;
use App\Services\OrderService;
use Exception;

trait IsVoucherable
{

    // region SETTER AND GETTERS
    // public function getId(): ?int
    // {
    //     return $this->id;
    // }

    /**
     * @param Voucher $voucher
     * @throws Exception
     */
    public function setVoucher(Voucher $voucher)
    {
        OrderService::setVoucher($this, $voucher);
    }

    public function getVoucher(): ?Voucher
    {
        return $this->voucher ?? null;
    }

    public function getOrderVouchers()
    {
        return $this->order_Vouchers ?? null;
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class)->withTrashed();
    }

    public function setVoucherError(VoucherError $error)
    {
        $this->voucher_error = $error;
    }

    // public function updatePricesFromItemLine()
    // {
    //     $item_lines = $this->getVoucherableLines();

    //     $this->setTotalPriceVoucher($item_lines->sum(fn(VoucherableLine $line) => $line->getTotalPriceVoucher()));
    //     $this->setTotalVoucher($item_lines->sum(fn(VoucherableLine $line) => $line->getTotalVoucher()));
    // }

    public function setTotalPriceVoucher(int $price)
    {
        $this->total_price = $price;
    }

    // endregion

    public function setTotalVoucher(int $price)
    {
        $this->total_voucher = $price;
    }

    // summary of total Voucher harus di unset sebelum di save
    public function setSumTotalVoucher(int $price)
    {
        $this->sum_total_voucher = $price + ($this->total_voucher ?? 0);
    }

    public function getVoucherError(): ?VoucherError
    {
        return $this?->voucher_error;
    }

    /**
     * @throws Exception
     */
    public function resetVoucherPrices()
    {
        // $this->checkInstance();

        $this->setTotalPriceVoucher($this->getTotalPriceVoucher() + $this->getTotalVoucher());
        $this->setTotalVoucher(0);
        $this->setSumTotalVoucher(0);

        if ($this instanceof Voucherable) {
            // $this->getVoucherableLines()->each(fn(VoucherableLine $line) => $line->resetVoucher());
        }
    }

    /**
     *
     * @throws Exception
     */
    public function resetVoucher()
    {
        // $this->checkInstance();
        $this->resetVoucherPrices();

        // $this->voucher    = null;
        // $this->voucher_id = null;
    }

    // protected function checkInstance()
    // {
    //     // if (!$this instanceof Voucherable && !$this instanceof VoucherableLine) {
    //     if (!$this instanceof Voucherable) {
    //         throw new Exception('Voucher applied to non Voucherable class.');
    //     }
    // }

    public function getTotalPriceVoucher(): int
    {
        return $this->total_price ?? 0;
    }

    public function getTotalVoucher(): int
    {
        return $this->total_voucher ?? 0;
    }

    public function getSumTotalVoucher(): int
    {
        return $this->sum_total_voucher ?? 0;
    }
}
