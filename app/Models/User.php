<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'mobile',
        'name',
        'email',
        'password',
        'image',
        'is_verified',
        'status',
        'user_type',
        'vendor_status',
        'is_active',
        'address',
        'pincode',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function assignedVendors()
    {
        return $this->hasMany(Vendor::class, 'assigned_salesman_id');
    }

    public function location()
    {
        return $this->hasOne(SalesmanLocation::class, 'salesman_id');
    }

    public function salesmanProfile()
    {
        return $this->hasOne(SalesmanProfile::class);
    }

    public function deliveryMan()
    {
        return $this->hasOne(DeliveryMan::class);
    }
}
