<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Announcement;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Announcement::whereNotNull('expired_at')
                ->where('expired_at', '<=', now())
                ->delete();
        })->hourly()
          ->name('delete-expired-announcements')
          ->withoutOverlapping(); // prevents multiple overlapping runs
    }
}
