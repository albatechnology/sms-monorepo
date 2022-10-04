<?php

namespace App\Models;

use App\Traits\CustomInteractsWithMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Export extends Model implements HasMedia
{
    use CustomInteractsWithMedia;
    protected $guarded = [];
    protected $appends = [
        'file',
    ];

    public function getFileAttribute()
    {
        $files = $this->getMedia('exports');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
