<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'fulfillment_center_id',
        'name',
        'slug',
        'description',
        'short_description',
        'status',
        'is_trending',
        'is_latest',
        'is_express_30',
    ];

    protected function casts(): array
    {
        return [
            'is_trending' => 'boolean',
            'is_latest' => 'boolean',
            'is_express_30' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function questions()
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function fulfillmentCenter()
    {
        return $this->belongsTo(FulfillmentCenter::class);
    }
}
