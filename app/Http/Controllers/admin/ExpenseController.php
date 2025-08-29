<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Expense;
// use App\Models\User;
// use App\Models\Group;
// use App\Models\Split;
// use App\Models\GroupMember;
// use Illuminate\Support\Facades\DB;

// class ExpenseController extends Controller
// {
     
//     function index()
//     {
//        $expenses = Expense::with(['user', 'group', 'splits.user'])->paginate(10);

//         return view('admin.expenses.index', compact('expenses'));
//     }

//     function create()
//     { 
         
//         $users = User::where('role', '!=', 'admin')->get();
//         $groups= Group::all();
//         return view('admin.expenses.create', compact('users', 'groups'));
         
//     }

 

//    public function store(Request $request)
// {
     
//     $rules = [
//         'user_id' => 'required|exists:users,id',
//         'group_id' => 'nullable|exists:groups,id',
//         'description' => 'required|string|max:255',
//         'amount' => 'required|numeric|min:1',
//         'expense_date' => 'required|date',
//         'category' => 'required|string|max:100',
//         'status' => 'required|in:pending,approved,rejected',
//     ];

     
//     if ($request->group_id) {
//         $rules['method'] = 'required|in:equal,unequal,percentage,shares,adjustment';
//         $rules['splits'] = 'required|array';
//     }

//     $request->validate($rules);

//     DB::beginTransaction();

//     try {
         
//         $expense = Expense::create([
//             'user_id' => $request->user_id,
//             'group_id' => $request->group_id ?? null,
//             'description' => $request->description,
//             'amount' => $request->amount,
//             'expense_date' => $request->expense_date,
//             'category' => $request->category,
//             'status' => $request->status,
//             'notes' => $request->notes ?? null,
//         ]);

         
//         if ($request->group_id && $request->splits) {
//             $totalAmount = $request->amount;
//             $splitsInput = $request->splits;
//             $method = $request->method;
//             $splits = [];

//             if ($method === 'equal') {
//                 $perUserAmount = round($totalAmount / count($splitsInput), 2);
//                 foreach ($splitsInput as $user_id => $val) {
//                     $splits[] = [
//                         'expense_id' => $expense->id,
//                         'user_id' => $user_id,
//                         'amount' => $perUserAmount,
//                         'type' => $user_id == $request->user_id ? 'lent' : 'owed',
//                         'method' => $method,
//                         'value' => $perUserAmount,
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ];
//                 }
//             } elseif ($method === 'unequal') {
//                 $sum = array_sum($splitsInput);
//                 if ($sum != $totalAmount) {
//                     throw new \Exception("Unequal split sum must equal total amount");
//                 }
//                 foreach ($splitsInput as $user_id => $amount) {
//                     $splits[] = [
//                         'expense_id' => $expense->id,
//                         'user_id' => $user_id,
//                         'amount' => $amount,
//                         'type' => $user_id == $request->user_id ? 'lent' : 'owed',
//                         'method' => $method,
//                         'value' => $amount,
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ];
//                 }
//             } elseif ($method === 'percentage') {
//                 foreach ($splitsInput as $user_id => $percentage) {
//                     $amount = round($totalAmount * ($percentage / 100), 2);
//                     $splits[] = [
//                         'expense_id' => $expense->id,
//                         'user_id' => $user_id,
//                         'amount' => $amount,
//                         'type' => $user_id == $request->user_id ? 'lent' : 'owed',
//                         'method' => $method,
//                         'value' => $percentage,
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ];
//                 }
//             } elseif ($method === 'shares') {
//                 $totalShares = array_sum($splitsInput);
//                 foreach ($splitsInput as $user_id => $share) {
//                     $amount = round($totalAmount * ($share / $totalShares), 2);
//                     $splits[] = [
//                         'expense_id' => $expense->id,
//                         'user_id' => $user_id,
//                         'amount' => $amount,
//                         'type' => $user_id == $request->user_id ? 'lent' : 'owed',
//                         'method' => $method,
//                         'value' => $share,
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ];
//                 }
//             } elseif ($method === 'adjustment') {
//                 $sum = array_sum($splitsInput);
//                 if ($sum != $totalAmount) {
//                     throw new \Exception("Adjusted split sum must equal total amount");
//                 }
//                 foreach ($splitsInput as $user_id => $amount) {
//                     $splits[] = [
//                         'expense_id' => $expense->id,
//                         'user_id' => $user_id,
//                         'amount' => $amount,
//                         'type' => $user_id == $request->user_id ? 'lent' : 'owed',
//                         'method' => $method,
//                         'value' => $amount,
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ];
//                 }
//             }

             
//             Split::insert($splits);
 
//             foreach ($splits as $split) {
//                 $user = User::find($split['user_id']);
//                 if (!$user) continue;

//                 if ($split['type'] === 'lent') {
//                     $user->lent_total += $split['amount'];
//                 } elseif ($split['type'] === 'owed') {
//                     $user->owed_total += $split['amount'];
//                 }
//                 $user->save();
//             }
//         }

//         DB::commit();

//         return redirect()->route('admin.expenses.index')
//                          ->with('success', 'Expense created successfully!');

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()
//                          ->withInput()
//                          ->withErrors(['error' => $e->getMessage()]);
//     }
// }

// public function getUsersByGroup($id)
// {
//     $group = Group::with('users')->findOrFail($id);
//     return response()->json($group->users);
// }
 
//      function edit($id){
//         $expense = Expense::findOrFail($id);
//        $users = User::where('role', '!=', 'admin')->get();
//         $groups = Group::all();
//         return view('admin.expenses.edit', compact('expense', 'users', 'groups'));
//      }

//      function update(Request $request, $id)
//      {
//         $request->validate([
//             'user_id' => 'required|exists:users,id',
//             'group_id' => 'nullable|exists:groups,id',
//             'description' => 'required|string|max:255',
//             'amount' => 'required|numeric|min:0',
//             'expense_date' => 'required|date',
//             'category' => 'nullable|string|max:100',
//             'status' => 'in:pending,approved',
//             'notes' => 'nullable|string|max:500',
//         ]);

//         $expense = Expense::findOrFail($id);
//         $expense->update($request->all());

//         return redirect()->route('admin.expenses.index')->with('success', 'Expense updated successfully.');
//      }  

// //    function destroy($id)
// // {
// //     $expense = Expense::with('splits.user')->findOrFail($id);

    
// //     foreach ($expense->splits as $split) {
// //         $user = $split->user;
// //         if ($split->type == 'lent') {
// //             $user->lent_total -= $split->amount;
// //         } elseif ($split->type == 'owed') {
// //             $user->owed_total -= $split->amount;
// //         }
// //         $user->save();
// //     }

// //     $expense->splits()->delete();  
// //     $expense->delete();  

// //     return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully.');
// // }
// function destroy($id)
// {
//     $expense = Expense::with('splits.user', 'group')->findOrFail($id); // load group too

//     foreach ($expense->splits as $split) {
//         $user = $split->user;
//         if ($split->type == 'lent') {
//             $user->lent_total -= $split->amount;
//         } elseif ($split->type == 'owed') {
//             $user->owed_total -= $split->amount;
//         }
//         $user->save();
//     }

//     $expense->splits()->delete();  

//     // Store group before deleting expense
//     $group = $expense->group;

//     $expense->delete();  

//     // Recalculate carry forward balance
//     if ($group) {
//         $totalExpenses = $group->expenses()->sum('amount');
//         $carryForward = $totalExpenses - $group->budget;
//         $group->carry_forward_balance = $carryForward > 0 ? $carryForward : 0;
//         $group->save();
//     }

//     return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully.');
// }

// //    public function getGroupsByUser($id)
// //     {
// //         $user = User::with('groups')->findOrFail($id);
// //         return response()->json($user->groups);
// //     }

// public function getGroupsByUser($id)
// {
//     $user = User::with('groups.expenses')->findOrFail($id);

//     $groups = $user->groups->map(function($group){
//         $spent = $group->expenses()->where('status', '!=', 'rejected')->sum('amount');
//         return [
//             'id' => $group->id,
//             'name' => $group->name,
//             'budget' => $group->budget,
//             'budget_left' => $group->budget - $spent,
//         ];
//     });

//     return response()->json($groups);
// }

    
// }


 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\User;
use App\Models\Group;
use App\Models\Split;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    // List all expenses
    function index()
    {
        $expenses = Expense::with(['user', 'group', 'splits.user'])->paginate(10);
        return view('admin.expenses.index', compact('expenses'));
    }

    // Show create form
    function create()
    {
        $users = User::where('role', '!=', 'admin')->get();
        $groups = Group::all();
        return view('admin.expenses.create', compact('users', 'groups'));
    }

    // Store new expense
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

            // Handle splits for group expenses
            if ($request->group_id && $request->splits) {
                $totalAmount = $request->amount;
                $splitsInput = $request->splits;
                $method = $request->method;
                $splits = [];

                // Generate splits
                if ($method === 'equal') {
                    $perUserAmount = round($totalAmount / count($splitsInput), 2);
                    foreach ($splitsInput as $user_id => $val) {
                        $splits[] = [
                            'expense_id' => $expense->id,
                            'user_id' => $user_id,
                            'amount' => $perUserAmount,
                            'type' => $user_id == $request->user_id ? 'lent' : 'owed',
                            'method' => $method,
                            'value' => $perUserAmount,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } elseif ($method === 'unequal') {
                    $sum = array_sum($splitsInput);
                    if ($sum != $totalAmount) {
                        throw new \Exception("Unequal split sum must equal total amount");
                    }
                    foreach ($splitsInput as $user_id => $amount) {
                        $splits[] = [
                            'expense_id' => $expense->id,
                            'user_id' => $user_id,
                            'amount' => $amount,
                            'type' => $user_id == $request->user_id ? 'lent' : 'owed',
                            'method' => $method,
                            'value' => $amount,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } elseif ($method === 'percentage') {
                    foreach ($splitsInput as $user_id => $percentage) {
                        $amount = round($totalAmount * ($percentage / 100), 2);
                        $splits[] = [
                            'expense_id' => $expense->id,
                            'user_id' => $user_id,
                            'amount' => $amount,
                            'type' => $user_id == $request->user_id ? 'lent' : 'owed',
                            'method' => $method,
                            'value' => $percentage,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } elseif ($method === 'shares') {
                    $totalShares = array_sum($splitsInput);
                    foreach ($splitsInput as $user_id => $share) {
                        $amount = round($totalAmount * ($share / $totalShares), 2);
                        $splits[] = [
                            'expense_id' => $expense->id,
                            'user_id' => $user_id,
                            'amount' => $amount,
                            'type' => $user_id == $request->user_id ? 'lent' : 'owed',
                            'method' => $method,
                            'value' => $share,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } elseif ($method === 'adjustment') {
                    $sum = array_sum($splitsInput);
                    if ($sum != $totalAmount) {
                        throw new \Exception("Adjusted split sum must equal total amount");
                    }
                    foreach ($splitsInput as $user_id => $amount) {
                        $splits[] = [
                            'expense_id' => $expense->id,
                            'user_id' => $user_id,
                            'amount' => $amount,
                            'type' => $user_id == $request->user_id ? 'lent' : 'owed',
                            'method' => $method,
                            'value' => $amount,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                Split::insert($splits);

                foreach ($splits as $split) {
                    $user = User::find($split['user_id']);
                    if (!$user) continue;

                    if ($split['type'] === 'lent') {
                        $user->lent_total += $split['amount'];
                    } elseif ($split['type'] === 'owed') {
                        $user->owed_total += $split['amount'];
                    }
                    $user->save();
                }
            }

            // Update group carry forward
            if ($request->group_id) {
                $group = Group::find($request->group_id);
                if ($group) {
                    $totalExpenses = $group->expenses()->where('status', '!=', 'rejected')->sum('amount');
                    $carryForward = $totalExpenses - $group->budget;
                    $group->carry_forward_balance = $carryForward > 0 ? $carryForward : 0;
                    $group->save();
                }
            }

            DB::commit();

            return redirect()->route('admin.expenses.index')
                             ->with('success', 'Expense created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Show edit form
    function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $users = User::where('role', '!=', 'admin')->get();
        $groups = Group::all();
        return view('admin.expenses.edit', compact('expense', 'users', 'groups'));
    }

    // Update expense
    function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'nullable|exists:groups,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'nullable|string|max:100',
            'status' => 'in:pending,approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update($request->all());

        // Recalculate group carry forward if expense belongs to a group
        if ($expense->group_id) {
            $group = Group::find($expense->group_id);
            if ($group) {
                $totalExpenses = $group->expenses()->where('status', '!=', 'rejected')->sum('amount');
                $carryForward = $totalExpenses - $group->budget;
                $group->carry_forward_balance = $carryForward > 0 ? $carryForward : 0;
                $group->save();
            }
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated successfully.');
    }

    // Delete expense
    function destroy($id)
    {
        $expense = Expense::with('splits.user', 'group')->findOrFail($id);

        foreach ($expense->splits as $split) {
            $user = $split->user;
            if ($split->type == 'lent') {
                $user->lent_total -= $split->amount;
            } elseif ($split->type == 'owed') {
                $user->owed_total -= $split->amount;
            }
            $user->save();
        }

        $expense->splits()->delete();

        $group = $expense->group;
        $expense->delete();

        // Recalculate carry forward
        if ($group) {
            $totalExpenses = $group->expenses()->where('status', '!=', 'rejected')->sum('amount');
            $carryForward = $totalExpenses - $group->budget;
            $group->carry_forward_balance = $carryForward > 0 ? $carryForward : 0;
            $group->save();
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully.');
    }

    // Get users by group (AJAX)
    public function getUsersByGroup($id)
    {
        $group = Group::with('users')->findOrFail($id);
        return response()->json($group->users);
    }

    // Get groups by user (AJAX)
    public function getGroupsByUser($id)
    {
        $user = User::with('groups.expenses')->findOrFail($id);

        $groups = $user->groups->map(function($group){
            $spent = $group->expenses()->where('status', '!=', 'rejected')->sum('amount');
            return [
                'id' => $group->id,
                'name' => $group->name,
                'budget' => $group->budget,
                'budget_left' => $group->budget - $spent,
            ];
        });

        return response()->json($groups);
    }
}

 

 