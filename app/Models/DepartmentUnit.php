<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
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
     * Get the department that owns the unit.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user assignments for this unit.
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
     * Scope to get only active units.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
