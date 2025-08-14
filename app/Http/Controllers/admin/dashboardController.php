<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Expense;
use App\Models\Split;
use App\Models\GroupMember;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function index()
{
    $totalUsers = User::count();
    $totalGroups = Group::count();
   
    $totalExpenses = Expense::count();
    $totalSplits = Split::count();

    // Line Chart: Monthly Expense Trend banane ke liye 
    $monthlyExpenses = Expense::select(
        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
        DB::raw("SUM(amount) as total")
    )
    ->groupBy('month')
    ->orderBy('month')
    ->get();

    $expenseTrendLabels = $monthlyExpenses->pluck('month');
    $expenseTrendData = $monthlyExpenses->pluck('total');

     
    $categorySummary = Expense::select('category', DB::raw('SUM(amount) as total'))
    ->groupBy('category')
    ->get();

$categoryLabels = $categorySummary->pluck('category');
$categoryData = $categorySummary->pluck('total');

    

    return view('admin.dashboard', compact(
        'totalUsers',
        'totalGroups',
        'totalExpenses',
        'totalSplits',
        'expenseTrendLabels',
        'expenseTrendData',
        'categoryLabels',
        'categoryData'
    ));
}
}
