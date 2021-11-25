<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;
use Sms\EbulkSms;
use App\GreatElites;

require(__DIR__ . '/../EbulkSms.php');

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\DatabaseBackUp'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {
            // your schedule code
            $currentDate = date("Y-m-d H:i:s", strtotime("now"));

            Log::info('Running scheduler for AUTOMATION : ' . $currentDate);

            $greatElites = new GreatElites();
            $greatElites->automaticMerge();

            Log::info('Ending scheduler for AUTOMATION: ' . $currentDate);
        })->everyMinute();

        $schedule->command('database:backup')->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
