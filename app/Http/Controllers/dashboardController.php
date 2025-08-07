<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\GroupMember;
use App\Models\Split;
 use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Group;   

class dashboardController extends Controller
{
  
    public function index()
    {
        $user = Auth::user();

       
        $totalUsers = 1; // Since this dashboard is per user
        $totalExpenses = Expense::where('user_id', $user->id)->sum('amount');
        $totalGroups = Group::where('created_by', $user->id)->count();

        $totalSplits = Split::where('user_id', $user->id)->count();

      
        $monthlyExpenses = Expense::where('user_id', $user->id)
            ->where('expense_date', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(expense_date, "%b %Y") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderByRaw('MIN(expense_date)')
            ->pluck('total', 'month')
            ->toArray();
 $expenseTrendLabels = array_keys($monthlyExpenses);
$expenseTrendData = array_values($monthlyExpenses);

        $categoryData = Expense::where('user_id', $user->id)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();
   $categoryLabels = array_keys($categoryData);
    $categoryTotals = array_values($categoryData);
        return view('dashboard', compact(
            'totalUsers',
            'totalExpenses',
            'totalGroups',
            'totalSplits',
            'monthlyExpenses',
            'categoryData',
             'expenseTrendLabels',
        'expenseTrendData',
         'categoryLabels',
        'categoryTotals'
        ));
    }
}
