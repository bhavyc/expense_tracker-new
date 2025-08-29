<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\GroupMember;
use App\Models\Expense;
use App\Models\Split;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class groupController extends Controller
{
    // List all groups
    public function index()
    {
        $groups = Group::with('creator')->orderBy('budget', 'asc')->get();

        if ($groups->isEmpty()) {
            return redirect()->route('admin.groups.create')
                             ->with('warning', 'No groups found. Please create one.');
        }

        return view('admin.groups.index', compact('groups'));
    }

    // Show form to create group
    public function create()
    {
        $users = User::all();
        return view('admin.groups.create', compact('users'));
    }

    // Store new group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'created_by' => 'required|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'permanent' => 'nullable|boolean',
            'category' => 'required|in:Expenses,Incomes,Loans,Investments',
        ]);

        $data = $request->all();
        $data['permanent'] = $request->has('permanent') ? 1 : 0;

        $group = Group::create($data);
        $group->users()->attach($request->created_by);

        return redirect()->route('admin.groups.index')->with('success', 'Group created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $group = Group::findOrFail($id);
        $users = User::all();
        return view('admin.groups.edit', compact('group', 'users'));
    }

    // Update group
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'created_by' => 'required|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'permanent' => 'nullable|boolean',
            'category' => 'required|in:Expenses,Incomes,Loans,Investments',
        ]);

        $data = $request->all();
        $data['permanent'] = $request->has('permanent') ? 1 : 0;

        $group = Group::findOrFail($id);
        $group->update($data);

        return redirect()->route('admin.groups.index')->with('success', 'Group updated successfully.');
    }

    // Delete group
    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.groups.index')->with('success', 'Group deleted successfully.');
    }

    // Weekly analytics for all groups
    public function weeklyAnalytics()
    {
        $weeklyExpenses = DB::table('groups as g')
            ->leftJoin('expenses as e', 'g.id', '=', 'e.group_id')
            ->select(
                'g.id as group_id',
                'g.name as group_name',
                DB::raw('YEARWEEK(e.expense_date, 1) as year_week'),
                DB::raw('SUM(e.amount) as total_weekly_expense')
            )
            ->groupBy('g.id', 'g.name', DB::raw('YEARWEEK(e.expense_date, 1)'))
            ->orderBy('g.id')
            ->orderBy('year_week')
            ->get();

        return view('admin.groups.weekly_analytics', compact('weeklyExpenses'));
    }

    // Weekly analytics for a single group
    public function analytics($id)
    {
        $group = Group::findOrFail($id);

        $weeklyExpenses = DB::table('expenses as e')
            ->select(
                DB::raw('YEARWEEK(e.expense_date, 1) as year_week'),
                DB::raw('MIN(e.expense_date) as start_date'),
                DB::raw('MAX(e.expense_date) as end_date'),
                DB::raw('SUM(e.amount) as total_weekly_expense')
            )
            ->where('e.group_id', $id)
            ->groupBy(DB::raw('YEARWEEK(e.expense_date, 1)'))
            ->orderBy('year_week')
            ->get();

        return view('admin.groups.analytics', compact('group', 'weeklyExpenses'));
    }

    // User-wise weekly expenses
    public function userWeeklyExpenses($id)
    {
        $group = Group::findOrFail($id);

        $weeklyExpenses = DB::table('expenses as e')
            ->join('users as u', 'e.user_id', '=', 'u.id')
            ->select(
                'u.name as user_name',
                DB::raw('YEARWEEK(e.expense_date, 1) as year_week'),
                DB::raw('SUM(e.amount) as total_weekly_expense')
            )
            ->where('e.group_id', $id)
            ->groupBy('u.name', DB::raw('YEARWEEK(e.expense_date, 1)'))
            ->orderBy('year_week')
            ->get();

        return view('admin.groups.user_weekly_expenses', compact('group', 'weeklyExpenses'));
    }

    // Monthly group expenses
    public function monthlyGroupExpenses()
    {
        $groups = Group::all();
        $data = [];

        foreach ($groups as $group) {
            $monthlyData = Expense::where('group_id', $group->id)
                ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $categoryData = Expense::where('group_id', $group->id)
                ->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->pluck('total', 'category')
                ->toArray();

            $data[$group->id] = [
                'group' => $group,
                'monthly' => $monthlyData,
                'categories' => $categoryData,
            ];
        }

        return view('admin.groups.monthly_analytics', compact('data'));
    }

    // Monthly analytics for a single group
    public function monthlyAnalytics($id)
    {
        $group = Group::findOrFail($id);

        $monthlyExpenses = DB::table('expenses as e')
            ->select(
                DB::raw('MONTH(e.expense_date) as month'),
                DB::raw('YEAR(e.expense_date) as year'),
                DB::raw('SUM(e.amount) as total_monthly_expense')
            )
            ->where('e.group_id', $id)
            ->groupBy(DB::raw('YEAR(e.expense_date)'), DB::raw('MONTH(e.expense_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.groups.monthly-analytics', compact('group', 'monthlyExpenses'));
    }

    // Show all users of a specific group
public function users($id)
{
    $group = Group::with('users')->findOrFail($id);
    return view('admin.groups.users', compact('group'));
}

}

// namespace App\Http\Controllers\admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Group;
// use App\Models\User;
// use App\Models\GroupMember;
// use App\Models\Expense;
// use App\Models\Split;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;

// class groupController extends Controller
// {
//     public function index()
//     {
//         $groups = Group::with('creator')->orderBy('budget', 'asc')->get();

//         if ($groups->isEmpty()) {
//             return redirect()->route('admin.groups.create')
//                              ->with('warning', 'No groups found. Please create one.');
//         }

//         return view('admin.groups.index', compact('groups'));
//     }

//     public function create()
//     {
//         $users = User::all();
//         return view('admin.groups.create', compact('users'));
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'required|string',
//             'created_by' => 'required|exists:users,id',
//             'budget' => 'nullable|numeric|min:0',
//             'permanent' => 'nullable|boolean',
//         ]);

//         $data = $request->all();
//         $data['permanent'] = $request->has('permanent') ? 1 : 0;

//         $group = Group::create($data);
//         $group->users()->attach($request->created_by);

//         return redirect()->route('admin.groups.index')->with('success', 'Group created successfully.');
//     }

//     public function edit($id)
//     {
//         $group = Group::findOrFail($id);
//         $users = User::all();
//         return view('admin.groups.edit', compact('group', 'users'));
//     }

//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'required|string',
//             'created_by' => 'required|exists:users,id',
//             'budget' => 'nullable|numeric|min:0',
//             'permanent' => 'nullable|boolean',
//         ]);

//         $data = $request->all();
//         $data['permanent'] = $request->has('permanent') ? 1 : 0;

//         $group = Group::findOrFail($id);
//         $group->update($data);

//         return redirect()->route('admin.groups.index')->with('success', 'Group updated successfully.');
//     }

//     public function destroy($id)
//     {
//         $group = Group::findOrFail($id);
//         $group->delete();

//         return redirect()->route('admin.groups.index')->with('success', 'Group deleted successfully.');
//     }


    
// public function weeklyAnalytics()
// {
//     $weeklyExpenses = DB::table('groups as g')
//         ->leftJoin('expenses as e', 'g.id', '=', 'e.group_id')
//         ->select(
//             'g.id as group_id',
//             'g.name as group_name',
//             DB::raw('YEARWEEK(e.expense_date, 1) as year_week'),
//             DB::raw('SUM(e.amount) as total_weekly_expense')
//         )
//         ->groupBy('g.id', 'g.name', DB::raw('YEARWEEK(e.expense_date, 1)'))
//         ->orderBy('g.id')
//         ->orderBy('year_week')
//         ->get();

//     return view('admin.groups.weekly_analytics', compact('weeklyExpenses'));
// }


//  public function analytics($id)
// {
//     $group = Group::findOrFail($id);

//     $weeklyExpenses = DB::table('expenses as e')
//         ->select(
//             DB::raw('YEARWEEK(e.expense_date, 1) as year_week'),
//             DB::raw('MIN(e.expense_date) as start_date'),
//             DB::raw('MAX(e.expense_date) as end_date'),
//             DB::raw('SUM(e.amount) as total_weekly_expense')
//         )
//         ->where('e.group_id', $id)
//         ->groupBy(DB::raw('YEARWEEK(e.expense_date, 1)'))
//         ->orderBy('year_week')
//         ->get();

//     return view('admin.groups.analytics', compact('group', 'weeklyExpenses'));
// }


// public function userWeeklyExpenses($id)
// {
//     $group = Group::findOrFail($id);

//     $weeklyExpenses = DB::table('expenses as e')
//         ->join('users as u', 'e.user_id', '=', 'u.id')
//         ->select(
//             'u.name as user_name',
//             DB::raw('YEARWEEK(e.expense_date, 1) as year_week'),
//             DB::raw('SUM(e.amount) as total_weekly_expense')
//         )
//         ->where('e.group_id', $id)
//         ->groupBy('u.name', DB::raw('YEARWEEK(e.expense_date, 1)'))
//         ->orderBy('year_week')
//         ->get();

//     return view('admin.groups.user_weekly_expenses', compact('group', 'weeklyExpenses'));
// }


//  public function monthlyGroupExpenses()
//     {
//         // Saare groups fetch karenge
//         $groups = Group::all();

//         $data = [];

//         foreach ($groups as $group) {
//             $monthlyData = Expense::where('group_id', $group->id)
//                 ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
//                 ->groupBy('month')
//                 ->pluck('total', 'month')
//                 ->toArray();

//             $categoryData = Expense::where('group_id', $group->id)
//                 ->selectRaw('category, SUM(amount) as total')
//                 ->groupBy('category')
//                 ->pluck('total', 'category')
//                 ->toArray();

//             $data[$group->id] = [
//                 'group' => $group,
//                 'monthly' => $monthlyData,
//                 'categories' => $categoryData,
//             ];
//         }

//         return view('admin.groups.monthly_analytics', compact('data'));
//     }

//     public function monthlyAnalytics($id)
// {
//     $group = Group::findOrFail($id);

//     $monthlyExpenses = DB::table('expenses as e')
//         ->select(
//             DB::raw('MONTH(e.expense_date) as month'),
//             DB::raw('YEAR(e.expense_date) as year'),
//             DB::raw('SUM(e.amount) as total_monthly_expense')
//         )
//         ->where('e.group_id', $id)
//         ->groupBy(DB::raw('YEAR(e.expense_date)'), DB::raw('MONTH(e.expense_date)'))
//         ->orderBy('year')
//         ->orderBy('month')
//         ->get();

//     return view('admin.groups.monthly-analytics', compact('group', 'monthlyExpenses'));
// }

// }
