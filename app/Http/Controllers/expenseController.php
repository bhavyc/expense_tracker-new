<?php



 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use App\Models\Group;

class expenseController extends Controller
{
     
    public function index()
    {
        $user = Auth::user();

        $expenses = Expense::where('user_id', $user->id)
            ->with('group')
            ->latest()
            ->get();

        
        $groups = $user->groups()->get();

        return view('user.expenses.index', compact('expenses', 'groups'));
    }

     
    public function create()
    {
        $userId = auth()->id();

        $groups = Group::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return view('user.expenses.create', compact('groups'));
    }

     
 

// public function store(Request $request)
// {
    
//     // Base validation
//     $rules = [
//         'description' => 'required|string|max:255',
//         'amount' => 'required|numeric|min:0',
//         'expense_date' => 'required|date',
//         'category' => 'required|string|max:100',
//         'group_id' => 'nullable|exists:groups,id',
//         'notes' => 'nullable|string|max:500',
//     ];  

//     // Only validate 'method' and 'splits' if group selected
//     if ($request->group_id) {
//         $rules['method'] = 'required|in:equal,unequal,percentage,shares,adjustment';
//         $rules['splits'] = 'nullable|array';
//     }

//     $request->validate($rules);

     
//     $expense = Expense::create([
//         'user_id' => Auth::id(),
//         'group_id' => $request->group_id,
//         'description' => $request->description,
//         'amount' => $request->amount,
//         'expense_date' => $request->expense_date,
//         'category' => $request->category,
//         'status' => 'pending',
//         'notes' => $request->notes,
//     ]);

     
//     if ($request->group_id) {
//         $group = Group::with('users')->find($request->group_id);
//         $members = $group ? $group->users : collect([Auth::user()]);

//         $method = $request->method;
//         $splitsInput = $request->splits ?? [];

//         switch ($method) {

//             case 'equal':
//                 $perUserAmount = round($request->amount / $members->count(), 2);
//                 foreach ($members as $member) {
//                     $type = $member->id == Auth::id() ? 'lent' : 'owed';
//                     $amount = $type === 'lent' ? $request->amount - $perUserAmount : $perUserAmount;

//                     \App\Models\Split::create([
//                         'user_id' => $member->id,
//                         'expense_id' => $expense->id,
//                         'amount' => $amount,
//                         'type' => $type,
//                         'method' => $method,
//                         'value' => null,
//                     ]);

//                     if ($type === 'lent') $member->lent_total += $amount;
//                     else $member->owed_total += $amount;
//                     $member->save();
//                 }
//                 break;
 
//             case 'unequal':
//                 $totalCustom = array_sum($splitsInput);
//                 if ($totalCustom != $request->amount) {
//                     return back()->withInput()->withErrors(['splits' => 'Sum of unequal splits must equal total amount.']);
//                 }
//                 foreach ($splitsInput as $userId => $amount) {
//                     $user = \App\Models\User::find($userId);
//                     if (!$user) continue;
//                     $type = $userId == Auth::id() ? 'lent' : 'owed';
//                     $splitAmount = $type === 'lent' ? $request->amount - $amount : $amount;

//                     \App\Models\Split::create([
//                         'user_id' => $userId,
//                         'expense_id' => $expense->id,
//                         'amount' => $splitAmount,
//                         'type' => $type,
//                         'method' => $method,
//                         'value' => $amount,
//                     ]);

//                     if ($type === 'lent') $user->lent_total += $splitAmount;
//                     else $user->owed_total += $splitAmount;
//                     $user->save();
//                 }
//                 break;

//             case 'percentage':
//                 foreach ($splitsInput as $userId => $percentage) {
//                     $user = \App\Models\User::find($userId);
//                     if (!$user) continue;
//                     $amount = round($request->amount * ($percentage / 100), 2);
//                     $type = $userId == Auth::id() ? 'lent' : 'owed';
//                     $splitAmount = $type === 'lent' ? $request->amount - $amount : $amount;

//                     \App\Models\Split::create([
//                         'user_id' => $userId,
//                         'expense_id' => $expense->id,
//                         'amount' => $splitAmount,
//                         'type' => $type,
//                         'method' => $method,
//                         'value' => $percentage,
//                     ]);

//                     if ($type === 'lent') $user->lent_total += $splitAmount;
//                     else $user->owed_total += $splitAmount;
//                     $user->save();
//                 }
//                 break;

//             case 'shares':
//                 $totalShares = array_sum($splitsInput);
//                 foreach ($splitsInput as $userId => $share) {
//                     $user = \App\Models\User::find($userId);
//                     if (!$user) continue;
//                     $amount = round($request->amount * ($share / $totalShares), 2);
//                     $type = $userId == Auth::id() ? 'lent' : 'owed';
//                     $splitAmount = $type === 'lent' ? $request->amount - $amount : $amount;

//                     \App\Models\Split::create([
//                         'user_id' => $userId,
//                         'expense_id' => $expense->id,
//                         'amount' => $splitAmount,
//                         'type' => $type,
//                         'method' => $method,
//                         'value' => $share,
//                     ]);

//                     if ($type === 'lent') $user->lent_total += $splitAmount;
//                     else $user->owed_total += $splitAmount;
//                     $user->save();
//                 }
//                 break;

//             case 'adjustment':
//                 foreach ($members as $member) {
//                     $user = \App\Models\User::find($member->id);
//                     if (!$user) continue;

//                     if ($member->id == Auth::id()) {
//                         $splitAmount = $request->amount;
//                         $type = 'lent';
//                     } else {
//                         $splitAmount = $splitsInput[$member->id] ?? 0;
//                         $type = 'owed';
//                     }

//                     if ($splitAmount > 0) {
//                         \App\Models\Split::create([
//                             'user_id' => $member->id,
//                             'expense_id' => $expense->id,
//                             'amount' => $splitAmount,
//                             'type' => $type,
//                             'method' => $method,
//                             'value' => $splitAmount,
//                         ]);

//                         if ($type === 'lent') $user->lent_total += $splitAmount;
//                         else $user->owed_total += $splitAmount;
//                         $user->save();
//                     }
//                 }
//                 break;
//         }
//     }

//     return redirect()->route('user.expenses.index')
//         ->with('success', 'Expense added and split successfully.');
// }


public function store(Request $request)
{
    // Base validation
    $rules = [
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'expense_date' => 'required|date',
        'category' => 'required|string|max:100',
        'group_id' => 'nullable|exists:groups,id',
        'notes' => 'nullable|string|max:500',
    ];  

    if ($request->group_id) {
        $rules['method'] = 'required|in:equal,unequal,percentage,shares,adjustment';
        $rules['splits'] = 'nullable|array';
    }

    $request->validate($rules);

    // Create Expense
    $expense = Expense::create([
        'user_id' => Auth::id(),
        'group_id' => $request->group_id,
        'description' => $request->description,
        'amount' => $request->amount,
        'expense_date' => $request->expense_date,
        'category' => $request->category,
        'status' => 'pending',
        'notes' => $request->notes,
    ]);

    // If group exists, handle splits and carry forward
    if ($request->group_id) {
        $group = Group::with('users')->find($request->group_id);

        // --- 1️⃣ Update carry_forward_balance if budget exceeded ---
        $totalSpent = $group->expenses()->sum('amount'); // total including this new expense
        $budgetLeft = $group->budget - $totalSpent;

        if ($budgetLeft < 0) {
            $group->carry_forward_balance = abs($budgetLeft); // set carry forward
        } else {
            $group->carry_forward_balance = 0;
        }
        $group->save();

        // --- 2️⃣ Handle splits ---
        $members = $group->users;
        $method = $request->method;
        $splitsInput = $request->splits ?? [];

        switch ($method) {
            case 'equal':
                $perUserAmount = round($request->amount / $members->count(), 2);
                foreach ($members as $member) {
                    $type = $member->id == Auth::id() ? 'lent' : 'owed';
                    $amount = $type === 'lent' ? $request->amount - $perUserAmount : $perUserAmount;

                    \App\Models\Split::create([
                        'user_id' => $member->id,
                        'expense_id' => $expense->id,
                        'amount' => $amount,
                        'type' => $type,
                        'method' => $method,
                        'value' => null,
                    ]);

                    if ($type === 'lent') $member->lent_total += $amount;
                    else $member->owed_total += $amount;
                    $member->save();
                }
                break;

            case 'unequal':
                $totalCustom = array_sum($splitsInput);
                if ($totalCustom != $request->amount) {
                    return back()->withInput()->withErrors(['splits' => 'Sum of unequal splits must equal total amount.']);
                }
                foreach ($splitsInput as $userId => $amount) {
                    $user = \App\Models\User::find($userId);
                    if (!$user) continue;
                    $type = $userId == Auth::id() ? 'lent' : 'owed';
                    $splitAmount = $type === 'lent' ? $request->amount - $amount : $amount;

                    \App\Models\Split::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'amount' => $splitAmount,
                        'type' => $type,
                        'method' => $method,
                        'value' => $amount,
                    ]);

                    if ($type === 'lent') $user->lent_total += $splitAmount;
                    else $user->owed_total += $splitAmount;
                    $user->save();
                }
                break;

            // --- Add other split methods (percentage, shares, adjustment) here as in your current code ---
        }
    }

    return redirect()->route('user.expenses.index')
        ->with('success', 'Expense added and split successfully.');
} 
    public function getBudgetLeft($groupId)
{

    $group = Group::find($groupId);
    if (!$group) {
        return response()->json(['budgetLeft' => 0]);
    }

    $totalSpent = $group->expenses()->sum('amount');
    $budgetLeft = max($group->budget - $totalSpent, 0);

    return response()->json(['budgetLeft' => $budgetLeft]);
}


public function checkBudget($groupId, $amount)
{
    $group = Group::find($groupId);
    if (!$group) {
        return response()->json(['error' => 'Group not found'], 404);
    }
 
    $totalSpent = $group->expenses()->sum('amount');

    $budget = $group->budget;
    $carryForward = $group->carry_forward_balance;

    
    $newTotal = $totalSpent + $amount;

    return response()->json([
        'budget' => $budget,
        'carry_forward' => $carryForward,
        'spent' => $totalSpent,
        'new_total' => $newTotal,
        'exceeded' => $newTotal > ($budget + $carryForward),
    ]);
}

}

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Expense;
// use App\Models\Group;
// use App\Models\GroupMember;
// class expenseController extends Controller
// {
    
//      public function index()
//     {
//         $user = Auth::user();
//         $expenses = Expense::where('user_id', $user->id)->with('group')->latest()->get();
//         $groups = $user->groups; // for group dropdown

//         return view('user.expenses.index', compact('expenses', 'groups'));
//     }

//     public function create()
// {
//     $userId = auth()->id();

    
//     $groups = \App\Models\Group::whereHas('members', function ($query) use ($userId) {
//         $query->where('user_id', $userId);
//     })->get();

//     return view('user.expenses.create', compact('groups'));
// }

//   public function store(Request $request)
// {
//     $request->validate([
//         'description' => 'required|string|max:255',
//         'amount' => 'required|numeric|min:0',
//         'expense_date' => 'required|date',
//         'category' => 'required|string|max:100',
//         'group_id' => 'nullable|exists:groups,id',
//     ]);

//     $expense = Expense::create([
//         'user_id' => Auth::id(),
//         'group_id' => $request->group_id,
//         'description' => $request->description,
//         'amount' => $request->amount,
//         'expense_date' => $request->expense_date,
//         'category' => $request->category,
//         'status' => 'pending',
//         'notes' => $request->notes,
//     ]);

//     if ($request->group_id) {
//         $group = Group::with('users')->find($request->group_id);
//         $members = $group->users;

//         $splitAmount = round($request->amount / $members->count());

//         foreach ($members as $member) {
//             if ((int)$member->id === (int)Auth::id()) {
//                 $member->lent_total += $request->amount - $splitAmount;

//                 \App\Models\Split::create([
//                     'user_id' => $member->id,
//                     'expense_id' => $expense->id,
//                     'amount' => $request->amount - $splitAmount,
//                     'type' => 'lent',
//                 ]);
//             } else {
//                 $member->owed_total += $splitAmount;

//                 \App\Models\Split::create([
//                     'user_id' => $member->id,
//                     'expense_id' => $expense->id,
//                     'amount' => $splitAmount,
//                     'type' => 'owed',
//                 ]);
//             }

//             $member->save();
//         }
//     }

//     return redirect()->route('user.expenses.create')->with('success', 'Expense added and split successfully.');
// }

// }



