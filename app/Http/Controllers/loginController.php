<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Http\Request;

class loginController extends Controller
{
    // Shows login page for user 
    function index(Request $request)
    {
        return view('login');
    }

    // 🔹 Authenticate by phone number
    function authenticate(Request $request){
        $request->validate([
            'phone_number' => 'required|string|max:15',
            'password' => 'required|min:6',
        ]);

        // Attempt login with phone_number instead of email
        if(Auth()->attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
            // Authentication passed
            return redirect()->route('account.dashboard')->with('success', 'Login successful!');
        } else {
            return redirect()->route('account.login')->with('error', 'Invalid credentials!');
        }
    }

    // Show register form
    function register(Request $request){
        return view('register');
    }

    // 🔹 Register user with phone number
    function registerUser(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users', // optional email
            'phone_number' => 'required|string|max:15|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email; // optional
        $user->phone_number = $request->phone_number;
        $user->password = bcrypt($request->password);
        $user->role = 'user';  
        $user->save();

        return redirect()->route('account.login')->with('success', 'Registration successful! Please login.');
    }

    function logout(Request $request){
        Auth::logout();
        return redirect()->route('account.login')->with('success', 'Logout successful!');
    }
}
