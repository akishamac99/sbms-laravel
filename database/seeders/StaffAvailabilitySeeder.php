<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffAvailability;
use Illuminate\Database\Seeder;

class StaffAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $plan = [
            'aurelia@salon.com' => [['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], '09:00:00', '18:00:00'],
            'pau@salon.com' => [['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'], '10:00:00', '19:00:00'],
        ];

        foreach ($plan as $email => [$days, $start, $end]) {
            $staff = Staff::where('email', $email)->first();

            if (! $staff) {
                continue;
            }

            foreach ($days as $day) {
                StaffAvailability::firstOrCreate(
                    ['staffID' => $staff->getKey(), 'day_of_week' => $day],
                    ['start_time' => $start, 'end_time' => $end, 'is_available' => 1]
                );
            }
        }
    }
}
