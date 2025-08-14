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

    function authenticate(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if(Auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            // Authentication passed
            return redirect()->route('account.dashboard')->with('success', 'Login successful!');
        } else {
            return redirect()->route('account.login')->with('error', 'Invalid credentials!');
        }
    }

    function register(Request $request){
        return view('register');
    }

    function registerUser(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|string|max:15|unique:users', // Added phone number validation
            'password' => 'required|min:6|confirmed',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number; // Save phone number
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

// namespace App\Http\Controllers;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;    
// use Illuminate\Http\Request;

// class loginController extends Controller
// {
//     //shows login page for user 
//   function index(Request $request)
//   {
//       return view('login');
//   }
//    function authenticate(Request $request){

//      $request->validate([
//          'email' => 'required|email',
//          'password' => 'required|min:6',
//      ]);
       
//       // For now, let's assume the credentials are valid
//        if(Auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
//            // Authentication passed
//          return redirect()->route('account.dashboard')->with('success', 'Login successful!');
//        }
// else{
//      return redirect()->route('accoun.login')->with('success', 'Login successful!');
// }
         
      
//    }

//    function register(Request $request){

//          return view('register');
//    }
//   function registerUser(Request $request){
//       $request->validate([
//          'email' => 'required|email|unique:users',
//          'password' => 'required|min:6|confirmed',
//          'name' => 'required|string|max:255',
//      ]);
      
//       $user = new User();
//       $user->email = $request->email;
//       $user->password = bcrypt($request->password);
//       $user->name = $request->name;
//       $user->role = 'user';  
//       $user->save();

//       return redirect()->route('account.login')->with('success', 'Registration successful! Please login.');
      
//    }

//    function logout(Request $request){
//        Auth::logout();
//        return redirect()->route('account.login')->with('success', 'Logout successful!');
//    }
// }
 