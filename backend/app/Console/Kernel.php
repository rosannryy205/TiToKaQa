<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('orders:auto-cancel')->everyMinute(); 
        $schedule->command('setup:flashsale')->hourly();
        $schedule->command('cleanup:flashsale')->everyMinute();
        
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
    protected $commands = [
        Commands\AutoCancelOrders::class,
       Commands\SetupFlashSale::class,
       Commands\CleanUpFlashSale::class,
    
    ];
    protected function scheduleTimezone()
{
    return 'Asia/Ho_Chi_Minh';
}
}
