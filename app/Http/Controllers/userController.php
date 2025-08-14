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

    }
 

