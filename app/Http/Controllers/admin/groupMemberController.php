<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;  
use App\Models\Group;  
use App\Models\GroupMember;  
class groupMemberController extends Controller
{
     function index()
    {
         $members = GroupMember::with(['user', 'group'])->get();  
        return view('admin.group-members.index', compact('members'));
                      
    }

    function create(){
         $users = User::where('role', '!=', 'admin')->get(); 
        $groups = Group::all();  
        return view('admin.group-members.create', compact('users', 'groups'));
    }

    function store(Request $request)
    {
         
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
        ]);

        
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
        $users = User::where('role', '!=', 'admin')->get();
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
