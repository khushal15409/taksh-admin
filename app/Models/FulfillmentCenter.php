<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FulfillmentCenter extends Model
{
    use HasFactory;

    protected $table = 'fulfillment_centers';

    protected $fillable = [
        'state_id',
        'city_id',
        'area_id',
        'name',
        'latitude',
        'longitude',
        'supports_30_min_delivery',
        'supports_express_30',
        'express_radius_km',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'supports_30_min_delivery' => 'boolean',
            'supports_express_30' => 'boolean',
        ];
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function warehouseProducts()
    {
        return $this->hasMany(WarehouseProduct::class, 'warehouse_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'warehouse_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope to get nearest fulfillment center
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $latitude
     * @param float $longitude
     * @param float $radius Radius in kilometers (default: 15)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNearest($query, $latitude, $longitude, $radius = 15)
    {
        return $query->select('fulfillment_centers.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance', 'asc');
    }
}

