<?php

namespace Database\Factories;

use App\Models\InteriorDesign;

class InteriorDesignFactory extends BaseFactory
{
    protected $model = InteriorDesign::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'                  => "Modern Culture V1 - Alba",
            'bum_id'                => "1",
            'sales_id'              => "1",
            'religion_id'           => "1",
            'company'               => "Albatech",
            'owner'                 => "Zulfi",
            'npwp'                  => "1234567890",
            'address'               => "Alam Sutera",
            'phone'                 => "081234567890",
            'email'                 => "zulfi@albatech.com",
            'bank_account_name'     => "BCA",
            'bank_account_holder'   => "Zulfi",
            'bank_account_number'   => "1234567890"
        ];
    }

    public function sample()
    {
        return $this->state([
            'name'                  => "Modern Culture V1 - Alba",
            'bum_id'                => "1",
            'sales_id'              => "1",
            'religion_id'           => "1",
            'company'               => "Albatech",
            'owner'                 => "Zulfi",
            'npwp'                  => "1234567890",
            'address'               => "Alam Sutera",
            'phone'                 => "081234567890",
            'email'                 => "zulfi@albatech.com",
            'bank_account_name'     => "BCA",
            'bank_account_holder'   => "Zulfi",
            'bank_account_number'   => "1234567890"
        ]);
    }
}
