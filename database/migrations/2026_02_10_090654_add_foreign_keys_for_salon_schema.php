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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('customer_id', 'fk_appointments_customer')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade');

            $table->foreign('service_id', 'fk_appointments_service')
                ->references('id')
                ->on('services')
                ->onUpdate('cascade');

            $table->foreign('staff_id', 'fk_appointments_staff')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->onUpdate('cascade');
        });

        Schema::table('time_off', function (Blueprint $table) {
            $table->foreign('staff_id', 'fk_time_off_staff')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->onUpdate('cascade');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('appointment_id', 'fk_notifications_appointment')
                ->references('id')
                ->on('appointments')
                ->cascadeOnDelete()
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign('fk_notifications_appointment');
        });

        Schema::table('time_off', function (Blueprint $table) {
            $table->dropForeign('fk_time_off_staff');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign('fk_appointments_customer');
            $table->dropForeign('fk_appointments_service');
            $table->dropForeign('fk_appointments_staff');
        });
    }
};
