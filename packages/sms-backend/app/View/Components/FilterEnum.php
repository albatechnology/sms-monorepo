<?php

namespace App\View\Components;

use App\Enums\BaseEnum;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use InvalidArgumentException;

class FilterEnum extends Component
{
    public Collection $enums;

    public function __construct(string $baseEnum)
    {
        if (!is_a($baseEnum, BaseEnum::class, true)) {
            throw new InvalidArgumentException("{$baseEnum} must be an Enum class.");
        }

        $this->enums = collect($baseEnum::getInstances());
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.filter-enum');
    }
}