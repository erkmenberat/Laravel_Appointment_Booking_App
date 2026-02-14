<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $table = 'business_hours';

    protected $fillable = [
        'weekday',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'is_closed' => 'boolean',
    ];
}
