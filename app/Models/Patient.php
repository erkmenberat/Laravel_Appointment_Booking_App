<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    public $incrementing = true;

    protected $fillable = [
        'firstname',
        'lastname',
        'address',
        'phone_number',
        'email',
        'note'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'patient_id', 'patient_id');
    }
}
