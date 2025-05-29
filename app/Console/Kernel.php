<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('data:delete-client')->dailyAt('13:36');

        // Optional: Add logging for successful execution
        $schedule->command('app:delete-client')
                 ->dailyAt('13:36')
                 ->onSuccess(function () {
                     Log::info('Scheduler: Old client data deletion command ran successfully.');
                 })
                 ->onFailure(function () {
                     Log::error('Scheduler: Old client data deletion command failed.');
                 });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
