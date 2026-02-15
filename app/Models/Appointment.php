<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modell für gebuchte Termine.
 */
class Appointment extends Model
{
    // Zugehörige Datenbanktabelle
    protected $table = 'appointments';

    // Per Mass Assignment erlaubte Felder
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

    // Typumwandlungen für Eloquent
    protected $casts = [
        'customer_id' => 'integer',
        'service_id' => 'integer',
        'staff_id' => 'integer',
        'date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    // Termin gehört zu genau einem Kunden
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Termin gehört zu genau einer Leistung
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Termin kann einem Mitarbeiter zugeordnet sein
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // Zu einem Termin können mehrere Benachrichtigungsprotokolle existieren
    public function notifications()
    {
        return $this->hasMany(NotificationLog::class, 'appointment_id');
    }
}
