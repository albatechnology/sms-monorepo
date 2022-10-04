<?php

namespace App\Enums;

/**
 * @method static static ALL_COMPANIES_COLLECTION()
 * @method static static ALL_CHANNELS_COLLECTION()
 * @method static static ALL_SUPERVISOR_TYPES_COLLECTION()
 */
final class CacheKey extends BaseEnum
{
    const ALL_COMPANIES_COLLECTION = "all_companies_collection";
    const ALL_CHANNELS_COLLECTION = "all_channels_collection";
    const ALL_SUPERVISOR_TYPES_COLLECTION = "all_supervisor_types_collection";
}