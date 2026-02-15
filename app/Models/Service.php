<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modell für angebotene Leistungen/Services.
 */
class Service extends Model
{
    // Zugehörige Datenbanktabelle
    protected $table = 'services';

    // Per Mass Assignment erlaubte Felder
    protected $fillable = [
        'name',
        'duration',
        'price',
        'is_active',
    ];

    // Typumwandlungen für Eloquent
    protected $casts = [
        'duration' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Eine Leistung kann in mehreren Terminen verwendet werden
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
