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
        // Employee fields
        'f_name',
        'l_name',
        'phone',
        'role_id',
        'pincode_id',
        'is_logged_in',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'is_logged_in' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Get the role that owns the user (for employees).
     */
    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    /**
     * Get the pincode that owns the user (for employees).
     */
    public function pincode()
    {
        return $this->belongsTo(Pincode::class, 'pincode_id');
    }

    /**
     * Scope to filter by pincode if user has pincode_id
     */
    public function scopePincode($query)
    {
        if (auth()->check() && auth()->user()->pincode_id) {
            return $query->where('pincode_id', auth()->user()->pincode_id);
        }
        return $query;
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        if ($this->f_name || $this->l_name) {
            return trim(($this->f_name ?? '') . ' ' . ($this->l_name ?? ''));
        }
        return $this->name ?? '';
    }

    /**
     * Get image full URL
     */
    public function getImageFullUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/admin/' . $this->image);
        }
        return asset('assets/admin/img/admin.png');
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

    /**
     * Get the user assignments.
     */
    public function assignments()
    {
        return $this->hasMany(UserAssignment::class);
    }

    /**
     * Get currently effective assignments.
     */
    public function activeAssignments()
    {
        return $this->assignments()->currentlyEffective();
    }

    /**
     * Get primary assignment (most recent active assignment).
     */
    public function primaryAssignment()
    {
        return $this->hasOne(UserAssignment::class)
            ->currentlyEffective()
            ->latestOfMany();
    }

    /**
     * Get all departments the user is assigned to.
     */
    public function departments()
    {
        return $this->hasManyThrough(
            Department::class,
            UserAssignment::class,
            'user_id',
            'id',
            'id',
            'department_id'
        )->distinct();
    }

    /**
     * Get all geographies the user has access to.
     */
    public function accessibleGeographies()
    {
        return $this->hasManyThrough(
            Geography::class,
            UserAssignment::class,
            'user_id',
            'id',
            'id',
            'geography_id'
        )->distinct();
    }
}
