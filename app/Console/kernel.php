<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
 use Illuminate\Support\Facades\Log;
 
class Kernel extends ConsoleKernel
{

    protected $commands = [
        Commands\TestLog::class,
        Commands\DeleteOldGroups::class,
        Commands\DeleteOldPersonalExpenses::class,
        // Commands\EmailMonthlySummary::class,
    ];
    protected function schedule(Schedule $schedule): void
    {
       \Log::info('🔥 schedule() method is being called');
       
        $schedule->command('email:monthly-summary')
                 ->monthlyOn(1, '08:00');

        $schedule->command('groups:delete-old')->everyMinute()->withoutOverlapping()->evenInMaintenanceMode();       

    $schedule->command('expenses:delete-old-personal')
        ->everyMinute();
        
  $schedule->command('budget:reset')->monthlyOn(1, '00:00');
  $schedule->call(function () {
        \Log::info('Scheduler is running at ' . now());
    })->everyMinute();
    }


  
    protected function commands(): void
{
    $this->load(__DIR__.'/Commands');
    require base_path('routes/console.php');
}

}
