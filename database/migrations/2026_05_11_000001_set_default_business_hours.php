<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Existing databases may still contain the closed placeholder rows from the
     * original business-hours migration. Set the salon's default opening hours.
     */
    public function up(): void
    {
        $hours = [
            ['weekday' => 1, 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['weekday' => 2, 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['weekday' => 3, 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['weekday' => 4, 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['weekday' => 5, 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['weekday' => 6, 'open_time' => '09:00:00', 'close_time' => '15:00:00', 'is_closed' => false],
            ['weekday' => 7, 'open_time' => null, 'close_time' => null, 'is_closed' => true],
        ];

        foreach ($hours as $hour) {
            DB::table('business_hours')->updateOrInsert(
                ['weekday' => $hour['weekday']],
                [
                    'open_time' => $hour['open_time'],
                    'close_time' => $hour['close_time'],
                    'is_closed' => $hour['is_closed'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('business_hours')->update([
            'open_time' => null,
            'close_time' => null,
            'is_closed' => true,
            'updated_at' => now(),
        ]);
    }
};
