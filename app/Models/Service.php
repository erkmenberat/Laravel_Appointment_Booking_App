<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'name',
        'duration',
        'price',
        'is_active',
    ];

    protected $casts = [
        'duration' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
