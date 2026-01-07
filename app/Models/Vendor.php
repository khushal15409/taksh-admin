<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    /**
     * Prevent Laravel from treating 'category' as a relationship
     * by explicitly excluding it from relationship detection
     */
    protected $with = [];

    protected $fillable = [
        'user_id',
        'vendor_name',
        'owner_name',
        'shop_name',
        'shop_address',
        'shop_pincode',
        'shop_latitude',
        'shop_longitude',
        'category_id',
        'shop_images',
        'owner_address',
        'owner_pincode',
        'owner_latitude',
        'owner_longitude',
        'owner_image',
        'email',
        'mobile_number',
        'gst_number',
        'pan_number',
        'address',
        'state_id',
        'city_id',
        'pincode',
        'bank_name',
        'account_number',
        'ifsc_code',
        'status',
        'assigned_salesman_id',
        'verification_status',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'shop_images' => 'array',
            'shop_latitude' => 'decimal:8',
            'shop_longitude' => 'decimal:8',
            'owner_latitude' => 'decimal:8',
            'owner_longitude' => 'decimal:8',
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function assignedSalesman()
    {
        return $this->belongsTo(User::class, 'assigned_salesman_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function verifications()
    {
        return $this->hasMany(VendorVerification::class);
    }

    /**
     * Get categories as array from comma-separated string
     */
    public function getCategoriesAttribute()
    {
        if (empty($this->category_id)) {
            return [];
        }
        return array_filter(array_map('trim', explode(',', $this->category_id)));
    }

    /**
     * Get category models from comma-separated IDs
     */
    public function getCategoryModelsAttribute()
    {
        $categoryIds = $this->categories;
        if (empty($categoryIds)) {
            return collect([]);
        }
        return Category::whereIn('id', $categoryIds)->get();
    }

    /**
     * Get first category as accessor (not a relationship)
     * Use $vendor->category or $vendor->category_models for multiple categories
     * This is an accessor, not a relationship method, to avoid Laravel's relationship detection
     */
    public function getCategoryAttribute()
    {
        // Check if already loaded to avoid infinite recursion
        if (isset($this->attributes['_category_cache'])) {
            return $this->attributes['_category_cache'];
        }

        $categoryIds = $this->categories;
        if (empty($categoryIds)) {
            $this->attributes['_category_cache'] = null;
            return null;
        }
        
        $category = Category::find($categoryIds[0]);
        $this->attributes['_category_cache'] = $category;
        return $category;
    }

    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }
}
