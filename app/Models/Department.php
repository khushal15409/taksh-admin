<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the units for the department.
     */
    public function units(): HasMany
    {
        return $this->hasMany(DepartmentUnit::class)->orderBy('sort_order');
    }

    /**
     * Get active units only.
     */
    public function activeUnits(): HasMany
    {
        return $this->units()->where('is_active', true);
    }

    /**
     * Get the user assignments for this department.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(UserAssignment::class);
    }

    /**
     * Get active assignments only.
     */
    public function activeAssignments(): HasMany
    {
        return $this->assignments()->where('is_active', true);
    }

    /**
     * Scope to get only active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
