<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // 👇 Tera command yahan scheduled hoga
        $schedule->command('email:monthly-summary')
                 ->monthlyOn(1, '08:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
