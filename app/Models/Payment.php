<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'gateway',
        'amount',
        'status',
        'response_json',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'response_json' => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
