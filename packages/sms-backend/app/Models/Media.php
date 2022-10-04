<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;

/**
 * @mixin IdeHelperMedia
 */
class Media extends BaseMedia
{
    use HasFactory;
    protected $table = 'media';

    public function toRecord()
    {

    }

    /**
     * @param string $conversionName
     * @return string
     */
    public function getUrl(string $conversionName = ''): string
    {
        $conversionName = $this->hasGeneratedConversion($conversionName) ? $conversionName : '';

        $urlGenerator = UrlGeneratorFactory::createForMedia($this, $conversionName);

        if ($this->disk === 's3-private') {
            // For images in private bucket, we need to use temporary url.
            return Cache::remember($this->uuid, config('core.media.temporary_url_seconds'), function () use ($conversionName) {
                return $this->getTemporaryUrl(Carbon::now()->addSeconds(config('core.media.temporary_url_seconds')),
                    $conversionName);
            });
        }

        return $urlGenerator->getUrl();
    }
}
