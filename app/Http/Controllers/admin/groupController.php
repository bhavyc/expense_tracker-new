<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;

class groupController extends Controller
{
    public function index()
    {
        $groups = Group::with('creator')->orderBy('budget', 'asc')->get();

        if ($groups->isEmpty()) {
            return redirect()->route('admin.groups.create')
                             ->with('warning', 'No groups found. Please create one.');
        }

        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.groups.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'created_by' => 'required|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'permanent' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['permanent'] = $request->has('permanent') ? 1 : 0;

        $group = Group::create($data);
        $group->users()->attach($request->created_by);

        return redirect()->route('admin.groups.index')->with('success', 'Group created successfully.');
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);
        $users = User::all();
        return view('admin.groups.edit', compact('group', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'created_by' => 'required|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'permanent' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['permanent'] = $request->has('permanent') ? 1 : 0;

        $group = Group::findOrFail($id);
        $group->update($data);

        return redirect()->route('admin.groups.index')->with('success', 'Group updated successfully.');
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.groups.index')->with('success', 'Group deleted successfully.');
    }
}
