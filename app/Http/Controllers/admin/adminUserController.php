<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class adminUserController extends Controller
{
   public function index()
    {
        // Load users with related expenses and groups
        $users = User::with(['expenses', 'groups'])->get();

        return view('admin.users.index', compact('users'));
    }
}
