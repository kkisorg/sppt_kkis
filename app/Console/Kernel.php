<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AnnouncementOnlineMediaPublishScheduleController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\MonthlyOfflineDistributionScheduleController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        // Send email
        $schedule
            ->call(new EmailController)
            ->everyMinute();

        // Add monthly offline distribution
        $schedule
            ->call(new MonthlyOfflineDistributionScheduleController)
            ->weeklyOn(1, '3:00');

        // Publish announcement to online media
        $schedule
            ->call(new AnnouncementOnlineMediaPublishScheduleController)
            ->everyMinute();

        // Check announcement approval center status
        $schedule
            ->call(new AnnouncementController)
            ->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
