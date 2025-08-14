<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
class loginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
         

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            if( Auth::guard('admin')->user()->role !== 'admin') {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->withErrors(['email' => 'You do not have admin access'])->withInput();
            }
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput(); 
        }
    }

    public function register()
    {
        return view('register');
    }

    public function registerUser(Request $request)
    {
        // Registration logic here
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
