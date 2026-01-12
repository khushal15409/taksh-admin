<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryMan extends Model
{
    use HasFactory;

    protected $table = 'delivery_men';

    protected $fillable = [
        'user_id',
        'fulfillment_center_id',
        'name',
        'email',
        'mobile_number',
        'address',
        'pincode',
        'state_id',
        'city_id',
        'vehicle_type',
        'deliveryman_type',
        'vehicle_number',
        'driving_license_number',
        'aadhaar_number',
        'profile_photo',
        'aadhaar_front',
        'aadhaar_back',
        'driving_license_photo',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fulfillmentCenter(): BelongsTo
    {
        return $this->belongsTo(FulfillmentCenter::class, 'fulfillment_center_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
