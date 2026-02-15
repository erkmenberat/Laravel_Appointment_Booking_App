<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modell für Versandprotokolle von Benachrichtigungen.
 */
class NotificationLog extends Model
{
    // Zugehörige Datenbanktabelle
    protected $table = 'notifications';

    // Per Mass Assignment erlaubte Felder
    protected $fillable = [
        'appointment_id',
        'channel',
        'type',
        'recipient',
        'sent_at',
        'status',
        'error_message',
    ];

    // Typumwandlungen für Eloquent
    protected $casts = [
        'appointment_id' => 'integer',
        'sent_at' => 'datetime',
    ];

    // Protokolleintrag gehört zu genau einem Termin
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
