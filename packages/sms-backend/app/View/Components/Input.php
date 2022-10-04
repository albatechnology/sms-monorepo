<?php

namespace App\View\Components;

use App\Models\BaseModel;
use Illuminate\View\Component;
use Illuminate\View\View;
use Log;

class Input extends Component
{
    public const TYPE_TEXT     = 'text';
    public const TYPE_NUMBER   = 'number';
    public const TYPE_DATETIME = 'datetime';

    public string $label;
    public string $label_helper;
    public string $input_type;

    public function __construct(
        public string $key,
        BaseModel $model,
        public mixed $value = '',
        string $labelKey = null,
        public string $type = 'text',
        public bool $required = true,
        public bool $disabled = false,
    )
    {
        $this->input_type = $type;

        $labelKey = $labelKey ?? "cruds.{$model->getCrudKey()}.fields.{$key}";

        // model is provided for edit
        if ($model->id) {
            $this->value = empty($value) ? $model->$key : $value;
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
     * @return View|string
     */
    public function render()
    {
        return match ($this->type) {
            self::TYPE_NUMBER => view('components.input_number'),
            default => view('components.input_text')
        };
    }
}