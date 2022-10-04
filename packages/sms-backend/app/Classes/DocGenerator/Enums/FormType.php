<?php

namespace App\Classes\DocGenerator\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static TEXT()
 * @method static static PASSWORD()
 * @method static static TEXTAREA()
 * @method static static RADIO()
 * @method static static CHECKBOX()
 * @method static static SELECT()
 */
final class FormType extends Enum
{
    const TEXT =   "text";
    const PASSWORD =   "password";
    const TEXTAREA =   "textarea";
    const RADIO =   "radio";
    const CHECKBOX =   "checkbox";
    const SELECT =   "select";

    public static function getDescription($value): string
    {
        return match ($value) {
            self::TEXT => 'Input field with type text',
            self::PASSWORD => 'Password text field that should be censored',
            self::TEXTAREA => 'Textarea input field',
            self::RADIO => 'Input type radio. User can only select from a predefined set of options.',
            self::CHECKBOX => 'Input type checkbox. User can select multiple values from a predefined set of options. Value should be submitted as comma separated values.',
            self::SELECT => 'Dropdown input type. User can only select from a predefined set of options.',
            default => self::getKey($value),
        };
    }
}
