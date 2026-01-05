<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }
}
