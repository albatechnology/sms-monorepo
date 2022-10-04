<?php

namespace App\Enums;

/**
 * @method static static RED()
 * @method static static YELLOW()
 * @method static static GREEN()
 * @method static static EXPIRED()
 * @method static static SALES()
 * @method static static OTHER_SALES()
 */
final class LeadStatus extends BaseEnum
{
    const GREEN       = 1;
    const YELLOW      = 2;
    const RED         = 3;
    const EXPIRED     = 4;
    const SALES       = 5;
    const OTHER_SALES = 6;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }

    /**
     * The next status progression from a given status
     *
     * @return $this
     */
    public function nextStatus(): ?self
    {
        return match ($this->value) {
            self::GREEN => self::YELLOW(),
            self::YELLOW => self::RED(),
            self::RED => self::EXPIRED(),
            default => null,
        };
    }

    /**
     * Should a lead status be reset back to green
     * when a new acticity is added
     * @return bool
     */
    public function shouldReset(): bool
    {
        return $this->in([self::YELLOW, self::RED]);
    }
}
