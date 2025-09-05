<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class userController extends Controller
{
    // public function index()
    // {
           
    //     $user = Auth::user();

         
    //     $groups = $user->groups()->with('users')->get();

       
    //     $members = $groups->pluck('users')->flatten();

        
    //     $uniqueMembers = $members->unique('id')->reject(function ($member) use ($user) {
    //         return $member->id === $user->id;
    //     });

       
    //     return view('index', ['members' => $uniqueMembers]);
    // }
     
public function index()
{
    
    $user = Auth::user();

$groups = $user->groups()
    ->with(['users' => function ($userQuery) {
        $userQuery->with(['expenses' => function ($expenseQuery) {
            
        }]);
    }])
    ->get();

 
    
$groups->each(function ($group) {
    $group->users->each(function ($member) use ($group) {
        $member->setRelation('expenses', 
            $member->expenses->where('group_id', $group->id)
             
        );
    });
});
  
    // $user = Auth::user();

    
    // $groups = $user->groups()->with(['users'])->get();
     
    return view('index', ['groups' => $groups]);
}



 public function showBudgetForm()
    {
        $user = Auth::user();
        return view('personal_budget', compact('user'));
    }

    public function updateBudget(Request $request)
    {
        $request->validate([
            'personal_budget' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $user->personal_budget = $request->personal_budget;
        $user->save();

        return redirect()->route('budget.form')->with('success', 'Personal budget updated successfully!');
    }
    }
 

