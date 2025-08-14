<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GroupMember;
use App\Models\Group;

class AnalyticsController extends Controller
{

// public function analyticsForm()
// {
//     $users = User::all();
//     return view('admin.analytics.user', compact('users'));
// }

// public function analyticsResult(Request $request)
// {
//     $userId = $request->user_id;

//     $analyticsData = Expense::where('user_id', $userId)
//         ->selectRaw('category, SUM(amount) as total')
//         ->groupBy('category')
//         ->get();

//     $users = User::all();
//     return view('admin.analytics.user', compact('users', 'analyticsData', 'userId'));
// }
public function index(Request $request)
{
    $users = User::all();
    $userId = $request->user_id;
    $selectedUser = null;
    $categoryNames = $categoryAmounts = [];
    $totalSpent = 0;
    $topCategory = null;

    if ($userId) {
        $selectedUser = User::find($userId);

        $expenses = Expense::where('user_id', $userId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $categoryNames = $expenses->pluck('category');
        $categoryAmounts = $expenses->pluck('total');
        $totalSpent = $expenses->sum('total');

        $topCategory = $expenses->sortByDesc('total')->first()?->category;
    }

    return view('admin.analytics.index', compact(
        'users', 'selectedUser', 'categoryNames', 'categoryAmounts', 'totalSpent', 'topCategory'
    ));
}

public function userAnalytics($userId)
{
    $user = User::findOrFail($userId);

     
    $totalSpent = Expense::where('user_id', $userId)->sum('amount');

    
    $categoryWise = Expense::where('user_id', $userId)
        ->select('category_id', \DB::raw('SUM(amount) as total_spent'))
        ->groupBy('category_id')
        ->with('category')
        ->get();

     
    $monthlyTrend = Expense::where('user_id', $userId)
        ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    return view('admin.analytics.index', compact('user', 'totalSpent', 'categoryWise', 'monthlyTrend'));
}

}
 

