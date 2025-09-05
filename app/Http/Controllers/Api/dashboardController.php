<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\GroupMember;
use App\Models\Split;
use App\Models\Category;
class DashboardController extends Controller
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

        return response()->json([
            'success' => true,
            'data' => [
                'totalUsers' => $totalUsers,
                'totalExpenses' => $totalExpenses,
                'totalGroups' => $totalGroups,
                'totalSplits' => $totalSplits,
                'monthlyExpenses' => $monthlyExpenses,
                'expenseTrendLabels' => $expenseTrendLabels,
                'expenseTrendData' => $expenseTrendData,
                'categoryLabels' => $categoryLabels,
                'categoryTotals' => $categoryTotals,
            ],
        ]);
    }
}
