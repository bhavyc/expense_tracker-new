<?php
  

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Carbon\Carbon;
use App\Models\Expense;
use App\Models\GroupMember;
use App\Models\User;

class GroupController extends Controller
{
     
    
 

     
    
    // ✅ Weekly expenses
    public function weeklyExpenses($id)
    {
        $group = Group::with(['users.expenses' => function ($query) use ($id) {
            $query->where('group_id', $id)
                  ->where('expense_date', '>=', Carbon::now()->subDays(7));
        }])->findOrFail($id);

        $totalSpent = 0;

        $weeklyData = $group->users->map(function ($user) use (&$totalSpent, $id) {
            $userSpent = $user->expenses->where('group_id', $id)->sum('amount');
            $totalSpent += $userSpent;

            return [
                'name' => $user->name,
                'total' => $userSpent,
            ];
        });

        $budgetLeft = max($group->budget - $totalSpent, 0);

        return response()->json([
            'success' => true,
            'group' => $group,
            'weeklyData' => $weeklyData,
            'budgetLeft' => $budgetLeft,
            'totalSpent' => $totalSpent,
        ]);
    }

   
    
    // ✅ Full Analytics
    
    // ✅ Monthly Analytics
    public function monthlyAnalytics($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (!$group->users->contains(auth()->id())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $weeklyExpenses = Expense::selectRaw('YEAR(expense_date) as year, WEEK(expense_date,1) as week, SUM(amount) as total')
            ->where('group_id', $groupId)
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->groupBy('year', 'week')
            ->orderBy('week')
            ->get()
            ->map(function ($item) {
                $startWeek = Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();
                $endWeek = Carbon::now()->setISODate($item->year, $item->week)->endOfWeek();
                $item->label = $startWeek->format('d M') . ' - ' . $endWeek->format('d M');
                return $item;
            });

        $categoryExpenses = Expense::selectRaw('category, SUM(amount) as total')
            ->where('group_id', $groupId)
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->groupBy('category')
            ->get();

        return response()->json([
            'success' => true,
            'weeklyExpenses' => $weeklyExpenses,
            'categoryExpenses' => $categoryExpenses
        ]);
    }

     
    

      public function index()
    {
        $user = Auth::user();

        $groups = $user->groups()
            ->with(['expenses', 'members'])
            ->get()
            ->map(function ($group) {
                $totalSpent = $group->expenses()->sum('amount');
                $effectiveBudget = max($group->budget - $group->carry_forward_balance, 0);
                $budgetLeft = $effectiveBudget - $totalSpent;

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'budget' => $group->budget,
                    'effectiveBudget' => $effectiveBudget,
                    'budgetLeft' => max($budgetLeft, 0),
                    'carryForward' => $group->carry_forward_balance,
                    'totalSpent' => $totalSpent,
                    'category' => $group->category,
                    'permanent' => $group->permanent,
                ];
            });

        return response()->json($groups);
    }

    // Store a new group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'budget' => 'required|numeric|min:0',
            'permanent' => 'sometimes|boolean',
            'category' => 'required|in:expenses,incomes,loans,investments',
        ]);

        $user = Auth::user();

        $group = $user->groups()->create([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent') ? true : false,
            'created_by' => $user->id,
            'category' => $request->category,
            'carry_forward_balance' => 0,
        ]);

        return response()->json(['message' => 'Group created successfully', 'group' => $group], 201);
    }

    // Update group details
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'budget' => 'required|numeric|min:0',
            'permanent' => 'sometimes|boolean',
            'category' => 'required|in:expenses,incomes,loans,investments',
        ]);

        $group = Group::findOrFail($id);

        if ($group->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent') ? true : false,
            'category' => $request->category,
        ]);

        return response()->json(['message' => 'Group updated successfully', 'group' => $group]);
    }

    // Delete a group
    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        if ($group->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $group->delete();

        return response()->json(['message' => 'Group deleted successfully']);
    }

    // Update budget via API
    public function updateBudget(Request $request, $id)
    {
        $request->validate([
            'budget' => 'required|numeric|min:0',
        ]);

        $group = Group::findOrFail($id);

        if ($group->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $group->update(['budget' => $request->budget]);

        // Recalculate budget left and carry forward
        $totalSpent = $group->expenses()->sum('amount');
        $effectiveBudget = max($group->budget - $group->carry_forward_balance, 0);
        $budgetLeft = $effectiveBudget - $totalSpent;

        if ($budgetLeft < 0) {
            $group->carry_forward_balance = abs($budgetLeft);
            $budgetLeft = 0;
        } else {
            $group->carry_forward_balance = 0;
        }

        $group->save();

        return response()->json([
            'message' => 'Budget updated successfully',
            'budget' => $group->budget,
            'budgetLeft' => $budgetLeft,
            'effectiveBudget' => $effectiveBudget,
            'carryForward' => $group->carry_forward_balance
        ]);
    }

    // Add member to group
    public function addMember(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);

        if ($group->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $exists = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'User already a member'], 400);
        }

        GroupMember::create([
            'group_id' => $groupId,
            'user_id' => $user->id
        ]);

        return response()->json(['message' => 'Member added successfully']);
    }

    // Get all users in a group
    public function getUsers($id)
    {
        $group = Group::with('users')->find($id);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        $users = $group->users->map(function($user){
            return [
                'id' => $user->id,
                'name' => $user->name
            ];
        });

        return response()->json($users);
    }

    // Monthly Expenses API
    public function monthlyExpenses($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (!$group->users->contains(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $userExpenses = $group->users->map(function ($user) use ($groupId, $startDate, $endDate) {
            $spent = $user->expenses()
                ->where('group_id', $groupId)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');

            return [
                'name' => $user->name,
                'spent' => $spent
            ];
        });

        $totalSpent = $userExpenses->sum('spent');
        $effectiveBudget = max($group->budget - $group->carry_forward_balance, 0);
        $budgetLeft = $effectiveBudget - $totalSpent;

        if ($budgetLeft < 0) {
            $group->carry_forward_balance = abs($budgetLeft);
            $budgetLeft = 0;
        } else {
            $group->carry_forward_balance = 0;
        }

        $group->save();

        return response()->json([
            'group' => $group,
            'userExpenses' => $userExpenses,
            'totalSpent' => $totalSpent,
            'budgetLeft' => $budgetLeft,
            'effectiveBudget' => $effectiveBudget,
            'carryForward' => $group->carry_forward_balance
        ]);
    }

    // Weekly analytics API
    public function analytics($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (!$group->users->contains(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $weeklyExpenses = Expense::selectRaw('YEAR(expense_date) as year, WEEK(expense_date, 1) as week, SUM(amount) as total')
            ->where('group_id', $groupId)
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->map(function ($item) {
                $startOfWeek = Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();
                $endOfWeek = Carbon::now()->setISODate($item->year, $item->week)->endOfWeek();
                if ($endOfWeek->isFuture()) {
                    $endOfWeek = Carbon::today();
                }
                $item->date_range = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');
                return $item;
            });

        $categoryExpenses = Expense::selectRaw('category, SUM(amount) as total')
            ->where('group_id', $groupId)
            ->groupBy('category')
            ->get();

        return response()->json([
            'group' => $group,
            'weeklyExpenses' => $weeklyExpenses,
            'categoryExpenses' => $categoryExpenses
        ]);
    }
}
