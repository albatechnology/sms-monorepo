<?php

namespace Database\Seeders;

use App\Models\TaxInvoice;
use Illuminate\Database\Seeder;

class TaxInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaxInvoice::factory()->count(40)->create();
    }
}
