<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeOff extends Model
{
    protected $table = 'time_off';

    protected $fillable = [
        'staff_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'staff_id' => 'integer',
        'date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
