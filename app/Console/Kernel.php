<?php

namespace App\Console;

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
        'App\Console\Commands\GenerateEmojis',
        'App\Console\Commands\GenerateInstancesCss',
        'App\Console\Commands\SendReminders',
        'App\Console\Commands\SendNotifMails',
        'App\Console\Commands\ResetJoinNotifs',
        'App\Console\Commands\ReindexCommand',
        'App\Console\Commands\WorkflowReminder',

        /* Generate bills */
        'App\Console\Commands\GenerateBillsCommand',

        'App\Console\Commands\DocumentThumbnailGenerator',
        // enable onlyoffice for an instance
        'App\Console\Commands\EnableOnlyoffice',

        // make plan forever on an instance
        'App\Console\Commands\MakePlanForever',
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

        // $schedule->command('generate:bills')->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
