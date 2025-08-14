<?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Models\Expense;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class ExpenseController extends Controller
// {
//     // List all expenses for logged-in user
//     public function index()
//     {
//         $expenses = Expense::where('user_id', Auth::id())->get();
//         return response()->json($expenses);
//     }

//     // Store a new expense
//     public function store(Request $request)
//     {
//         $request->validate([
//             'group_id' => 'required|exists:groups,id',
//             'description' => 'nullable|string',
//             'amount' => 'required|numeric|min:0',
//             'expense_date' => 'required|date',
//             'category' => 'nullable|string',
//             'status' => 'nullable|string',
//             'notes' => 'nullable|string',
//         ]);

//         $expense = Expense::create([
//             'user_id' => Auth::id(),
//             'group_id' => $request->group_id,
//             'description' => $request->description,
//             'amount' => $request->amount,
//             'expense_date' => $request->expense_date,
//             'category' => $request->category,
//             'status' => $request->status,
//             'notes' => $request->notes,
//         ]);

//         return response()->json($expense, 201);
//     }

//     // Show single expense
//     public function show($id)
//     {
//         $expense = Expense::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
//         return response()->json($expense);
//     }

//     // Update expense
//     public function update(Request $request, $id)
//     {
//         $expense = Expense::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

//         $request->validate([
//             'group_id' => 'sometimes|exists:groups,id',
//             'description' => 'nullable|string',
//             'amount' => 'sometimes|numeric|min:0',
//             'expense_date' => 'sometimes|date',
//             'category' => 'nullable|string',
//             'status' => 'nullable|string',
//             'notes' => 'nullable|string',
//         ]);

//         $expense->update($request->only([
//             'group_id', 'description', 'amount', 'expense_date', 'category', 'status', 'notes'
//         ]));

//         return response()->json($expense);
//     }

//     // Delete expense
//     public function destroy($id)
//     {
//         $expense = Expense::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
//         $expense->delete();
//         return response()->json(null, 204);
//     }
// }
 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use App\Models\Group;

class ExpenseController extends Controller
{
    // User ke expenses list karo
    public function index()
    {
        $user = Auth::user();

        $expenses = Expense::where('user_id', $user->id)
            ->with('group')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'expenses' => $expenses,
        ]);
    }

    // Groups jisme user member hai (dropdown ke liye)
    public function groups()
    {
        $user = Auth::user();
        $groups = $user->groups()->get();

        return response()->json([
            'success' => true,
            'groups' => $groups,
        ]);
    }

    // Naya expense create karo
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string|max:100',
            'group_id' => 'nullable|exists:groups,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Budget check agar group select hua ho
        if ($request->group_id) {
            $group = Group::with('expenses')->findOrFail($request->group_id);
            $totalSpent = $group->expenses()->sum('amount');
            $budgetLeft = max($group->budget - $totalSpent, 0);

            if ($request->amount > $budgetLeft) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense amount exceeds the remaining group budget.'
                ], 422);
            }
        }

        // Expense create karo
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

        // Agar group expense hai to split logic
        if ($request->group_id) {
            $group = Group::with('users')->find($request->group_id);
            $members = $group->users;

            $splitAmount = round($request->amount / $members->count(), 2);

            foreach ($members as $member) {
                if ($member->id == Auth::id()) {
                    $member->lent_total += $request->amount - $splitAmount;

                    \App\Models\Split::create([
                        'user_id' => $member->id,
                        'expense_id' => $expense->id,
                        'amount' => $request->amount - $splitAmount,
                        'type' => 'lent',
                    ]);
                } else {
                    $member->owed_total += $splitAmount;

                    \App\Models\Split::create([
                        'user_id' => $member->id,
                        'expense_id' => $expense->id,
                        'amount' => $splitAmount,
                        'type' => 'owed',
                    ]);
                }
                $member->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense added and split successfully.',
            'expense' => $expense,
        ]);
    }

    // Specific group ka budget remaining lao
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
}
