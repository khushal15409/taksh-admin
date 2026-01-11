<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'admin';

    protected $fillable = [
        'f_name',
        'l_name',
        'phone',
        'email',
        'image',
        'password',
        'remember_token',
        'role_id',
        'zone_id',
        'is_logged_in',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_logged_in' => 'boolean',
    ];

    /**
     * Get the role that owns the admin.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    /**
     * Get the zone that owns the admin.
     */
    public function zone(): BelongsTo
    {
        if (class_exists(Zone::class) && \Illuminate\Support\Facades\Schema::hasTable('zones')) {
            return $this->belongsTo(Zone::class, 'zone_id');
        }
        // Return a dummy relationship if Zone doesn't exist
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'zone_id');
    }

    /**
     * Scope to filter by zone if user has zone_id
     */
    public function scopeZone($query)
    {
        if (auth('admin')->check() && auth('admin')->user()->zone_id) {
            return $query->where('zone_id', auth('admin')->user()->zone_id);
        }
        return $query;
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return trim($this->f_name . ' ' . $this->l_name);
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
}
