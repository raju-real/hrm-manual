<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\AttendanceLog;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class AttendanceLogSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $startDate = Carbon::now()->subMonths(5)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        $userIds = range(2, 10); // users 2 to 10

        foreach ($userIds as $userId) {
            $date = $startDate->copy();

            while ($date->lte($endDate)) {
                // track used check_in times per user/day to avoid duplicates
                $usedTimes = [];

                // minimum 5 check-ins/check-outs per user per day
                $dailyCheckCount = rand(5, 7);

                for ($i = 0; $i < $dailyCheckCount; $i++) {
                    // random check-in hour between 8 AM and 4 PM
                    $inHour = rand(8, 16);
                    $inMinute = rand(0, 59);
                    $inTime = $date->copy()->setTime($inHour, $inMinute);

                    // avoid duplicate check_in for same user/day
                    while (in_array($inTime->format('H:i'), $usedTimes)) {
                        $inMinute = rand(0, 59);
                        $inTime = $date->copy()->setTime($inHour, $inMinute);
                    }
                    $usedTimes[] = $inTime->format('H:i');

                    // check-out 2–3 hours later + random minutes
                    $outTime = $inTime->copy()->addHours(rand(2, 3))->addMinutes(rand(0, 30));

                    // calculate total_minutes as integer
                    $totalMinutes = $inTime->diffInMinutes($outTime);

                    // random lat/lng near Dhaka
                    $lat = $faker->randomFloat(6, 23.70, 23.90);
                    $lng = $faker->randomFloat(6, 90.35, 90.45);

                    // create attendance log
                    AttendanceLog::create([
                        'user_id'    => $userId,
                        'check_in'   => $inTime,
                        'check_out'  => $outTime,
                        'total_minutes' => $totalMinutes,
                        'latitude'   => $lat,
                        'longitude'  => $lng,
                        'created_by' => $userId,
                        'created_at' => $inTime,
                        'updated_at' => $outTime,
                    ]);
                }

                $date->addDay();
            }
        }
    }
}
