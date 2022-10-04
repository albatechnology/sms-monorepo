<?php

namespace App\View\Components;

use App\Enums\BaseEnum;
use App\Models\BaseModel;
use Illuminate\View\Component;
use Illuminate\View\View;

class ShowRow extends Component
{
    public $value;
    public $label;

    public const TYPE_PRICE = 'price';

    public function __construct(BaseModel $model, string $key, $value = null, $type = null)
    {
        $this->label = trans("cruds.{$model->getCrudKey()}.fields.{$key}");
        $this->value = $value ?? $model?->$key ?? '';

        if ($type === self::TYPE_PRICE) {
            $this->value = helper()->formatRupiah($this->value);
        }

        if ($this->value instanceof BaseEnum) {
            $this->value = $this->value->key;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('components.show-row');
    }
}