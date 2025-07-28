<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
class groupController extends Controller
{
     function index(){
         $groups= Group::all();
         $user = User::findorFail($groups->pluck('created_by'));
         $user= $user->pluck('name')[0];
         
         return view('admin.groups.index', compact('groups'));

     }
     function create(Request $request){
          $groups= Group::all();
          $users = User::all();
          return view('admin.groups.create', compact('groups', 'users'));
         

     }

     

     function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'description' => 'required',
        'created_by' => 'required|exists:users,id',
    ]);

    // Create group
    $group = Group::create($request->all());

    // Add the creator as a member
    $group->users()->attach($request->created_by);

    return redirect()->route('admin.groups.index')->with('success', 'Group created successfully.');
}


     function edit($id){
        $group=Group::findorfail($id);
        $users = User::all();
        return view('admin.groups.edit', compact('group', 'users'));
     }
        function update(Request $request, $id){
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'created_by' => 'required|exists:users,id',
            ]);
    
            $groups = Group::findOrFail($id);
            $groups->update($request->all());
    
            return redirect()->route('admin.groups.index')->with('success', 'Group updated successfully.');
        }
        function destroy($id){
            $groups=Group::findorfail($id);
            $groups->delete();
            return redirect()->route('admin.groups.index')->with('success', 'Group deleted successfully.');
        }
}