<?php

namespace App\View\Components;

use App\Enums\BaseEnum;
use App\Models\BaseModel;
use Illuminate\View\Component;
use Log;

class Enum extends Component
{
    public ?BaseEnum $enum = null;
    public string $enumClass;
    public string $label;
    public string $label_helper;

    public function __construct(
        public string $key,
        public BaseModel $model,
        public bool $required = true,
    )
    {
        $labelKey = "cruds.{$model->getCrudKey()}.fields.{$key}";

        $this->enumClass = $model->getCasts()[$key];

        if ($model->id) {
            // update page, existing record provided
            $this->enum = $model->$key;
        }

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
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.enum');
    }
}