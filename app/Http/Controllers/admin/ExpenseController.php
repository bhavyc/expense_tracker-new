<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\User;
use App\Models\Group;
use App\Models\Split;
use App\Models\GroupMember;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
     
    function index()
    {
       $expenses = Expense::with(['user', 'group', 'splits.user'])->paginate(10);

        return view('admin.expenses.index', compact('expenses'));
    }

    function create()
    { 
         
        $users = User::where('role', '!=', 'admin')->get();
        $groups= Group::all();
        return view('admin.expenses.create', compact('users', 'groups'));
         
    }


//     function store(Request $request)
//     {
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

//        $expense = Expense::create($request->all());
//         if ($request->group_id) {
//     $group = Group::with('users')->find($request->group_id);
// $members = $group->users;

// $split_amount = round($request->amount / $members->count());

// foreach($members as $member){
    
//     if((int)$member->id === (int)$request->user_id){
       
//         $member->lent_total += $request->amount - $split_amount;

//         Split::create([
//             'user_id' => $member->id,
//             'expense_id' => $expense->id,
//             'amount' => $request->amount - $split_amount,
//             'type' => 'lent',
//         ]);
//     } else {
        
//         $member->owed_total += $split_amount;

//         Split::create([
//             'user_id' => $member->id,
//             'expense_id' => $expense->id,
//             'amount' => $split_amount,
//             'type' => 'owed',
//         ]);
//     }

     
//     $member->save();
// }

            
//         }

//         return redirect()->route('admin.expenses.index')->with('success', 'Expense created successfully.');
//     }


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'nullable|exists:groups,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'category' => 'required|string|max:100',
            'status' => 'required|in:pending,approved,rejected',
            'method' => 'required|in:equal,unequal,percentage,shares,adjustment',
            'splits' => 'required|array', // user_id => value
        ]);

        DB::beginTransaction();
        try {
            // 1️⃣ Create Expense
            $expense = Expense::create([
                'user_id' => $request->user_id,
                'group_id' => $request->group_id,
                'description' => $request->description,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'category' => $request->category,
                'status' => $request->status,
                'notes' => $request->notes ?? null,
            ]);

            $totalAmount = $request->amount;
            $splitsInput = $request->splits;
            $method = $request->method;

            $splits = [];

            // 2️⃣ Generate Splits
            if ($method == 'equal') {
                $userCount = count($splitsInput);
                $perUserAmount = round($totalAmount / $userCount, 2);
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
            } elseif ($method == 'unequal') {
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
            } elseif ($method == 'percentage') {
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
            } elseif ($method == 'shares') {
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
            } elseif ($method == 'adjustment') {
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

            // 3️⃣ Insert Splits
            Split::insert($splits);

            // 4️⃣ Update lent_total and owed_total for each user
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

            DB::commit();

            // 5️⃣ Redirect to expense list
            return redirect()->route('admin.expenses.index')
                             ->with('success', 'Expense created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->withErrors(['error' => $e->getMessage()]);
        }
    }


public function getUsersByGroup($id)
{
    $group = Group::with('users')->findOrFail($id);
    return response()->json($group->users);
}

// public function getUserGroups($userId)
// {
//     $groups = Group::whereHas('members', function($q) use ($userId) {
//         $q->where('user_id', $userId);
//     })->get();

//     return response()->json($groups);
// }


     function edit($id){
        $expense = Expense::findOrFail($id);
       $users = User::where('role', '!=', 'admin')->get();
        $groups = Group::all();
        return view('admin.expenses.edit', compact('expense', 'users', 'groups'));
     }

     function update(Request $request, $id)
     {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'nullable|exists:groups,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'nullable|string|max:100',
            'status' => 'in:pending,approved',
            'notes' => 'nullable|string|max:500',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update($request->all());

        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated successfully.');
     }  

   function destroy($id)
{
    $expense = Expense::with('splits.user')->findOrFail($id);

    
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
    $expense->delete();  

    return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully.');
}

//    public function getGroupsByUser($id)
//     {
//         $user = User::with('groups')->findOrFail($id);
//         return response()->json($user->groups);
//     }

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



 

 