<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class dashboardController extends Controller
{
     function index(Request $request)
     {
          
         return view('dashboard');
     }
}
