<?php

 

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\User;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth;

// class AuthController extends Controller
// {
//     public function register(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'email' => 'required|email|unique:users,email',
//             'password' => 'required|string|min:6|confirmed', // password_confirmation zaroori hai
//         ]);

//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//         ]);

//         $token = $user->createToken('ApiToken')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'User Created Successfully.',
//             'user' => $user,
//             'token' => $token,
//             'token_type' => 'Bearer',
//         ], 201);
//     }

//     public function login(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//             'password' => 'required|string|min:6',
//         ]);

//         if (!Auth::attempt($request->only('email', 'password'))) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Invalid credentials.',
//             ], 401);
//         }

//         $user = Auth::user();
//         $token = $user->createToken('ApiToken')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'User Logged In Successfully.',
//             'user' => $user,
//             'token' => $token,
//             'token_type' => 'Bearer',
//         ], 200);
//     }

//     public function logout(Request $request)
//     {
//         $request->user()->tokens()->delete();

//         return response()->json([
//             'success' => true,
//             'message' => 'Logged out successfully.',
//         ]);
//     }

//     public function user(Request $request)
// {
//     return response()->json($request->user());
// }

// }
 

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\User;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth;

// class AuthController extends Controller
// {
//     // Register API
//     public function register(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'email' => 'required|email|unique:users,email',
//             'phone_number' => 'required|string|max:15|unique:users,phone_number', // added
//             'password' => 'required|string|min:6|confirmed',
//         ]);

//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'phone_number' => $request->phone_number, // save phone number
//             'password' => Hash::make($request->password),
//         ]);

//         $token = $user->createToken('ApiToken')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'User Created Successfully.',
//             'user' => $user,
//             'token' => $token,
//             'token_type' => 'Bearer',
//         ], 201);
//     }

//     // Login API (email or phone_number)
//     public function login(Request $request)
//     {
//         $request->validate([
//             'login' => 'required|string', 
//             'password' => 'required|string|min:6',
//         ]);

//         $login_field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

//         if (!Auth::attempt([$login_field => $request->login, 'password' => $request->password])) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Invalid credentials.',
//             ], 401);
//         }

//         $user = Auth::user();
//         $token = $user->createToken('ApiToken')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'User Logged In Successfully.',
//             'user' => $user,
//             'token' => $token,
//             'token_type' => 'Bearer',
//         ], 200);
//     }

//     // Logout API
//     public function logout(Request $request)
//     {
//         $request->user()->tokens()->delete();

//         return response()->json([
//             'success' => true,
//             'message' => 'Logged out successfully.',
//         ]);
//     }

//     // Current Authenticated User
//     public function user(Request $request)
//     {
//         return response()->json($request->user());
//     }


//     // Lookup user by phone number
// public function lookupByPhone(Request $request)
// {
//     $request->validate([
//         'phone_number' => 'required|string|max:15',
//     ]);

//     $user = User::where('phone_number', $request->phone_number)->first();

//     if ($user) {
//         return response()->json([
//             'success' => true,
//             'message' => 'User found.',
//             'user' => $user,
//         ], 200);
//     } else {
//         return response()->json([
//             'success' => false,
//             'message' => 'No user found with this phone number.',
//         ], 404);
//     }
// }

// }

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:15|unique:users,phone_number',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        // Generate JWT with phone_number
        $token = JWTAuth::fromUser($user, ['phone_number' => $user->phone_number]);

        return response()->json([
            'success' => true,
            'message' => 'User Created Successfully.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $login_field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        $credentials = [
            $login_field => $request->login,
            'password' => $request->password,
        ];

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // user nikal lo
        $user = Auth::guard('api')->user();

        // phone number embed with token



        $customToken = JWTAuth::claims(['phone_number' => $user->phone_number])->fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User Logged In Successfully.',
            'user' => $user,
            'token' => $customToken,
        ], 200);
    }



    
    // Current User
    public function user()
    {
        return response()->json(Auth::guard('api')->user());
    }

    // Logout
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function lookupByPhone(Request $request)
{ 
    $request->validate([
        'phone_number' => 'required|string|max:15',
    ]);

    $user = User::where('phone_number', $request->phone_number)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'No user found with this phone number.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'User found.',
        'user' => $user,
    ], 200);
}
}




