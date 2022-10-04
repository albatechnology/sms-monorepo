<?php

namespace App\Classes;

use App\Enums\TargetBreakdownType;
use App\Interfaces\InTargetBreakdown;

class TargetBreakdown
{
    public function __construct(
        public TargetBreakdownType $enumType,
        public InTargetBreakdown $enumValue,
        public int $value,
    )
    {
    }
}
