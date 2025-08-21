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
    //     $request->validate([
    //         'description' => 'required|string|max:255',
    //         'amount' => 'required|numeric|min:0',
    //         'expense_date' => 'required|date',
    //         'category' => 'required|string|max:100',
    //         'group_id' => 'nullable|exists:groups,id',
    //         'notes' => 'nullable|string|max:500',
    //     ]);

         
    //     if ($request->group_id) {
    //         $group = Group::with('expenses')->findOrFail($request->group_id);

    //         $totalSpent = $group->expenses()->sum('amount');
    //         $budgetLeft = max($group->budget - $totalSpent, 0);

    //         if ($request->amount > $budgetLeft) {
    //             return back()
    //                 ->withErrors(['amount' => 'Expense amount exceeds the remaining group budget.'])
    //                 ->withInput();
    //         }
    //     }

         
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

    //         $splitAmount = round($request->amount / $members->count(), 2);

    //         foreach ($members as $member) {
    //             if ($member->id == Auth::id()) {
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

    //     return redirect()->route('user.expenses.index')->with('success', 'Expense added and split successfully.');
    // }

 


  public function store(Request $request)
{
    $request->validate([
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'expense_date' => 'required|date',
        'category' => 'required|string|max:100',
        'group_id' => 'nullable|exists:groups,id',
        'method' => 'required|in:equal,unequal,percentage,shares,adjustment',
        'splits' => 'nullable|array'
    ]);

    // 1️⃣ Create Expense
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

    // 2️⃣ Fetch Group Members
    $group = Group::with('users')->find($request->group_id);
    $members = $group ? $group->users : collect([Auth::user()]);

    // 3️⃣ Split Logic
    switch ($request->method) {

        case 'equal':
            $splitAmount = round($request->amount / $members->count(), 2);

            foreach ($members as $member) {
                if ($member->id == Auth::id()) {
                    $creatorLent = $request->amount - $splitAmount;

                    $split = \App\Models\Split::create([
                        'user_id' => $member->id,
                        'expense_id' => $expense->id,
                        'amount' => $creatorLent,
                        'type' => 'lent',
                        'method' => 'equal',
                        'value' => null
                    ]);

                    $member->lent_total += $split->amount;
                    $member->save();
                } else {
                    $split = \App\Models\Split::create([
                        'user_id' => $member->id,
                        'expense_id' => $expense->id,
                        'amount' => $splitAmount,
                        'type' => 'owed',
                        'method' => 'equal',
                        'value' => null
                    ]);

                    $member->owed_total += $split->amount;
                    $member->save();
                }
            }
            break;

        case 'unequal':
            $creatorShare = $request->splits[Auth::id()] ?? 0;
            $creatorLent = $request->amount - $creatorShare;

            foreach ($request->splits as $userId => $customAmount) {
                $user = \App\Models\User::find($userId);
                if (!$user) continue;

                if ($userId == Auth::id()) {
                    $split = \App\Models\Split::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'amount' => $creatorLent,
                        'type' => 'lent',
                        'method' => 'unequal',
                        'value' => $customAmount
                    ]);

                    $user->lent_total += $split->amount;
                    $user->save();
                } else {
                    $split = \App\Models\Split::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'amount' => $customAmount,
                        'type' => 'owed',
                        'method' => 'unequal',
                        'value' => $customAmount
                    ]);

                    $user->owed_total += $split->amount;
                    $user->save();
                }
            }
            break;

        case 'percentage':
            $creatorPercentage = $request->splits[Auth::id()] ?? 0;
            $creatorShare = round(($request->amount * $creatorPercentage) / 100, 2);
            $creatorLent = $request->amount - $creatorShare;

            foreach ($request->splits as $userId => $percentage) {
                $user = \App\Models\User::find($userId);
                if (!$user) continue;

                $amount = round(($request->amount * $percentage) / 100, 2);

                if ($userId == Auth::id()) {
                    $split = \App\Models\Split::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'amount' => $creatorLent,
                        'type' => 'lent',
                        'method' => 'percentage',
                        'value' => $percentage
                    ]);

                    $user->lent_total += $split->amount;
                    $user->save();
                } else {
                    $split = \App\Models\Split::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'amount' => $amount,
                        'type' => 'owed',
                        'method' => 'percentage',
                        'value' => $percentage
                    ]);

                    $user->owed_total += $split->amount;
                    $user->save();
                }
            }
            break;

        case 'shares':
            $totalShares = array_sum($request->splits);
            $creatorShare = round(($request->amount * ($request->splits[Auth::id()] ?? 0)) / $totalShares, 2);
            $creatorLent = $request->amount - $creatorShare;

            foreach ($request->splits as $userId => $shares) {
                $user = \App\Models\User::find($userId);
                if (!$user) continue;

                $amount = round(($request->amount * $shares) / $totalShares, 2);

                if ($userId == Auth::id()) {
                    $split = \App\Models\Split::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'amount' => $creatorLent,
                        'type' => 'lent',
                        'method' => 'shares',
                        'value' => $shares
                    ]);

                    $user->lent_total += $split->amount;
                    $user->save();
                } else {
                    $split = \App\Models\Split::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'amount' => $amount,
                        'type' => 'owed',
                        'method' => 'shares',
                        'value' => $shares
                    ]);

                    $user->owed_total += $split->amount;
                    $user->save();
                }
            }
            break;

        case 'adjustment':
            foreach ($members as $member) {
                $user = \App\Models\User::find($member->id);
                if (!$user) continue;

                if ($member->id == Auth::id()) {
                    $split = \App\Models\Split::create([
                        'user_id' => $member->id,
                        'expense_id' => $expense->id,
                        'amount' => $request->amount,
                        'type' => 'lent',
                        'method' => 'adjustment',
                        'value' => $request->amount
                    ]);

                    $user->lent_total += $split->amount;
                    $user->save();
                } else {
                    $owedAmount = $request->splits[$member->id] ?? 0;
                    if ($owedAmount > 0) {
                        $split = \App\Models\Split::create([
                            'user_id' => $member->id,
                            'expense_id' => $expense->id,
                            'amount' => $owedAmount,
                            'type' => 'owed',
                            'method' => 'adjustment',
                            'value' => $owedAmount
                        ]);

                        $user->owed_total += $split->amount;
                        $user->save();
                    }
                }
            }
            break;
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



