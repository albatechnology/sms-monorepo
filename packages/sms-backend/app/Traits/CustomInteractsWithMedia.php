<?php


namespace App\Traits;

use App\Models\Media;
use App\Services\HelperService;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

trait CustomInteractsWithMedia
{
    use InteractsWithMedia;

    public function getImageAttribute()
    {
        $files = $this->getMedia('image');
        $files->each(function ($item) {
            $item->url       = $item->getUrl() ?? null;
            $item->thumbnail = $item->getUrl('thumb') ?? null;
            $item->preview   = $item->getUrl('preview') ?? null;
        });

        return $files;
    }

    public function getImagesAttribute()
    {
        if (method_exists(static::class, 'getPhotoAttribute')) {
            return $this->photo;
        } elseif (method_exists(static::class, 'getImageAttribute')) {
            return $this->image;
        }

        return null;
    }

    public function getRecordImages()
    {
        return collect($this->images)->map(function ($media) {
            return [
                'id'              => $media->id,
                'url'             => $media->url,
                'thumbnail'       => $media->thumbnail,
                'preview'         => $media->preview,
                'mime_type'       => $media->mime_type,
                'collection_name' => $media->collection_name,
                'name'            => $media->name,
            ];
        })->all();
    }

    public function getApiImagesAttribute()
    {
        if (empty($this->images) || $this->images->isEmpty()) {
            $files = collect([
                Media::factory()->model($this)->make(['id' => 1]),
                Media::factory()->model($this)->make(['id' => 2]),
            ]);

            $files->each(function ($item) {
                $item->url       = HelperService::getDummyImageUrl($this);
                $item->thumbnail = HelperService::getDummyImageUrl($this);
                $item->preview   = HelperService::getDummyImageUrl($this);
            });

            return $files;
        }

        return $this->images;
    }

    public function registerMediaConversions(SpatieMedia $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }
}
