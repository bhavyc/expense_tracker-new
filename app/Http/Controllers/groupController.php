<?php

// namespace App\Http\Controllers;


// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Group;
// use Carbon\Carbon;
// use App\Models\Expense;
// use App\Models\GroupMember;
// use App\Models\User;

// class groupController extends Controller
// {
//     // All Groups Index
//     public function index()
//     {
//         $user = Auth::user();

//         $groups = $user->groups()
//             ->with(['expenses', 'members'])
//             ->get()
//             ->map(function ($group) {
//                 $totalSpent = $group->expenses()->sum('amount');
//                 $group->totalSpent = $totalSpent;
//                 $group->budgetLeft = max($group->budget - $totalSpent, 0);
//                 return $group;
//             });

//         $users = User::where('id', '!=', $user->id)
//                     ->orderBy('name')
//                     ->get();

//         return view('user.groups.index', compact('groups', 'users'));
//     }

//     // Show form to create group
//     public function create()
//     {
//         return view('user.groups.create');
//     }

//     // Store new group
//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string|max:255',
//             'budget' => 'required|numeric|min:0',
//             'permanent' => 'sometimes|boolean',
//             'category' => 'required|in:expenses,incomes,loans,investments',
//         ]);

//         $user = Auth::user();

//         $user->groups()->create([
//             'name' => $request->name,
//             'description' => $request->description,
//             'budget' => $request->budget,
//             'permanent' => $request->has('permanent') ? true : false,
//             'created_by' => $user->id,
//             'category' => $request->category,
//         ]);

//         return redirect()->route('user.groups.index')->with('success', 'Group created successfully.');
//     }

//     // Show edit form
//     public function edit($id)
//     {
//         $user = Auth::user();
//         $group = $user->groups()->findOrFail($id);

//         return view('user.groups.edit', compact('group'));
//     }

//     // Update group
//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string|max:255',
//             'budget' => 'required|numeric|min:0',
//             'permanent' => 'sometimes|boolean',
//             'category' => 'required|in:expenses,incomes,loans,investments',
//         ]);

//         $user = Auth::user();
//         $group = $user->groups()->findOrFail($id);

//         $group->update([
//             'name' => $request->name,
//             'description' => $request->description,
//             'budget' => $request->budget,
//             'permanent' => $request->has('permanent') ? true : false,
//             'category' => $request->category,
//         ]);

//         return redirect()->route('user.groups.index')->with('success', 'Group updated successfully.');
//     }

//     // Update only budget (Ajax call)
//     public function updateBudget(Request $request, $id)
//     {
//         $request->validate([
//             'budget' => 'required|numeric|min:0',
//         ]);

//         $group = Group::findOrFail($id);

//         if ($group->created_by !== auth()->id()) {
//             abort(403, 'You are not allowed to update budget.');
//         }

//         $group->update(['budget' => $request->budget]);

//         return response()->json([
//             'message'=>'Budget updated successfully',
//             'success' => true,
//             'budget' => $group->budget,
//             'budgetLeft' => max($group->budget - $group->expenses()->sum('amount'), 0),
//         ]);
//     }

//     // Weekly expenses
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

//         $budgetLeft = $group->budget - $totalSpent;
//         if ($budgetLeft < 0) $budgetLeft = 0;

//         return view('group.weekly-expenses', [
//             'group' => $group,
//             'weeklyData' => $weeklyData,
//             'budgetLeft' => $budgetLeft,
//             'totalSpent' => $totalSpent,
//         ]);
//     }

//     // Analytics Page
//     public function analytics($groupId)
//     {
//         $group = Group::with('users')->findOrFail($groupId);

//         if (!$group->users->contains(auth()->id())) {
//             abort(403, 'Unauthorized');
//         }

//         $weeklyExpenses = Expense::selectRaw('YEAR(expense_date) as year, WEEK(expense_date, 1) as week, SUM(amount) as total')
//             ->where('group_id', $groupId)
//             ->groupBy('year', 'week')
//             ->orderBy('year')
//             ->orderBy('week')
//             ->get()
//             ->map(function ($item) {
//                 $startOfWeek = \Carbon\Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();
//                 $endOfWeek = \Carbon\Carbon::now()->setISODate($item->year, $item->week)->endOfWeek();
//                 if ($endOfWeek->isFuture()) {
//                     $endOfWeek = \Carbon\Carbon::today();
//                 }
//                 $item->date_range = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');
//                 return $item;
//             });

//         $categoryExpenses = Expense::selectRaw('category, SUM(amount) as total')
//             ->where('group_id', $groupId)
//             ->groupBy('category')
//             ->get();

//         return view('user.groups.group-analytics', compact('group', 'weeklyExpenses', 'categoryExpenses'));
//     }

//     // Add member by phone
//     public function addMember(Request $request, $groupId)
//     {
//         $group = Group::findOrFail($groupId);

//         if ($group->created_by !== auth()->id()) {
//             abort(403, 'You are not allowed to add members to this group.');
//         }

//         $validated = $request->validate([
//             'phone_number' => 'required|string|exists:users,phone_number',
//         ]);

//         $user = User::where('phone_number', $validated['phone_number'])->first();

//         if (!$user) {
//             return back()->with('error', 'No user found with this phone number.');
//         }

//         $exists = GroupMember::where('group_id', $groupId)
//             ->where('user_id', $user->id)
//             ->exists();

//         if ($exists) {
//             return back()->with('error', 'User is already a member of this group.');
//         }

//         GroupMember::create([
//             'group_id' => $groupId,
//             'user_id'  => $user->id,
//         ]);

//         return back()->with('success', 'Member added successfully!');
//     }

//     // Monthly Expenses
//     public function monthlyExpenses($groupId)
//     {
//         $group = Group::with('users')->findOrFail($groupId);

//         if (!$group->users->contains(auth()->id())) {
//             abort(403, 'Unauthorized');
//         }

//         $startDate = Carbon::now()->startOfMonth();
//         $endDate = Carbon::now()->endOfMonth();

//         $userExpenses = $group->users->map(function ($user) use ($groupId, $startDate, $endDate) {
//             $spent = $user->expenses()
//                 ->where('group_id', $groupId)
//                 ->whereBetween('expense_date', [$startDate, $endDate])
//                 ->sum('amount');
//             return [
//                 'name' => $user->name,
//                 'spent' => $spent
//             ];
//         });

//         $totalSpent = $userExpenses->sum('spent');
//         $budgetLeft = max($group->budget - $totalSpent, 0);

//         return view('user.groups.monthly-expenses', compact('group', 'userExpenses', 'totalSpent', 'budgetLeft'));
//     }

//     // Get group users (Ajax)
//     public function getUsers($id)
//     {
//         $group = Group::with('users')->find($id);

//         if (!$group) {
//             return response()->json([]);
//         }

//         $users = $group->users->map(function($user){
//             return [
//                 'id' => $user->id,
//                 'name' => $user->name
//             ];
//         });

//         return response()->json($users);
//     }

//     // Monthly Analytics
//     public function monthlyAnalytics($groupId)
//     {
//         $group = Group::with('users')->findOrFail($groupId);

//         if (!$group->users->contains(auth()->id())) {
//             abort(403, 'Unauthorized');
//         }

//         $startOfMonth = Carbon::now()->startOfMonth();
//         $endOfMonth = Carbon::now()->endOfMonth();

//         $weeklyExpenses = Expense::selectRaw('YEAR(expense_date) as year, WEEK(expense_date,1) as week, SUM(amount) as total')
//             ->where('group_id', $groupId)
//             ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
//             ->groupBy('year', 'week')
//             ->orderBy('week')
//             ->get()
//             ->map(function ($item) {
//                 $startWeek = Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();
//                 $endWeek = Carbon::now()->setISODate($item->year, $item->week)->endOfWeek();
//                 $item->label = $startWeek->format('d M') . ' - ' . $endWeek->format('d M');
//                 return $item;
//             });

//         $categoryExpenses = Expense::selectRaw('category, SUM(amount) as total')
//             ->where('group_id', $groupId)
//             ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
//             ->groupBy('category')
//             ->get();

//         return view('user.groups.monthly-analytics', compact('group', 'weeklyExpenses', 'categoryExpenses'));
//     }

//     // Delete a group
// public function destroy($id)
// {
//     $user = Auth::user();
//     $group = $user->groups()->findOrFail($id);

//     $group->delete();

//     return redirect()->route('user.groups.index')->with('success', 'Group deleted successfully.');
// }

// }  

 
 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Carbon\Carbon;
use App\Models\Expense;
use App\Models\GroupMember;
use App\Models\User;

class groupController extends Controller
{
    // Display all groups for the authenticated user
    public function index()
    {
        $user = Auth::user();

        $groups = $user->groups()
            ->with(['expenses', 'members'])
            ->get()
            ->map(function ($group) {
                $totalSpent = $group->expenses()->sum('amount');

                // Effective budget after considering carry forward
                $effectiveBudget = max($group->budget - $group->carry_forward_balance, 0);
                $budgetLeft = $effectiveBudget - $totalSpent;

                $group->totalSpent = $totalSpent;
                $group->effectiveBudget = $effectiveBudget;
                $group->budgetLeft = $budgetLeft;
                $group->carryForward = $group->carry_forward_balance;

                return $group;
            });

        $users = User::where('id', '!=', $user->id)
                    ->orderBy('name')
                    ->get();

        return view('user.groups.index', compact('groups', 'users'));
    }

    // Show form to create a group
    public function create()
    {
        return view('user.groups.create');
    }

    // Store new group
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

        $user->groups()->create([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent') ? true : false,
            'created_by' => $user->id,
            'category' => $request->category,
            'carry_forward_balance' => 0,
        ]);

        return redirect()->route('user.groups.index')->with('success', 'Group created successfully.');
    }

    // Edit group form
    public function edit($id)
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);

        return view('user.groups.edit', compact('group'));
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

        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent') ? true : false,
            'category' => $request->category,
        ]);

        return redirect()->route('user.groups.index')->with('success', 'Group updated successfully.');
    }

    // Update budget via AJAX
    public function updateBudget(Request $request, $id)
    {
        $request->validate([
            'budget' => 'required|numeric|min:0',
        ]);

        $group = Group::findOrFail($id);

        if ($group->created_by !== auth()->id()) {
            abort(403, 'You are not allowed to update budget.');
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
            'success' => true,
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
            abort(403, 'You are not allowed to add members.');
        }

        $validated = $request->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
        ]);

        $user = User::where('phone_number', $validated['phone_number'])->first();

        if (!$user) {
            return back()->with('error', 'No user found with this phone number.');
        }

        $exists = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'User already a member.');
        }

        GroupMember::create([
            'group_id' => $groupId,
            'user_id' => $user->id
        ]);

        return back()->with('success', 'Member added successfully!');
    }

    // Monthly expenses with carry forward logic
    // Monthly Expenses with carry forward logic
public function monthlyExpenses($groupId)
{
    // Fetch the group with its users
    $group = Group::with('users')->findOrFail($groupId);

    // Check if the logged-in user is part of this group
    if (!$group->users->contains(auth()->id())) {
        abort(403, 'Unauthorized');
    }

    // Current month's start and end dates
    $startDate = Carbon::now()->startOfMonth();
    $endDate = Carbon::now()->endOfMonth();

    // Calculate each user's spending this month
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

    // Total spent by all users
    $totalSpent = $userExpenses->sum('spent');

    // Calculate effective budget considering previous carry forward
    $effectiveBudget = max($group->budget - $group->carry_forward_balance, 0);
    $budgetLeft = $effectiveBudget - $totalSpent;

    // Update carry_forward_balance if budget exceeded
    if ($budgetLeft < 0) {
        $group->carry_forward_balance = abs($budgetLeft); // save exceeded amount
        $budgetLeft = 0; // budget left cannot be negative
    } else {
        $group->carry_forward_balance = 0; // reset if under budget
    }

    // Save the updated carry_forward_balance
    $group->save();

    // Pass all data to the Blade view
    return view('user.groups.monthly-expenses', compact(
        'group', 
        'userExpenses', 
        'totalSpent', 
        'budgetLeft', 
        'effectiveBudget'
    ));
}


    // Delete group
    public function destroy($id)
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);
        $group->delete();

        return redirect()->route('user.groups.index')->with('success', 'Group deleted successfully.');
    }



    // Get group users  
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



    public function monthlyAnalytics($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (!$group->users->contains(auth()->id())) {
            abort(403, 'Unauthorized');
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

        return view('user.groups.monthly-analytics', compact('group', 'weeklyExpenses', 'categoryExpenses'));
    }


// for weekly analytics of groups
     public function analytics($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (!$group->users->contains(auth()->id())) {
            abort(403, 'Unauthorized');
        }

        $weeklyExpenses = Expense::selectRaw('YEAR(expense_date) as year, WEEK(expense_date, 1) as week, SUM(amount) as total')
            ->where('group_id', $groupId)
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->map(function ($item) {
                $startOfWeek = \Carbon\Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($item->year, $item->week)->endOfWeek();
                if ($endOfWeek->isFuture()) {
                    $endOfWeek = \Carbon\Carbon::today();
                }
                $item->date_range = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');
                return $item;
            });

        $categoryExpenses = Expense::selectRaw('category, SUM(amount) as total')
            ->where('group_id', $groupId)
            ->groupBy('category')
            ->get();

        return view('user.groups.group-analytics', compact('group', 'weeklyExpenses', 'categoryExpenses'));
    }
}
