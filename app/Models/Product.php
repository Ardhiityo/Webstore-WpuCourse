<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasTags, InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('cover')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
}
