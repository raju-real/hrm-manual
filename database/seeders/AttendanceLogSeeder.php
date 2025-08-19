<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\AttendanceLog;

class AttendanceLogSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        $startDate = Carbon::now()->subMonths(5);
        $endDate   = Carbon::now();

        foreach (range(1, 5) as $userId) { // users 1 to 5
            $date = $startDate->copy();

            while ($date->lte($endDate)) {
                // Random chance user was present this day
                if (rand(0, 100) > 25) { // ~75% attendance
                    // "IN" punch
                    $inTime = $date->copy()->setTime(rand(8, 10), rand(0, 59)); // 8–10 AM
                    $lat = $faker->randomFloat(6, 23.70, 23.90);
                    $lng = $faker->randomFloat(6, 90.35, 90.45);

                    AttendanceLog::create([
                        'user_id'    => $userId,
                        'punch_time' => $inTime,
                        'direction'  => 'in',
                        'latitude'   => $lat,
                        'longitude'  => $lng,
                        'created_by' => $userId,
                        'created_at' => $inTime,
                        'updated_at' => $inTime,
                    ]);

                    // "OUT" punch ~6–9 hours later
                    $outTime = $inTime->copy()->addHours(rand(6, 9))->addMinutes(rand(0, 30));
                    $lat = $faker->randomFloat(6, 23.70, 23.90);
                    $lng = $faker->randomFloat(6, 90.35, 90.45);

                    AttendanceLog::create([
                        'user_id'    => $userId,
                        'punch_time' => $outTime,
                        'direction'  => 'out',
                        'latitude'   => $lat,
                        'longitude'  => $lng,
                        'created_by' => $userId,
                        'created_at' => $outTime,
                        'updated_at' => $outTime,
                    ]);

                    // Optional: sometimes extra out punches (like lunch break)
                    if (rand(0, 100) > 70) {
                        $extraOutTime = $inTime->copy()->addHours(rand(3, 5));
                        $lat = $faker->randomFloat(6, 23.70, 23.90);
                        $lng = $faker->randomFloat(6, 90.35, 90.45);

                        AttendanceLog::create([
                            'user_id'    => $userId,
                            'punch_time' => $extraOutTime,
                            'direction'  => 'out',
                            'latitude'   => $lat,
                            'longitude'  => $lng,
                            'created_by' => $userId,
                            'created_at' => $extraOutTime,
                            'updated_at' => $extraOutTime,
                        ]);
                    }
                }

                $date->addDay();
            }
        }
    }
}
