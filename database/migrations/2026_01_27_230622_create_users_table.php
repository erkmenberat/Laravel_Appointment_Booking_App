<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legt die Benutzer-Tabelle für Admins und Mitarbeiter an.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Benutzerstammdaten
            $table->id();
            $table->string('name', 100);
            $table->string('email', 255)->unique('unique_email');
            $table->string('password', 255);
            $table->rememberToken();
            $table->enum('role', ['admin', 'staff'])->default('staff');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index für Rollen-/Statusfilter
            $table->index(['role', 'is_active'], 'idx_role_active');
        });
    }

    /**
     * Entfernt die Tabelle wieder.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
