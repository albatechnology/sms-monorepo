<?php

namespace Tests\Unit\API\Business;

use App\Models\Discount;
use Illuminate\Support\Collection;

/**
 * Class CartTest
 * @package Tests\Unit\API
 */
class DiscountApiTest extends BaseApiTest
{
    protected Collection $discount;
    const default_discount_count = 3;

    /**
     * @dataProvider provideHiddenDiscountData
     * @return void
     */
    public function testDiscountIsHidden($data)
    {
        $discount = Discount::factory()->create($data);
        $this->get(route('discounts.index'))
             ->assertJsonMissing(['id' => $discount->id])
             ->assertJsonCount(self::default_discount_count, 'data');
    }

    public function provideHiddenDiscountData()
    {
        return [
            'has_activation_code'    => [
                'data' => ['activation_code' => 'CODETEST']
            ],
            'is_inactive_flag'       => [
                'data' => ['is_active' => false]
            ],
            'is_inactive_start_time' => [
                'data' => ['start_time' => now()->addDay()]
            ],
            'is_inactive_end_time'   => [
                'data' => [
                    'start_time' => now()->subDays(5),
                    'end_time'   => now()->subDay(),
                ]
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->discount = Discount::factory()->count(self::default_discount_count)->create();
    }
}