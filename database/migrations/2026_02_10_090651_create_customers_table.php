<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legt die Kundentabelle inklusive eindeutiger Kontaktdaten an.
     */
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            return;
        }

        Schema::create('customers', function (Blueprint $table) {
            // Stammdaten des Kunden
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 30);
            $table->string('email', 254)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            // Duplikate verhindern und Namenssuche beschleunigen
            $table->unique('phone', 'unique_phone');
            $table->unique('email', 'unique_email');
            $table->index(['last_name', 'first_name'], 'idx_name');
        });
    }

    /**
     * Entfernt die Tabelle wieder.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
