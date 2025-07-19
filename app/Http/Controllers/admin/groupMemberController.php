<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Assuming you have a User model
use App\Models\Group; // Assuming you have a Group model    
use App\Models\GroupMember; // Assuming you have a GroupMember model
class groupMemberController extends Controller
{
     function index()
    {
         $members = GroupMember::with(['user', 'group'])->get(); // Eager load user and group relationships
        return view('admin.group-members.index', compact('members'));
                      
    }

    function create(){
        $users = User::all(); // Assuming you have a User model
        $groups = Group::all(); // Assuming you have a Group model
        return view('admin.group-members.create', compact('users', 'groups'));
    }

    function store(Request $request)
    {
        // Logic to store a new group member
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
        ]);

        // Assuming you have a GroupMember model
        $groupMember = new GroupMember();
        $groupMember->user_id = $request->user_id;
        $groupMember->group_id = $request->group_id;
        $groupMember->save();

        return redirect()->route('admin.group-members.index')->with('success', 'Group member added successfully.');
    }
    function  edit($id)
    {
        //   group member edit karne ke liye hai
        $groupMember = GroupMember::findOrFail($id);
        $users = User::all();
        $groups = Group::all();
        return view('admin.group-members.edit', compact('groupMember', 'users', 'groups'));
    }
    function update(Request $request, $id)
    {
        //group member update karhne ke liye
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
        ]);

        $groupMember = GroupMember::findOrFail($id);
        $groupMember->user_id = $request->user_id;
        $groupMember->group_id = $request->group_id;
        $groupMember->save();

        return redirect()->route('admin.group-members.index')->with('success', 'Group member updated successfully.');
    }
    function destroy($id)
    {
        // delete karne ka logic hai
        $groupMember = GroupMember::findOrFail($id);
        $groupMember->delete();

        return redirect()->route('admin.group-members.index')->with('success', 'Group member deleted successfully.');
    }

}
