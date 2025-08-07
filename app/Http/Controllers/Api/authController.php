<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{
    public function register(Request $request)
    {
        $credentials = Validator::make($request->all(), [     
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($credentials->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $credentials->errors()
            ], 401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'User Created Successfully.',
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User Not Created.',
                'user' => null
            ], 401);
        }
    }

    public function login(Request $req)
    {
        $credentials = Validator::make($req->all(), [   // ✅ Fixed here
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($credentials->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $credentials->errors()
            ], 401);
        }

        if (Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'message' => 'User Logged In Successfully.',
                'token' => $user->createToken('ApiToken')->plainTextToken,
                'type' => 'Bearer'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorised.',
            ], 401);
        }
    }

    public function logout(Request $req)
    {
        $req->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'user' => $req->user(),
            'message' => 'You are Logged Out Successfully.'
        ], 200);
    }
}
