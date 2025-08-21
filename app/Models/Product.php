<?php

namespace App\Models;

use App\Models\Tag;
use Spatie\Tags\HasTags;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, HasTags;

    // protected $fillable = [
    //     'name',
    //     'sku',
    //     'slug',
    //     'description',
    //     'price',
    //     'stock',
    //     'weight',
    // ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('cover')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
}
