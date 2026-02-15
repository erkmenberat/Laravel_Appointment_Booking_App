<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Benutzer-Modell für Login, Rollen und Mitarbeiterbezüge.
 */
class User extends Authenticatable
{
    use Notifiable;

    // Zugehörige Datenbanktabelle
    protected $table = 'users';

    // Per Mass Assignment erlaubte Felder
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    // Diese Felder werden in Arrays/JSON nicht ausgegeben
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Typumwandlungen für Eloquent
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Ein Mitarbeiter kann mehrere Termine haben
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'staff_id');
    }

    // Ein Mitarbeiter kann mehrere Abwesenheiten haben
    public function timeOff()
    {
        return $this->hasMany(TimeOff::class, 'staff_id');
    }
}
