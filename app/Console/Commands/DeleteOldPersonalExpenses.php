<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Expense;
use Carbon\Carbon;

class DeleteOldPersonalExpenses extends Command
{
    protected $signature = 'expenses:delete-old-personal';
    protected $description = 'Delete personal expenses older than 1 year';

    public function handle()
    {
        $cutoffDate = Carbon::now()->subYear(); // 1 saal purana
        
        $deleted = Expense::whereNull('group_id')
            ->where('expense_date', '<', $cutoffDate)
            ->delete();

        $this->info("Deleted {$deleted} old personal expenses.");
    }
}
