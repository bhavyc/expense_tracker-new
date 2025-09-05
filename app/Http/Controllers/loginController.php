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

    /**
     * 🔹 Authenticate by phone number
     */
    function authenticate(Request $request){
        $request->validate([
            'phone_number' => 'required|string|max:15',
            'password' => 'required|min:6',
        ]);

        if(Auth()->attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
            return redirect()->route('account.dashboard')->with('success', 'Login successful!');
        } else {
            return redirect()->route('account.login')->with('error', 'Invalid credentials!');
        }
    }

    // Show register form
    function register(Request $request){
        return view('register');
    }

    // 🔹 Register user with phone number + extra fields
    function registerUser(Request $request){
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|unique:users',
            'phone_number'  => 'required|string|max:15|unique:users',
            'password'      => 'required|min:6|confirmed',

            // New fields
            'country'       => 'nullable|string|max:100',
            'city'          => 'nullable|string|max:100',
            'financial_goal'=> 'nullable|string|max:255',
            'gender'        => 'nullable|in:male,female,other',
            'occupation'    => 'nullable|string|max:255',
            'profile_pic'   => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // 2MB limit
        ]);

        $user = new User();
        $user->name          = $request->name;
        $user->email         = $request->email;
        $user->phone_number  = $request->phone_number;
        $user->password      = bcrypt($request->password);
        $user->role          = 'user';  

        // Extra fields
        $user->country       = $request->country;
        $user->city          = $request->city;
        $user->financial_goal= $request->financial_goal;
        $user->gender        = $request->gender;
        $user->occupation    = $request->occupation;

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public'); 
            $user->profile_pic = $path;
        }

        $user->save();

        return redirect()->route('account.login')->with('success', 'Registration successful! Please login.');
    }

    // Logout
    function logout(Request $request){
        Auth::logout();
        return redirect()->route('account.login')->with('success', 'Logout successful!');
    }
}
