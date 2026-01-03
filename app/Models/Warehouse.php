<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'city_id',
        'area_id',
        'name',
        'latitude',
        'longitude',
        'supports_30_min_delivery',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'supports_30_min_delivery' => 'boolean',
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
        return $this->hasMany(WarehouseProduct::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
