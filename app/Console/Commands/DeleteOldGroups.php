<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;
use Carbon\Carbon;

class DeleteOldGroups extends Command
{
    protected $signature = 'groups:delete-old';

    protected $description = 'Delete old groups - permanent after 1 year, non-permanent after 1 month';

    public function handle()
    {
        $permanentGroups = Group::where('permanent', true)
            ->where('created_at', '<=', Carbon::now()->subYear())
            ->get();

        $permanentCount = $permanentGroups->count();
        foreach ($permanentGroups as $group) {
            $group->delete();
        }

        $nonPermanentGroups = Group::where('permanent', false)
            ->where('created_at', '<=', Carbon::now()->subMonth())
            ->get();

        $nonPermanentCount = $nonPermanentGroups->count();
        foreach ($nonPermanentGroups as $group) {
            $group->delete();
        }

        $this->info("Deleted $permanentCount permanent groups older than 1 year.");
        $this->info("Deleted $nonPermanentCount non-permanent groups older than 1 month.");
    }
}
