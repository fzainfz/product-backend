<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['name', 'price', 'product_category_id', 'product_status_id'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function status()
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id');
    }

    /**
     * Register media conversions for images.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Normal size (optimized for web)
        $this->addMediaConversion('normal')
             ->width(800)      // max width
             ->height(800)     // max height
             ->format('jpg')   // convert to jpg for smaller size
             ->quality(85)     // 85% quality
             ->nonQueued();    // convert immediately

        // Thumbnail size
        $this->addMediaConversion('thumb')
             ->width(200)
             ->height(200)
             ->format('jpg')
             ->quality(80)
             ->nonQueued();
    }
    protected $appends = ['image_urls'];

    public function getImageUrlsAttribute()
    {
        return $this->getMedia('products')->map(fn($media) => $media->getUrl());
    }
}

