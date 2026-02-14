<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'customer_id',
        'service_id',
        'staff_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'customer_note',
        'staff_note',
        'cancel_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'service_id' => 'integer',
        'staff_id' => 'integer',
        'date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function notifications()
    {
        return $this->hasMany(NotificationLog::class, 'appointment_id');
    }
}
