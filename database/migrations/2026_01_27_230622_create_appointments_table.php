<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legt die Tabelle für Termine inkl. wichtiger Indizes an.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            // Primärdaten des Termins
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['requested', 'confirmed', 'cancelled', 'completed'])->default('requested');
            $table->text('customer_note')->nullable();
            $table->text('staff_note')->nullable();
            $table->string('cancel_reason', 255)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Indizes für typische Filter-/Suchabfragen
            $table->index('date', 'idx_date');
            $table->index('staff_id', 'idx_staff_id');
            $table->index('status', 'idx_status');
            $table->index(['date', 'start_time', 'staff_id'], 'idx_date_time_staff');
            $table->index('customer_id', 'idx_customer_id');
            $table->index('service_id', 'idx_service_id');
        });
    }

    /**
     * Entfernt die Tabelle wieder.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
