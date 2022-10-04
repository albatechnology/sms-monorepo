<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Str;

abstract class BaseEnum extends Enum
{
    public static string $enum_description;
    public string $label;

    public function __construct($enumValue)
    {
        parent::__construct($enumValue);
        $this->label = Str::of($this->description)->replace('_', '')->lower()->ucfirst();
    }

    public function isReadOnly(): bool
    {
        return $this->in(static::readOnly());
    }

    public static function getDefaultValue(): string
    {
        return static::getDefaultInstance()->value;
    }

    public static function getDefaultInstance()
    {
        $array = static::getInstances();
        return $array[array_key_first($array)];
    }

    public static function getContract()
    {
        return [
            'code'        => (string)Str::of(get_called_class())->afterLast('\\'),
            'description' => static::$enum_description ?? 'Missing enum description',
            'enums'       => collect(static::getInstances())->map(function (self $enum) {
                return [
                    'value'     => $enum->key,
                    'label'     => ucfirst(strtolower($enum->description)),
                    'read_only' => $enum->isReadOnly()
                ];
            })->values()->toArray(),
        ];
    }

    public static function readOnly(): array
    {
        return [];
    }

    // all instances minus read only
    public static function getSelectableInstances(): array
    {
        $array = self::getInstances();

        foreach (self::readOnly() as $remove) {
            unset($array[$remove->key]);
        }

        return $array;
    }

    public static function valueType()
    {
        return 'int';
    }

    /**
     * Transform the enum instance when it's converted to an array
     *
     * @return string
     */
    public function toArray()
    {
        $acceptedValues = ['value', 'key', 'description'];

        $value = in_array(config('core.invoker.enum_value'), $acceptedValues) ? config('core.invoker.enum_value') : 'value';
        return (string)$this->$value;
    }

    public static function getValuesString()
    {
        return collect(static::getValues())->implode(', ');
    }

    public static function getKeysString()
    {
        return collect(static::getKeys())->implode(', ');
    }
}
