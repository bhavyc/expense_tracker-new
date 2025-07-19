<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Http\Request;

class loginController extends Controller
{
    //shows login page for user 
  function index(Request $request)
  {
      return view('login');
  }
   function authenticate(Request $request){

     $request->validate([
         'email' => 'required|email',
         'password' => 'required|min:6',
     ]);
      // Here you would typically check the credentials against your database
      // For now, let's assume the credentials are valid
       if(Auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
           // Authentication passed
         return redirect()->route('account.dashboard')->with('success', 'Login successful!');
       }
else{
     return redirect()->route('accoun.login')->with('success', 'Login successful!');
}
         
      
   }

   function register(Request $request){

         return view('register');
   }
  function registerUser(Request $request){
      $request->validate([
         'email' => 'required|email|unique:users',
         'password' => 'required|min:6|confirmed',
         'name' => 'required|string|max:255',
     ]);
      // Here you would typically save the user to your database
      // For now, let's assume the registration is successful
      $user = new User();
      $user->email = $request->email;
      $user->password = bcrypt($request->password);
      $user->name = $request->name;
      $user->role = 'user'; // Default role, you can change this as needed
      // Save the user to the database
      $user->save();

      return redirect()->route('account.login')->with('success', 'Registration successful! Please login.');
      
   }

   function logout(Request $request){
       Auth::logout();
       return redirect()->route('account.login')->with('success', 'Logout successful!');
   }
}
 