<?php

 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    // ✅ Get all expenses for authenticated user
    public function index()
    {
        $expenses = Expense::where('user_id', auth()->id())->get();

        return response()->json([
            'status' => true,
            'message' => 'Expenses fetched successfully.',
            'data' => $expenses
        ]);
    }

    // ✅ Store new expense
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'group_id' => 'required|exists:groups,id',
    //         'description' => 'required|string',
    //         'amount' => 'required|numeric',
    //         'expense_date' => 'required|date',
    //         'category' => 'nullable|string',
    //         'status' => 'nullable|string',
    //         'notes' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     $expense = Expense::create([
    //         'user_id' => auth()->id(),
    //         'group_id' => $request->group_id,
    //         'description' => $request->description,
    //         'amount' => $request->amount,
    //         'expense_date' => $request->expense_date,
    //         'category' => $request->category,
    //         'status' => $request->status ?? 'pending',
    //         'notes' => $request->notes,
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Expense created successfully.',
    //         'data' => $expense
    //     ], 201);
    // }

   

public function store(Request $request)
{
    $request->validate([
        'group_id' => 'required|exists:groups,id',
        'description' => 'required|string',
        'amount' => 'required|numeric|min:0',
        'expense_date' => 'required|date',
        'category' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        // Create the expense
        $expense = Expense::create([
            'user_id' => auth()->id(),
            'group_id' => $request->group_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'status' => 'active', // or default
            'notes' => $request->notes,
        ]);

        // Get group members (including creator)
        $groupMembers = DB::table('group_user')
            ->where('group_id', $request->group_id)
            ->pluck('user_id')
            ->toArray();

        // Split amount
        $splitAmount = round($request->amount / count($groupMembers), 2);

        foreach ($groupMembers as $memberId) {
            $type = $memberId == auth()->id() ? 'lent' : 'owed';

            // Insert split
            DB::table('splits')->insert([
                'expense_id' => $expense->id,
                'user_id' => $memberId,
                'amount' => $splitAmount,
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update totals
            if ($type === 'lent') {
                DB::table('users')->where('id', $memberId)->increment('lent_total', $splitAmount);
            } else {
                DB::table('users')->where('id', $memberId)->increment('owed_total', $splitAmount);
            }
        }

        DB::commit();

        return response()->json(['message' => 'Expense created and split successfully.'], 201);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['error' => 'Failed to create expense.'], 500);
    }
}

    public function show($id)
    {
        $expense = Expense::where('user_id', auth()->id())->find($id);

        if (!$expense) {
            return response()->json(['status' => false, 'message' => 'Expense not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $expense
        ]);
    }

    // ✅ Update expense
    public function update(Request $request, $id)
    {
        $expense = Expense::where('user_id', auth()->id())->find($id);

        if (!$expense) {
            return response()->json(['status' => false, 'message' => 'Expense not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'group_id' => 'sometimes|exists:groups,id',
            'description' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'expense_date' => 'sometimes|date',
            'category' => 'nullable|string',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $expense->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Expense updated successfully.',
            'data' => $expense
        ]);
    }

    // ✅ Delete expense
    public function destroy($id)
    {
        $expense = Expense::where('user_id', auth()->id())->find($id);

        if (!$expense) {
            return response()->json(['status' => false, 'message' => 'Expense not found.'], 404);
        }

        $expense->delete();

        return response()->json([
            'status' => true,
            'message' => 'Expense deleted successfully.'
        ]);
    }
}
