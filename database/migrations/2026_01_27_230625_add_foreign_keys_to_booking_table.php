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
        Schema::table('booking', function (Blueprint $table) {
            $table->foreign(['user_id'], 'booking_ibfk_1')->references(['user_id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['handling_id'], 'booking_ibfk_2')->references(['handling_id'])->on('handling')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropForeign('booking_ibfk_1');
            $table->dropForeign('booking_ibfk_2');
        });
    }
};
