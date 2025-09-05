<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class userController extends Controller
{
    //  public function index(){
    //     $users=User::all();
    //     return response()->json([
    //         'success' => true,
    //         'users' => $users
    //     ], 200);
    //  }

     public function show($id){
         $user= User::find($id);
         print_r($user);
         if($user){
            return response()->json([
                'success' => true,
                'user' => $user
            ], 200);
         }  
         else{
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
         }
     }


       public function index()
    {
        $user = Auth::user();

        $groups = $user->groups()
            ->with(['users' => function ($userQuery) {
                $userQuery->with(['expenses']);
            }])
            ->get();

        // Filter only relevant group expenses for each member
        $groups->each(function ($group) {
            $group->users->each(function ($member) use ($group) {
                $member->setRelation(
                    'expenses',
                    $member->expenses->where('group_id', $group->id)
                );
            });
        });

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'groups' => $groups
        ]);
    }

    // Show personal budget of logged-in user
    public function showBudget()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'personal_budget' => $user->personal_budget
        ]);
    }

    // Update personal budget
    public function updateBudget(Request $request)
    {
        $request->validate([
            'personal_budget' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $user->personal_budget = $request->personal_budget;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Personal budget updated successfully!',
            'personal_budget' => $user->personal_budget
        ]);
    }
}
