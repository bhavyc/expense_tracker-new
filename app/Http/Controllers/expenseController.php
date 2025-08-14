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

         
        if ($request->group_id) {
            $group = Group::with('expenses')->findOrFail($request->group_id);

            $totalSpent = $group->expenses()->sum('amount');
            $budgetLeft = max($group->budget - $totalSpent, 0);

            if ($request->amount > $budgetLeft) {
                return back()
                    ->withErrors(['amount' => 'Expense amount exceeds the remaining group budget.'])
                    ->withInput();
            }
        }

         
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

        return redirect()->route('user.expenses.index')->with('success', 'Expense added and split successfully.');
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



