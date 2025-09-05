<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

use App\Models\Split;
use App\Models\User;   
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

   public function store(Request $request)
{
    $rules = [
        'user_id' => 'required|exists:users,id',
        'group_id' => 'nullable|exists:groups,id',
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric|min:1',
        'expense_date' => 'required|date',
        'category' => 'required|string|max:100',
        'status' => 'required|in:pending,approved,rejected',
    ];

    if ($request->group_id) {
        $rules['method'] = 'required|in:equal,unequal,percentage,shares,adjustment';
        $rules['splits'] = 'required|array';
    }

    $request->validate($rules);

    DB::beginTransaction();

    try {
        $expense = Expense::create([
            'user_id' => $request->user_id,
            'group_id' => $request->group_id ?? null,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'status' => $request->status,
            'notes' => $request->notes ?? null,
        ]);

        // Handle group splits
        if ($request->group_id && $request->splits) {
            $totalAmount = $request->amount;
            $splitsInput = $request->splits;
            $method = $request->method;
            $userId = $request->user_id;
            $splits = [];

            $owedAmounts = [];
            $paidByLent = 0;

            switch ($method) {
                case 'equal':
                    $numUsers = count($splitsInput);
                    $perUser = round($totalAmount / $numUsers, 2);
                    foreach ($splitsInput as $uid => $val) {
                        if ($uid != $userId) $owedAmounts[$uid] = $perUser;
                    }
                    $paidByLent = $totalAmount - array_sum($owedAmounts);
                    break;

                case 'unequal':
                    if (array_sum($splitsInput) != $totalAmount) {
                        throw new \Exception("Unequal split sum must equal total amount");
                    }
                    foreach ($splitsInput as $uid => $val) {
                        if ($uid != $userId) $owedAmounts[$uid] = $val;
                    }
                    $paidByLent = $totalAmount - array_sum($owedAmounts);
                    break;

                case 'percentage':
                    foreach ($splitsInput as $uid => $perc) {
                        $amount = round($totalAmount * ($perc / 100), 2);
                        if ($uid != $userId) $owedAmounts[$uid] = $amount;
                    }
                    $paidByLent = $totalAmount - array_sum($owedAmounts);
                    break;

                case 'shares':
                    $totalShares = array_sum($splitsInput);
                    foreach ($splitsInput as $uid => $share) {
                        $amount = round($totalAmount * ($share / $totalShares), 2);
                        if ($uid != $userId) $owedAmounts[$uid] = $amount;
                    }
                    $paidByLent = $totalAmount - array_sum($owedAmounts);
                    break;

                case 'adjustment':
                    if (array_sum($splitsInput) != $totalAmount) {
                        throw new \Exception("Adjusted split sum must equal total amount");
                    }
                    foreach ($splitsInput as $uid => $val) {
                        if ($uid != $userId) $owedAmounts[$uid] = $val;
                    }
                    $paidByLent = $totalAmount - array_sum($owedAmounts);
                    break;
            }

            // Insert paid by user
            $splits[] = [
                'expense_id' => $expense->id,
                'user_id' => $userId,
                'amount' => $paidByLent,
                'type' => 'lent',
                'method' => $method,
                'value' => $method == 'percentage' ? $splitsInput[$userId] ?? null : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert owed by others
            foreach ($owedAmounts as $uid => $amount) {
                $splits[] = [
                    'expense_id' => $expense->id,
                    'user_id' => $uid,
                    'amount' => $amount,
                    'type' => 'owed',
                    'method' => $method,
                    'value' => $method == 'percentage' ? $splitsInput[$uid] : $splitsInput[$uid] ?? $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert into database
            Split::insert($splits);

            // Update lent_total and owed_total for users
            foreach ($splits as $s) {
                $u = User::find($s['user_id']);
                if (!$u) continue;
                if ($s['type'] === 'lent') $u->lent_total += $s['amount'];
                else $u->owed_total += $s['amount'];
                $u->save();
            }
        }

        // Update group carry forward
        if ($request->group_id) {
            $group = Group::find($request->group_id);
            if ($group) {
                $totalExpenses = $group->expenses()->where('status', '!=', 'rejected')->sum('amount');
                $group->carry_forward_balance = max($totalExpenses - $group->budget, 0);
                $group->save();
            }
        }

        DB::commit();
        return redirect()->route('user.expenses.index')->with('success', 'Expense created successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
    }
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


// public function checkBudget($groupId, $amount)
// {
//     $group = Group::find($groupId);
//     if (!$group) {
//         return response()->json(['error' => 'Group not found'], 404);
//     }
 
//     $totalSpent = $group->expenses()->sum('amount');

//     $budget = $group->budget;
//     $carryForward = $group->carry_forward_balance;

    
//     $newTotal = $totalSpent + $amount;

//     return response()->json([
//         'budget' => $budget,
//         'carry_forward' => $carryForward,
//         'spent' => $totalSpent,
//         'new_total' => $newTotal,
//         'exceeded' => $newTotal > ($budget + $carryForward),
//     ]);
// }


public function checkBudget($groupId, $amount)
{
    $group = Group::find($groupId);
    if (!$group) {
        return response()->json(['error' => 'Group not found'], 404);
    }

    $totalSpent = $group->expenses()->sum('amount');

    $budget = (float) $group->budget ?? 0;
    $carryForward = (float) $group->carry_forward_balance ?? 0;
    $newTotal = (float) $totalSpent + (float) $amount;

    return response()->json([
        'budget' => $budget,
        'carry_forward' => $carryForward,
        'spent' => (float) $totalSpent,
        'new_total' => $newTotal,
        'exceeded' => $newTotal > ($budget + $carryForward),
    ]);
}

public function checkPersonalBudget($amount)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Sum of personal  expenses
    $totalSpent = Expense::where('user_id', $user->id)
        ->whereNull('group_id')
        ->sum('amount');

    $budget = $user->personal_budget ?? 0;
    $newTotal = $totalSpent + $amount;

    return response()->json([
        'budget' => $budget,
        'spent' => $totalSpent,
        'new_total' => $newTotal,
        'budgetLeft' => max($budget - $newTotal, 0),
        'exceeded' => $newTotal > $budget,
    ]);
}


}
 
 
 