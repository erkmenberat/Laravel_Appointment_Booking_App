<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modell für Öffnungszeiten je Wochentag.
 */
class BusinessHour extends Model
{
    // Zugehörige Datenbanktabelle
    protected $table = 'business_hours';

    // Per Mass Assignment erlaubte Felder
    protected $fillable = [
        'weekday',
        'open_time',
        'close_time',
        'is_closed',
    ];

    // Typumwandlungen für Eloquent
    protected $casts = [
        'weekday' => 'integer',
        'is_closed' => 'boolean',
    ];
}
