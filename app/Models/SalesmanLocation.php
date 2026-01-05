<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesmanLocation extends Model
{
    use HasFactory;

    protected $table = 'salesmen_locations';
    
    public $timestamps = false;
    
    protected $fillable = [
        'salesman_id',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'updated_at' => 'datetime',
        ];
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }
}
