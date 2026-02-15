<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legt Öffnungszeiten an und erzeugt 7 Standardzeilen (Mo-So).
     */
    public function up(): void
    {
        Schema::create('business_hours', function (Blueprint $table) {
            // Öffnungsdaten je Wochentag
            $table->id();
            $table->unsignedTinyInteger('weekday')->comment('1=Montag, 7=Sonntag');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->unique('weekday', 'unique_weekday');
        });

        // Initiale Platzhalterwerte für alle Wochentage anlegen
        $rows = [];
        for ($weekday = 1; $weekday <= 7; $weekday++) {
            $rows[] = [
                'weekday' => $weekday,
                'open_time' => null,
                'close_time' => null,
                'is_closed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('business_hours')->insert($rows);
    }

    /**
     * Entfernt die Tabelle wieder.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};
