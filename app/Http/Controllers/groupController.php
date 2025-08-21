<?php

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
    
    // public function index()
    // {
    //     $user = Auth::user();

    //     $groups = $user->groups()->with('expenses')->get()->map(function ($group) {
    //         $totalSpent = $group->expenses()->sum('amount');  // total spent by group
    //         $group->totalSpent = $totalSpent;
    //         $group->budgetLeft = max($group->budget - $totalSpent, 0);
    //         return $group;
    //     });

    //     return view('user.groups.index', compact('groups'));
    // }

    

public function index()
{
    $user = Auth::user();

    // Groups + expenses + members eager load
    $groups = $user->groups()
        ->with(['expenses', 'members']) 
        ->get()
        ->map(function ($group) {
            $totalSpent = $group->expenses()->sum('amount');
            $group->totalSpent = $totalSpent;
            $group->budgetLeft = max($group->budget - $totalSpent, 0);
            return $group;
        });

    // All other users except current one
    $users = User::where('id', '!=', $user->id)
                 ->orderBy('name')
                 ->get();

    return view('user.groups.index', compact('groups', 'users'));
}


    // Show form to create group
    public function create()
    {
        return view('user.groups.create');
    }

    // Store new group with permanent option
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'budget' => 'required|numeric|min:0',
            'permanent' => 'sometimes|boolean',
        ]);

        $user = Auth::user();

        $user->groups()->create([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent') ? true : false,
            'created_by' => $user->id,
        ]);

        return redirect()->route('user.groups.index')->with('success', 'Group created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);

        return view('user.groups.edit', compact('group'));
    }

    // Update group details including permanent
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'budget' => 'required|numeric|min:0',
            'permanent' => 'sometimes|boolean',
        ]);

        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent') ? true : false,
        ]);

        return redirect()->route('user.groups.index')->with('success', 'Group updated successfully.');
    }


   public function updateBudget(Request $request, $id)
{
    $request->validate([
        'budget' => 'required|numeric|min:0',
    ]);

    $group = Group::findOrFail($id);

    // Optionally, permission check
    if ($group->created_by !== auth()->id()) {
        abort(403, 'You are not allowed to update budget.');
    }

    $group->update(['budget' => $request->budget]);

    return response()->json([
        'message'=>'Budget updated successfully',
        'success' => true,
        'budget' => $group->budget,
        'budgetLeft' => max($group->budget - $group->expenses()->sum('amount'), 0),
    ]);
}


    // Weekly expenses  
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

        $budgetLeft = $group->budget - $totalSpent;
        if ($budgetLeft < 0) $budgetLeft = 0;

        return view('group.weekly-expenses', [
            'group' => $group,
            'weeklyData' => $weeklyData,
            'budgetLeft' => $budgetLeft,
            'totalSpent' => $totalSpent,
        ]);
    }

public function analytics($groupId)
{
    // Fetch the group along with its users
    $group = Group::with('users')->findOrFail($groupId);

    // Check if the current user is a member of the group
    if (!$group->users->contains(auth()->id())) {
        abort(403, 'Unauthorized');
    }

    // Weekly Expenses
    $weeklyExpenses = Expense::selectRaw('YEAR(expense_date) as year, WEEK(expense_date, 1) as week, SUM(amount) as total')
        ->where('group_id', $groupId)
        ->groupBy('year', 'week')
        ->orderBy('year')
        ->orderBy('week')
        ->get()
        ->map(function ($item) {
            
            $startOfWeek = \Carbon\Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();

            // End of the week (Sunday)
            $endOfWeek = \Carbon\Carbon::now()->setISODate($item->year, $item->week)->endOfWeek();

            // If the end date is in the future, show today instead
            if ($endOfWeek->isFuture()) {
                $endOfWeek = \Carbon\Carbon::today();
            }

            // Format date range
            $item->date_range = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');

            return $item;
        });

    // Category-wise Expenses
    $categoryExpenses = Expense::selectRaw('category, SUM(amount) as total')
        ->where('group_id', $groupId)
        ->groupBy('category')
        ->get();

    // Return view with data
    return view('user.groups.group-analytics', compact('group', 'weeklyExpenses', 'categoryExpenses'));
}


public function addMember(Request $request, $groupId)
{
    $group = \App\Models\Group::findOrFail($groupId);

    // Check if logged-in user is the creator
    if ($group->created_by !== auth()->id()) {
        abort(403, 'You are not allowed to add members to this group.');
    }

    // Validate input
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    // Check if already a member
    $exists = \App\Models\GroupMember::where('group_id', $groupId)
        ->where('user_id', $validated['user_id'])
        ->exists();

    if ($exists) {
        return back()->with('error', 'User is already a member of this group.');
    }

    // Add member
    \App\Models\GroupMember::create([
        'group_id' => $groupId,
        'user_id' => $validated['user_id'],
    ]);

    return back()->with('success', 'Member added successfully!');
}


 // Monthly Expenses
    public function monthlyExpenses($groupId)
    {
        $group = Group::with('users')->findOrFail($groupId);

        if (!$group->users->contains(auth()->id())) {
            abort(403, 'Unauthorized');
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

        return view('user.groups.monthly-expenses', compact('group', 'userExpenses', 'totalSpent', 'budgetLeft'));
    }

  public function getUsers($id)
    {
        $group = Group::with('users')->find($id);

        if (!$group) {
            return response()->json([]);
        }

        // JSON me sirf id aur name bhej rahe
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

    // User check
    if (!$group->users->contains(auth()->id())) {
        abort(403, 'Unauthorized');
    }

    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();

    // Weekly expenses for line chart
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

    // Category-wise expenses for pie chart
    $categoryExpenses = Expense::selectRaw('category, SUM(amount) as total')
        ->where('group_id', $groupId)
        ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
        ->groupBy('category')
        ->get();

    return view('user.groups.monthly-analytics', compact('group', 'weeklyExpenses', 'categoryExpenses'));
}
}

 

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Group;
// use Carbon\Carbon;

// class groupController extends Controller
// {
//    public function index()
// {
//     $user = Auth::user();

//     $groups = $user->groups()->with('expenses')->get()->map(function ($group) {
//         $totalSpent = $group->expenses()->sum('amount');  // total spent by group
//         $group->totalSpent = $totalSpent;
//         $group->budgetLeft = max($group->budget - $totalSpent, 0);
//         return $group;
//     });

//     return view('user.groups.index', compact('groups'));
// }

//     public function create(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'required|string|max:255',
//             'budget' => 'required|numeric|min:0',  // budget validate
//         ]);

//         $user = Auth::user();

//         $user->groups()->create([
//             'name' => $request->name,
//             'description' => $request->description,
//             'budget' => $request->budget,         // budget save
//             'created_by' => $user->id
//         ]);

//         return redirect()->route('user.groups.index')->with('success', 'Group created successfully.');
//     }

//     public function edit($id)
//     {
//         $user = Auth::user();
//         $group = $user->groups()->findOrFail($id);

//         return view('user.groups.edit', compact('group'));
//     }

//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'required|string|max:255',
//             'budget' => 'required|numeric|min:0',  // budget validate
//         ]);

//         $user = Auth::user();
//         $group = $user->groups()->findOrFail($id);

//         $group->update([
//             'name' => $request->name,
//             'description' => $request->description,
//             'budget' => $request->budget,
//         ]);

//         return redirect()->route('user.groups.index')->with('success', 'Group updated successfully.');
//     }


//     public function weeklyExpenses($id)
//     {
//         $group = Group::with(['users.expenses' => function ($query) use ($id) {
//             $query->where('group_id', $id)
//                   ->where('expense_date', '>=', Carbon::now()->subDays(7));
//         }])->findOrFail($id);

//         // Total spent by all users in last 7 days for this group
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
// }

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Group;
// use App\Models\GroupMember;
// use Carbon\Carbon;
// class groupController extends Controller
// {
    
//     public function index(){
//         $user= Auth::user();
//         $groups= $user->groups()->get();
//         return view('user.groups.index', compact('groups'));
//     }

//    public function create(Request $request)
// {
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'description' => 'required|string|max:255',
//     ]);

//     $user = Auth::user();

//     $user->groups()->create([
//         'name' => $request->name,
//         'description' => $request->description,
//         'created_by' => $user->id
//     ]);

//     return redirect()->route('user.groups.index')->with('success', 'Group created successfully.');
// }

//  public function edit($id)
// {
//     $user = Auth::user();
//     $group = $user->groups()->findOrFail($id);  

//     return view('user.groups.edit', compact('group'));
// }

// public function update(Request $request, $id)
// {
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'description' => 'required|string|max:255',
//     ]);

//     $user = Auth::user();
//     $group = $user->groups()->findOrFail($id);

//     $group->update([
//         'name' => $request->name,
//         'description' => $request->description
//     ]);

//     return redirect()->route('user.groups.index')->with('success', 'Group updated successfully.');
// }


// public function weeklyExpenses($id)
//     {
//         $group = Group::with(['users.expenses' => function ($query) use ($id) {
//             $query->where('group_id', $id)
//                   ->where('expense_date', '>=', Carbon::now()->subDays(7));
//         }])->findOrFail($id);

        
//         $weeklyData = $group->users->map(function ($user) {
//             return [
//                 'name' => $user->name,
//                 'total' => $user->expenses->sum('amount')
//             ];
//         });

//         return view('group.weekly-expenses', [
//             'group' => $group,
//             'weeklyData' => $weeklyData
//         ]);
//     }
// }
