<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;

// class ResetPersonalBudget extends Command
// {
//     /**
//      * The name and signature of the console command.
//      *
//      * @var string
//      */
//     protected $signature = 'app:reset-personal-budget';

//     /**
//      * The console command description.
//      *
//      * @var string
//      */
//     protected $description = 'Command description';

//     /**
//      * Execute the console command.
//      */
//     public function handle()
//     {
//         //
//     }
// }
 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class ResetPersonalBudget extends Command
{
    protected $signature = 'budget:reset';
    protected $description = 'Reset personal budget every month and adjust carry forward balance';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            // Step 1: Last month ka carry forward balance le lo
            $carryForward = $user->personal_carry_forward_balance ?? 0;

            // Step 2: Current month ke liye personal budget reset
            $user->personal_budget = 0;

            // Step 3: Carry forward balance add kardo budget me
            if ($carryForward > 0) {
                $user->personal_budget += $carryForward;

                // Adjust hone ke baad carry forward zero kar dena
                $user->personal_carry_forward_balance = 0;
            }

            $user->save();
        }

        $this->info('All users personal budgets have been reset and carry forward adjusted.');
    }
}
