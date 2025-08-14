<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    \Log::info("TEST LOG from TestLog command");
    $this->info("TestLog command executed");
}
}
