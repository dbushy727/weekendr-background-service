<?php

namespace Weekendr\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        $schedule->command('custom:get-flight-deals')
            ->everyTenMinutes()
            ->appendOutputTo(storage_path('logs/get-flight-deals.log'));

        $schedule->command('custom:notify-users --frequency=everyThirtyMinutes')
            ->everyThirtyMinutes()
            ->appendOutputTo(storage_path('logs/notify-users.log'));


        // Schedule API only gives twice daily, so splitting up into two calls on dictated hours for 4 times daily
        $schedule->command('custom:notify-users --frequency=fourTimesDaily')
            ->twiceDaily(8, 12)
            ->appendOutputTo(storage_path('logs/notify-users.log'));

        $schedule->command('custom:notify-users --frequency=fourTimesDaily')
            ->twiceDaily(16, 20)
            ->appendOutputTo(storage_path('logs/notify-users.log'));
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
