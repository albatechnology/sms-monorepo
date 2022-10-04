<?php

namespace App\Http\Resources\V1\Notification;

use App\Classes\DocGenerator\BaseResource;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\ResourceData;
use App\Enums\NotificationType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class NotificationResource extends BaseResource
{
    public static function data(): array
    {
        return [
            ResourceData::make("id", Schema::TYPE_STRING, ApiDataExample::UUID),
            ResourceData::makeEnum('type', NotificationType::class),
            ResourceData::make('title', Schema::TYPE_STRING, 'title', value: fn($q) => $q->data['title'] ?? 'New Notification'),
            ResourceData::make('body', Schema::TYPE_STRING, 'body', value: fn($q) => $q->data['body'] ?? ''),
            ResourceData::make('link', Schema::TYPE_STRING, '/link', value: fn($q) => $q->data['link'] ?? null),
        ];
    }
}