<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modell für Kundendaten.
 */
class Customer extends Model
{
    // Zugehörige Datenbanktabelle
    protected $table = 'customers';

    // Per Mass Assignment erlaubte Felder
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'note',
    ];

    // Ein Kunde kann mehrere Termine haben
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
