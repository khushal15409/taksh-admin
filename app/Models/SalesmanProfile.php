<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesmanProfile extends Model
{
    use HasFactory;

    protected $table = 'salesmen_profiles';

    protected $fillable = [
        'user_id',
        'name',
        'mobile_number',
        'email',
        'state_id',
        'city_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

    public function location()
    {
        return $this->hasOne(SalesmanLocation::class, 'salesman_id', 'user_id');
    }
}
