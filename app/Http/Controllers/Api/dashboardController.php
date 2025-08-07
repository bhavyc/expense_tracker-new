<?php

 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        // Filter by user or group if provided
        $userId = $request->query('user_id');
        $groupId = $request->query('group_id');

        $query = Expense::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        // Summary stats
        $totalExpenses = $query->sum('amount');
        $expenseCount = $query->count();

        // Category-wise breakdown
        $categoryBreakdown = $query->select('category', DB::raw('SUM(amount) as total'))
                                   ->groupBy('category')
                                   ->get();

        // Monthly trend
        $monthly = $query->select(
                        DB::raw("DATE_FORMAT(expense_date, '%Y-%m') as month"),
                        DB::raw('SUM(amount) as total')
                    )
                    ->groupBy('month')
                    ->orderBy('month', 'asc')
                    ->get();

        return response()->json([
            'total_expenses' => $totalExpenses,
            'expense_count' => $expenseCount,
            'category_breakdown' => $categoryBreakdown,
            'monthly_trend' => $monthly,
        ]);
    }
}
