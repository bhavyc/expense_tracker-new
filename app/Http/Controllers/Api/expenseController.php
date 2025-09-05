<?php
 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Split;
use App\Models\User;

class ExpenseController extends Controller
{
    // ✅ List all expenses for authenticated user
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

    // ✅ Create expense
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
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
                'user_id' => $user->id,
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
                $this->handleSplits($expense, $request->splits, $request->method, $user->id);
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

            return response()->json([
                'success' => true,
                'message' => 'Expense created successfully',
                'expense' => $expense,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ Handle group splits
    protected function handleSplits($expense, $splitsInput, $method, $userId)
    {
        $totalAmount = $expense->amount;
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
            case 'adjustment':
                if (array_sum($splitsInput) != $totalAmount) {
                    throw new \Exception("Split sum must equal total amount");
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
        }

        // Paid by user
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

        // Owed by others
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

        Split::insert($splits);

        // Update user totals
        foreach ($splits as $s) {
            $u = User::find($s['user_id']);
            if (!$u) continue;
            if ($s['type'] === 'lent') $u->lent_total += $s['amount'];
            else $u->owed_total += $s['amount'];
            $u->save();
        }
    }

    // ✅ Get budget left for a group
    public function getBudgetLeft($groupId)
    {
        $group = Group::find($groupId);
        if (!$group) return response()->json(['budgetLeft' => 0]);

        $totalSpent = $group->expenses()->sum('amount');
        $budgetLeft = max($group->budget - $totalSpent, 0);

        return response()->json(['budgetLeft' => $budgetLeft]);
    }

    // ✅ Check group budget before adding expense
    public function checkBudget($groupId, $amount)
    {
        $group = Group::find($groupId);
        if (!$group) return response()->json(['error' => 'Group not found'], 404);

        $totalSpent = $group->expenses()->sum('amount');

        $budget = (float) ($group->budget ?? 0);
        $carryForward = (float) ($group->carry_forward_balance ?? 0);
        $newTotal = (float) $totalSpent + (float) $amount;

        return response()->json([
            'budget' => $budget,
            'carry_forward' => $carryForward,
            'spent' => (float) $totalSpent,
            'new_total' => $newTotal,
            'exceeded' => $newTotal > ($budget + $carryForward),
        ]);
    }

    // ✅ Check personal budget before adding personal expense
    public function checkPersonalBudget($amount)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

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
