<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'salesman_id',
        'shop_photo',
        'license_photo',
        'remarks',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }
}
