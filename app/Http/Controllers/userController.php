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

    
    $groups = $user->groups()->with(['users'])->get();

    return view('index', ['groups' => $groups]);
}

    }
 

