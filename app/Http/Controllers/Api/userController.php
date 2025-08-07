<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class userController extends Controller
{
     public function index(){
        $users=User::all();
        return response()->json([
            'success' => true,
            'users' => $users
        ], 200);
     }

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
}
