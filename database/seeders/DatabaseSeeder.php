<?php

namespace Database\Seeders;

use App\Models\BusinessHour;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin-User ───────────────────────────────────────────────
        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@salon.de',
            'password'  => Hash::make('admin123'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // ─── Services ─────────────────────────────────────────────────
        $services = [
            ['name' => 'Haarschnitt',           'duration' => 30, 'price' => 25.00],
            ['name' => 'Bartpflege',             'duration' => 20, 'price' => 15.00],
            ['name' => 'Haarschnitt + Bart',     'duration' => 45, 'price' => 35.00],
            ['name' => 'Waschen & Föhnen',       'duration' => 30, 'price' => 20.00],
            ['name' => 'Färben',                 'duration' => 60, 'price' => 50.00],
        ];

        foreach ($services as $service) {
            Service::create([...$service, 'is_active' => true]);
        }

        // ─── Öffnungszeiten (ISO-Wochentage: 1=Mo … 7=So) ────────────
        $hours = [
            ['weekday' => 1, 'open_time' => '09:00', 'close_time' => '18:00', 'is_closed' => false], // Montag
            ['weekday' => 2, 'open_time' => '09:00', 'close_time' => '18:00', 'is_closed' => false], // Dienstag
            ['weekday' => 3, 'open_time' => '09:00', 'close_time' => '18:00', 'is_closed' => false], // Mittwoch
            ['weekday' => 4, 'open_time' => '09:00', 'close_time' => '18:00', 'is_closed' => false], // Donnerstag
            ['weekday' => 5, 'open_time' => '09:00', 'close_time' => '18:00', 'is_closed' => false], // Freitag
            ['weekday' => 6, 'open_time' => '09:00', 'close_time' => '15:00', 'is_closed' => false], // Samstag
            ['weekday' => 7, 'open_time' => null,    'close_time' => null,    'is_closed' => true],  // Sonntag
        ];

        foreach ($hours as $hour) {
            BusinessHour::create($hour);
        }
    }
}
