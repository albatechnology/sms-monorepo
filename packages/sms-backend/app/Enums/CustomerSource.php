<?php

namespace App\Enums;

/**
 * @method static static MOVES()
 * @method static static SMS()
 */
final class CustomerSource extends BaseEnum
{
    const MOVES = 'moves';
    const SMS = 'sms';
}
