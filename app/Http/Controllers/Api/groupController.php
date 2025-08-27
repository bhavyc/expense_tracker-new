<?php
 
// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Group;
// use Carbon\Carbon;

// class GroupController extends Controller
// {
     
//     public function index()
//     {
//         $user = Auth::user();

//         $groups = $user->groups()->with('expenses')->get()->map(function ($group) {
//             $totalSpent = $group->expenses()->sum('amount');
//             $group->totalSpent = $totalSpent;
//             $group->budgetLeft = max($group->budget - $totalSpent, 0);
//             return $group;
//         });

//         return response()->json([
//             'success' => true,
//             'groups' => $groups,
//         ]);
//     }

//     // Store new group
//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string|max:255',
//             'budget' => 'required|numeric|min:0',
//             'permanent' => 'sometimes|boolean',
//         ]);

//         $user = Auth::user();

//         $group = $user->groups()->create([
//             'name' => $request->name,
//             'description' => $request->description,
//             'budget' => $request->budget,
//             'permanent' => $request->has('permanent') ? true : false,
//             'created_by' => $user->id,
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Group created successfully',
//             'group' => $group,
//         ]);
//     }

//     // Update group
//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string|max:255',
//             'budget' => 'required|numeric|min:0',
//             'permanent' => 'sometimes|boolean',
//         ]);

//         $user = Auth::user();
//         $group = $user->groups()->findOrFail($id);

//         $group->update([
//             'name' => $request->name,
//             'description' => $request->description,
//             'budget' => $request->budget,
//             'permanent' => $request->has('permanent') ? true : false,
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Group updated successfully',
//             'group' => $group,
//         ]);
//     }

//     // Weekly expenses summary for group
//     public function weeklyExpenses($id)
//     {
//         $group = Group::with(['users.expenses' => function ($query) use ($id) {
//             $query->where('group_id', $id)
//                   ->where('expense_date', '>=', Carbon::now()->subDays(7));
//         }])->findOrFail($id);

//         $totalSpent = 0;

//         $weeklyData = $group->users->map(function ($user) use (&$totalSpent, $id) {
//             $userSpent = $user->expenses->where('group_id', $id)->sum('amount');
//             $totalSpent += $userSpent;

//             return [
//                 'name' => $user->name,
//                 'total' => $userSpent,
//             ];
//         });

//         $budgetLeft = max($group->budget - $totalSpent, 0);

//         return response()->json([
//             'success' => true,
//             'group' => $group,
//             'weeklyData' => $weeklyData,
//             'budgetLeft' => $budgetLeft,
//             'totalSpent' => $totalSpent,
//         ]);
//     }
// }


 

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
    // ✅ All Groups Index
    public function index()
    {
        $user = Auth::user();

        $groups = $user->groups()
            ->with(['expenses', 'members'])
            ->get()
            ->map(function ($group) {
                $totalSpent = $group->expenses()->sum('amount');
                $group->totalSpent = $totalSpent;
                $group->budgetLeft = max($group->budget - $totalSpent, 0);
                return $group;
            });

        return response()->json([
            'success' => true,
            'groups' => $groups
        ]);
    }

    // ✅ Store new group
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
            'permanent' => $request->has('permanent'),
            'created_by' => $user->id,
            'category' => $request->category,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Group created successfully',
            'group' => $group,
        ]);
    }

    // ✅ Show single group
    public function show($id)
    {
        $group = Group::with(['expenses', 'members'])->findOrFail($id);

        $totalSpent = $group->expenses()->sum('amount');
        $group->totalSpent = $totalSpent;
        $group->budgetLeft = max($group->budget - $totalSpent, 0);

        return response()->json([
            'success' => true,
            'group' => $group,
        ]);
    }

    // ✅ Update group
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'budget' => 'required|numeric|min:0',
            'permanent' => 'sometimes|boolean',
            'category' => 'required|in:expenses,incomes,loans,investments',
        ]);

        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent'),
            'category' => $request->category,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully',
            'group' => $group,
        ]);
    }

    // ✅ Delete a group
    public function destroy($id)
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Group deleted successfully'
        ]);
    }

    // ✅ Update only budget
    public function updateBudget(Request $request, $id)
    {
        $request->validate([
            'budget' => 'required|numeric|min:0',
        ]);

        $group = Group::findOrFail($id);

        if ($group->created_by !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $group->update(['budget' => $request->budget]);

        return response()->json([
            'success' => true,
            'message' => 'Budget updated successfully',
            'budget' => $group->budget,
            'budgetLeft' => max($group->budget - $group->expenses()->sum('amount'), 0),
        ]);
    }

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

    // ✅ Monthly Expenses
    public function monthlyExpenses($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (!$group->users->contains(auth()->id())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
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
        $budgetLeft = max($group->budget - $totalSpent, 0);

        return response()->json([
            'success' => true,
            'userExpenses' => $userExpenses,
            'totalSpent' => $totalSpent,
            'budgetLeft' => $budgetLeft,
        ]);
    }

    // ✅ Full Analytics
    public function analytics($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (!$group->users->contains(auth()->id())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
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
            'success' => true,
            'weeklyExpenses' => $weeklyExpenses,
            'categoryExpenses' => $categoryExpenses
        ]);
    }

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

    // ✅ Add member by phone
    public function addMember(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);

        if ($group->created_by !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
        ]);

        $user = User::where('phone_number', $validated['phone_number'])->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No user found'], 404);
        }

        $exists = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Already a member'], 409);
        }

        GroupMember::create([
            'group_id' => $groupId,
            'user_id'  => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member added successfully'
        ]);
    }

    // ✅ Get group users
    public function getUsers($id)
    {
        $group = Group::with('users')->find($id);

        if (!$group) {
            return response()->json(['success' => false, 'users' => []], 404);
        }

        $users = $group->users->map(function($user){
            return [
                'id' => $user->id,
                'name' => $user->name
            ];
        });

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}
