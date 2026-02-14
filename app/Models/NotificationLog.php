<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'appointment_id',
        'channel',
        'type',
        'recipient',
        'sent_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'appointment_id' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
