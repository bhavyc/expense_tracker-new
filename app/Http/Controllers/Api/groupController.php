<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
 
class GroupController extends Controller
{
    public function index()
    {
        return response()->json(Group::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id()
        ]);

        return response()->json($group, 201);
    }

    public function show($id)
    {
        $group = Group::findOrFail($id);
        return response()->json($group, 200);
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        // Optional: Check if current user is owner
        if ($group->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $group->update($request->only(['name', 'description']));
        return response()->json($group, 200);
    }


 

 

public function myGroups()
{
    $user = Auth::user();
    $groups = $user->groups()->with('users')->get(); // Load group members too if needed

    return response()->json([
        'groups' => $groups
    ]);
}

    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        if ($group->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $group->delete();
        return response()->json(['message' => 'Group deleted successfully'], 200);
    }
}
