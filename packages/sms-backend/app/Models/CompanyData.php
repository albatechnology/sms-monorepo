<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperCompanyData
 */
class CompanyData extends Model
{
    /**
     * @param int $company_id
     * @param Carbon|null $time
     * @return string
     */
    public static function getInvoiceNumber(int $company_id, Carbon $time = null): string
    {
        $invoice_id = DB::transaction(function () use ($company_id) {
            // Get current invoice id to use. We use lock for update
            // to prevent other thread to read this row until we update it
            $inv_id = DB::table('company_data')
                        ->where('company_id', $company_id)
                        ->lockForUpdate()
                        ->first('next_invoice_id')
                ->next_invoice_id;

            // increment the invoice id
            DB::table('company_data')
              ->where('company_id', $company_id)
              ->increment('next_invoice_id');

            return $inv_id;
        });

        $time = $time ?? now();
        return sprintf('INV%s%04d', $time->format('Ymd'), $invoice_id % 10000);
    }
}
