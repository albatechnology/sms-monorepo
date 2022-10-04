<?php


namespace App\Traits;

use App\Enums\BaseEnum;

trait CustomCastEnums
{
    /**
     * For api array casts enum to its key
     * @return array
     */
    public function toApiArray(): array
    {
        $enum_fields = collect($this->getEnumCasts())
            ->map(fn($v, $key) => $this->$key?->key)
            ->all();
        return array_merge($this->toArray(), $enum_fields);
    }

    /**
     * Get array of properties that is enums
     * @return array
     */
    public static function getEnumCasts(): array
    {
        $casts = (new static())->getCasts();
        return collect($casts)
            ->filter(function ($val) {
                return is_a($val, BaseEnum::class, true);
            })
            ->all();
    }

    /**
     * convert the enum value to keys
     */
    public function toArrayEnumKey()
    {
        $enumCasts = (new static())->getEnumCasts();

        return collect($this->toArray())
            ->filter(fn($q) => !empty($q))
            ->map(function ($val, $property) use ($enumCasts) {
                if (!array_key_exists($property, $enumCasts)) {
                    return $val;
                }

                $enumClass = $enumCasts[$property];

                $cast = $enumClass::valueType();

                $val = match ($cast) {
                    'string' => (string)$val,
                    default => (int)$val
                };

                return $enumClass::fromValue($val)->key;
            })
            ->all();
    }
}
