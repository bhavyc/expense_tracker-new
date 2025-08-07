<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyExpenseSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $totalExpense;
    public $month;

    public function __construct($user, $totalExpense, $month)
    {
        $this->user = $user;
        $this->totalExpense = $totalExpense;
        $this->month = $month;
    }

    public function build()
    {
        return $this->subject("Your Monthly Expense Summary - {$this->month}")
                    ->view('emails.monthly_summary');
    }
}
