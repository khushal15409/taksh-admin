<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class UserAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'department_unit_id',
        'geography_id',
        'role_id',
        'effective_from',
        'effective_to',
        'notes',
        'is_active',
        'assigned_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user for this assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department for this assignment.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the department unit for this assignment.
     */
    public function departmentUnit(): BelongsTo
    {
        return $this->belongsTo(DepartmentUnit::class);
    }

    /**
     * Get the geography for this assignment.
     */
    public function geography(): BelongsTo
    {
        return $this->belongsTo(Geography::class);
    }

    /**
     * Get the role for this assignment (Spatie Permission).
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the user who assigned this assignment.
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if assignment is currently effective.
     */
    public function isCurrentlyEffective(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->toDateString();

        if ($this->effective_from && $this->effective_from->gt($now)) {
            return false;
        }

        if ($this->effective_to && $this->effective_to->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Scope to get only currently effective assignments.
     */
    public function scopeCurrentlyEffective($query)
    {
        $now = now()->toDateString();
        
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('effective_from')
                  ->orWhere('effective_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $now);
            });
    }

    /**
     * Scope to get only active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
