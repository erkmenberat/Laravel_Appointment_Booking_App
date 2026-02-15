<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legt die Tabelle für angebotene Leistungen an.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            // Stammdaten einer Leistung
            $table->id();
            $table->string('name', 150);
            $table->unsignedInteger('duration')->comment('Dauer in Minuten');
            $table->decimal('price', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Schneller Filter auf aktive Leistungen
            $table->index('is_active', 'idx_active');
        });
    }

    /**
     * Entfernt die Tabelle wieder.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
