<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Split; // Assuming you have a Split model
use App\Models\Expense; // Assuming you have an Expense model
use App\Models\User; // Assuming you have a User model
class splitController extends Controller
{
     function index(){
        $splits = Split::with(['expense', 'user'])->get();  
        return view('admin.splits.index', compact('splits'));

     }
      function create(){
        $expenses = Expense::all(); // Assuming you have an Expense model
        $users = User::all(); // Assuming you have a User model
        return view('admin.splits.create', compact('expenses', 'users'));
     
    }
    function store(Request $request)
    {
        // Logic to store a new split
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:owned,lent', // Validate type as either owned or lent
        ]);

        $split = new Split();
        $split->expense_id = $request->expense_id;
        $split->user_id = $request->user_id;
        $split->amount = $request->amount;
        $split->type = $request->type;
        $split->save();

        return redirect()->route('admin.splits.index')->with('success', 'Split added successfully.');
    }
    function edit($id)
    {
        // Split edit karne ke liye hai
        $split = Split::findOrFail($id);
        $expenses = Expense::all();
        $users = User::all();
        return view('admin.splits.edit', compact('split', 'expenses', 'users'));
    }
     function destroy($id){
        // Split delete karne ke liye
        $split = Split::findOrFail($id);
        $split->delete();

        return redirect()->route('admin.splits.index')->with('success', 'Split deleted successfully.');
     }
}