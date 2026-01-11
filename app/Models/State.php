<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function warehouses()
    {
        return $this->hasMany(FulfillmentCenter::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get geographies for this state.
     */
    public function geographies()
    {
        return $this->hasMany(Geography::class, 'state_id');
    }
}
