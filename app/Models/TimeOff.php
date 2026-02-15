<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modell für Abwesenheiten eines Mitarbeiters.
 */
class TimeOff extends Model
{
    // Zugehörige Datenbanktabelle
    protected $table = 'time_off';

    // Per Mass Assignment erlaubte Felder
    protected $fillable = [
        'staff_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    // Typumwandlungen für Eloquent
    protected $casts = [
        'staff_id' => 'integer',
        'date' => 'date',
    ];

    // Abwesenheit gehört zu einem Mitarbeiter
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
