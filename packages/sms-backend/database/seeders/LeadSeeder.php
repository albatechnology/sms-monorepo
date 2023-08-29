<?php

namespace Database\Seeders;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\LeadCategory;
use App\Models\SubLeadCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leadCategory = LeadCategory::create([
            'subscribtion_user_id' => 2,
            'name' => 'Lead Category 1',
            'description' => 'description Lead Category 1',
        ]);

        SubLeadCategory::create([
            'lead_category_id' => $leadCategory->id,
            'name' => 'Lead Category 1',
            'description' => 'description Lead Category 1',
        ]);

        $user = User::where('email', 'sales@gmail.com')->first();
        Customer::all()->each(function ($customer) use ($user) {
            $user->leads()->create([
                'type' => LeadType::LEADS,
                'status' => LeadStatus::GREEN,
                'label' => $customer->full_name . ' - ' . date('d-m-Y'),
                'is_unhandled' => 0,
                'channel_id' => $user->channel_id ?? Channel::where('subscribtion_user_id', 2)->first()?->id ?? 1,
                'lead_category_id' => 1,
                'sub_lead_category_id' => 1,
                'is_new_customer' => 1,
                'customer_id' => $customer->id,
            ]);
        });


        // Excel::import(new LeadSeederImport, public_path('leads.xlsx'));
        // $code = 'L';

        // foreach(Channel::all() as $channel){
        //     foreach (range(1, 3) as $number) {
        //         $name = sprintf(
        //             'Lead %s-%s%s',
        //             Str::of($channel->name)->after(' '),
        //             $code,
        //             $number
        //         );
        //         Lead::factory()->create(
        //             [
        //                 "channel_id" => $channel->id,
        //                 "name" => $name,
        //             ]
        //         );
        //     }
        // }
    }
}
