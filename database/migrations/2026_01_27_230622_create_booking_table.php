<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->integer('booking_id')->primary();
            $table->integer('patient_id')->index('patient_id');
            $table->integer('handling_id')->index('handling_id');
            $table->date('datum');
            $table->time('startsat');
            $table->time('endsat');
            $table->text('usernote')->nullable();
            $table->text('staffnote')->nullable();
            $table->enum('state', ['pending', 'confirmed', 'cancelled'])->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent(); // last version 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
