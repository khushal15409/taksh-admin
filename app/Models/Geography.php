<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Geography extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'level',
        'name',
        'code',
        'pincode_id',
        'area_id',
        'zone_id',
        'state_id',
        'path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Geographic levels hierarchy.
     */
    const LEVELS = [
        'india' => 1,
        'state' => 2,
        'zone' => 3,
        'area' => 4,
        'pincode' => 5,
    ];

    /**
     * Get the parent geography.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Geography::class, 'parent_id');
    }

    /**
     * Get child geographies.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Geography::class, 'parent_id')->orderBy('name');
    }

    /**
     * Get all descendants recursively.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (up to root).
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }
        
        return $ancestors;
    }

    /**
     * Get the pincode reference (if level is pincode).
     */
    public function pincode(): BelongsTo
    {
        return $this->belongsTo(Pincode::class, 'pincode_id');
    }

    /**
     * Get the area reference (if level is area).
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Get the zone reference (if level is zone).
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    /**
     * Get the state reference (if level is state).
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Get the user assignments for this geography.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(UserAssignment::class);
    }

    /**
     * Check if this geography is a descendant of another.
     */
    public function isDescendantOf(Geography $geography): bool
    {
        if (!$this->path || !$geography->path) {
            return false;
        }

        // Check if this path starts with the parent path followed by /
        return str_starts_with($this->path, $geography->path . '/') || $this->path === $geography->path;
    }

    /**
     * Check if this geography is an ancestor of another.
     */
    public function isAncestorOf(Geography $geography): bool
    {
        return $geography->isDescendantOf($this);
    }

    /**
     * Scope to filter by level.
     */
    public function scopeLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to get only active geographies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Build path from hierarchy.
     */
    public static function buildPath(Geography $geography): string
    {
        $parts = [];
        $current = $geography;
        
        while ($current) {
            array_unshift($parts, $current->code ?? $current->name);
            $current = $current->parent;
        }
        
        return implode('/', $parts);
    }
}
