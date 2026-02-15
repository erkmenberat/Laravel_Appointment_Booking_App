<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

/**
 * Erstbefüllung der Datenbank für Entwicklungs-/Testzwecke.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Führt alle konfigurierten Seeder aus.
     */
    public function run(): void
    {
        // Beispielbenutzer für den schnellen Einstieg
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }
}
