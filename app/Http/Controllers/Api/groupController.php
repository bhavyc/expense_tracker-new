<?php
// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Group;
// use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\Validator;
 
// class GroupController extends Controller
// {
//     public function index()
//     {
//         return response()->json(Group::all(), 200);
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'name' => 'required|string',
//             'description' => 'nullable|string',
//         ]);

//         $group = Group::create([
//             'name' => $validated['name'],
//             'description' => $validated['description'] ?? null,
//             'created_by' => Auth::id()
//         ]);

//         return response()->json($group, 201);
//     }

//     public function show($id)
//     {
//         $group = Group::findOrFail($id);
//         return response()->json($group, 200);
//     }

//     public function update(Request $request, $id)
//     {
//         $group = Group::findOrFail($id);

//         // Optional: Check if current user is owner
//         if ($group->created_by !== Auth::id()) {
//             return response()->json(['error' => 'Unauthorized'], 403);
//         }

//         $group->update($request->only(['name', 'description']));
//         return response()->json($group, 200);
//     }


 

 

// public function myGroups()
// {
//     $user = Auth::user();
//     $groups = $user->groups()->with('users')->get(); // Load group members too if needed

//     return response()->json([
//         'groups' => $groups
//     ]);
// }

//     public function destroy($id)
//     {
//         $group = Group::findOrFail($id);

//         if ($group->created_by !== Auth::id()) {
//             return response()->json(['error' => 'Unauthorized'], 403);
//         }

//         $group->delete();
//         return response()->json(['message' => 'Group deleted successfully'], 200);
//     }
// }


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Carbon\Carbon;

class GroupController extends Controller
{
     
    public function index()
    {
        $user = Auth::user();

        $groups = $user->groups()->with('expenses')->get()->map(function ($group) {
            $totalSpent = $group->expenses()->sum('amount');
            $group->totalSpent = $totalSpent;
            $group->budgetLeft = max($group->budget - $totalSpent, 0);
            return $group;
        });

        return response()->json([
            'success' => true,
            'groups' => $groups,
        ]);
    }

    // Store new group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'budget' => 'required|numeric|min:0',
            'permanent' => 'sometimes|boolean',
        ]);

        $user = Auth::user();

        $group = $user->groups()->create([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent') ? true : false,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Group created successfully',
            'group' => $group,
        ]);
    }

    // Update group
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'budget' => 'required|numeric|min:0',
            'permanent' => 'sometimes|boolean',
        ]);

        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'permanent' => $request->has('permanent') ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully',
            'group' => $group,
        ]);
    }

    // Weekly expenses summary for group
    public function weeklyExpenses($id)
    {
        $group = Group::with(['users.expenses' => function ($query) use ($id) {
            $query->where('group_id', $id)
                  ->where('expense_date', '>=', Carbon::now()->subDays(7));
        }])->findOrFail($id);

        $totalSpent = 0;

        $weeklyData = $group->users->map(function ($user) use (&$totalSpent, $id) {
            $userSpent = $user->expenses->where('group_id', $id)->sum('amount');
            $totalSpent += $userSpent;

            return [
                'name' => $user->name,
                'total' => $userSpent,
            ];
        });

        $budgetLeft = max($group->budget - $totalSpent, 0);

        return response()->json([
            'success' => true,
            'group' => $group,
            'weeklyData' => $weeklyData,
            'budgetLeft' => $budgetLeft,
            'totalSpent' => $totalSpent,
        ]);
    }
}
