<?php

namespace App\Pipes\Reportable;

use App\Enums\TargetType;
use App\Interfaces\Reportable;
use App\Models\Activity;
use App\Models\Channel;
use App\Services\CacheService;

/**
 *
 * Class ActivityCount
 * @package App\Pipes\Reportable
 */
class ActivityCount extends BaseReportablePipe
{
    final protected function getTargetType(): TargetType
    {
        return TargetType::ACTIVITY_COUNT();
    }

    final protected function getReportableClassName(): string
    {
        return Activity::class;
    }

    protected function getReportableValue(Reportable $model = null): int
    {
        return 1;
    }

    protected function getReportableValueProperty(): ?string
    {
        return null;
    }

    protected function whereReportableBaseQuery($query)
    {
        return $query->where('created_at', '>', $this->report->start_date)
            ->where('created_at', '<', $this->report->end_date);
    }

    protected function whereReportableCompany($query, int $id)
    {
        $channelIds = app(CacheService::class)->channels()
            ->filter(function (Channel $channel) use ($id) {
                return $channel->company_id === $id;
            })
            ->pluck('id');

        return $query->whereIn('channel_id', $channelIds->all());
    }

    protected function whereReportableChannel($query, int $id)
    {
        return $query->where('channel_id', $id);
    }

    protected function whereReportableUsers($query, array $ids)
    {
        return $query->whereIn('user_id', $ids);
    }
}
