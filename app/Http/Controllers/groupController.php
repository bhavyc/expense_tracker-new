<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class groupController extends Controller
{
    
    public function index(){
        $user= Auth::user();
        $groups= $user->groups()->get();
        return view('user.groups.index', compact('groups'));
    }

   public function create(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:255',
    ]);

    $user = Auth::user();

    $user->groups()->create([
        'name' => $request->name,
        'description' => $request->description,
        'created_by' => $user->id
    ]);

    return redirect()->route('user.groups.index')->with('success', 'Group created successfully.');
}

 public function edit($id)
{
    $user = Auth::user();
    $group = $user->groups()->findOrFail($id); // Only user's own group

    return view('user.groups.edit', compact('group'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:255',
    ]);

    $user = Auth::user();
    $group = $user->groups()->findOrFail($id);

    $group->update([
        'name' => $request->name,
        'description' => $request->description
    ]);

    return redirect()->route('user.groups.index')->with('success', 'Group updated successfully.');
}
}
