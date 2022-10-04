<?php

namespace App\Enums;

use App\Models\Channel;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static static COMPANY()
 * @method static static CHANNEL()
 * @method static static USER()
 */
final class ReportableType extends BaseEnum
{
    public const COMPANY = 'company';
    public const CHANNEL = 'channel';
    public const USER    = 'user';

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }

    public static function fromModel(Model $model)
    {
        if ($model instanceof Company) {
            return self::COMPANY();
        }

        if ($model instanceof Channel) {
            return self::CHANNEL();
        }

        if ($model instanceof User) {
            return self::USER();
        }
    }

    public static function getMorphMap(): array
    {
        return [
            'company' => Company::class,
            'channel' => Channel::class,
            'user'    => User::class,
        ];
    }
}