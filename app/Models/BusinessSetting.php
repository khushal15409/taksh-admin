<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'key',
        'value'
    ];

    protected $table = 'business_settings';
}

