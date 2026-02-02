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
            $table->integer('user_id')->index('user_id');
            $table->integer('handling_id')->index('handling_id');
            $table->timestamp('startsat')->nullable();
            $table->timestamp('endsat')->nullable();
            $table->text('usernote')->nullable();
            $table->text('staffnote')->nullable();
            $table->enum('state', ['pending', 'confirmed', 'cancelled']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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
