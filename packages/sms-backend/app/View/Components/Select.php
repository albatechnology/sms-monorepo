<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Log;

class Select extends Component
{
    public $options;
    public $optionLabel;
    public $label;
    public $label_helper;

    public function __construct(
        public string $key,
        string $class,
        string $optionClass,
        public $value = null,
        $optionLabel = 'name'
    )
    {
        $this->optionLabel = $optionLabel;
        $this->options     = $optionClass::tenanted()->get(['id', $optionLabel]);
        $labelKey          = $labelKey ?? "cruds.{$class::getCrudKey()}.fields.{$key}";

        // if trans doesnt exist
        if (trans($labelKey) == $labelKey) {
            Log::warning("Expected translation for {$labelKey} is missing.");
            $this->label        = $key;
            $this->label_helper = $key;
        } else {
            $this->label        = trans($labelKey);
            $this->label_helper = trans($labelKey . '_helper');
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('components.select');
    }
}