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
        Schema::create('time_off', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->index('date', 'idx_date');
            $table->index(['staff_id', 'date'], 'idx_staff_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_off');
    }
};
