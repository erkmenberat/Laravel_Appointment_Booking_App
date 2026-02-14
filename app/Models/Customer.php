<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'note',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
