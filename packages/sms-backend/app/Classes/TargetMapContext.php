<?php

namespace App\Classes;

use App\Http\Requests\API\V1\Cart\SyncCartRequest;
use App\Models\ProductUnit;
use Database\Factories\CartItemFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;

class TargetMapContext implements JsonSerializable, Arrayable
{
    public function __construct(public int $id, public string $class, public string $label, public int $value)
    {
    }

    public static function make(int $id, string $class, string $label, int $value)
    {
        return new static($id, $class, $label, $value);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    #[ArrayShape(['id' => "int", 'class' => "string", 'label' => "string", 'value' => "int"])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'class' => $this->class,
            'label' => $this->label,
            'value' => $this->value,
        ];
    }

    public function toTargetLineArray()
    {
        
    }

    #[Pure]
    public static function fromArray(array $data): static
    {
        return new static($data['id'], $data['class'], $data['label'], $data['value']);
    }

    public function combine(TargetMapContext $context): static
    {
        $this->value += $context->value;
        return $this;
    }
}
