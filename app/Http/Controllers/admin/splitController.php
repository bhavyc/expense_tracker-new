<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Split;  
use App\Models\Expense;  
use App\Models\User;  
class splitController extends Controller
{
     function index(){
        $splits = Split::with(['expense', 'user'])->get();  
        return view('admin.splits.index', compact('splits'));

     }
      function create(){
        $expenses = Expense::all();  
        $users = User::all();  
        return view('admin.splits.create', compact('expenses', 'users'));
     
    }
    function store(Request $request)
    {
         
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:owed,lent',  
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