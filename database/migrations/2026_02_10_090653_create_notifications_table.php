<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legt die Tabelle für Benachrichtigungsprotokolle an.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // Versand- und Statusdaten
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->enum('channel', ['email'])->default('email');
            $table->enum('type', ['created', 'confirmed', 'cancelled', 'reminder']);
            $table->string('recipient', 255);
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Indizes für Monitoring und Auswertung
            $table->index('appointment_id', 'idx_appointment_id');
            $table->index('status', 'idx_status');
            $table->index('sent_at', 'idx_sent_at');
        });
    }

    /**
     * Entfernt die Tabelle wieder.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
