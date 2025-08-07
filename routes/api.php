<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\expenseController;
use App\Http\Controllers\api\groupController;
use App\Http\Controllers\api\GroupExpenseController;
use App\Http\Controllers\api\GroupMemberController;
use App\Http\Controllers\api\SplitController;
use App\Http\Controllers\api\userController;
 
use App\Http\Controllers\api\reportController;
use App\Http\Controllers\api\dashboardController;
 
 
 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


 
 
Route::middleware(['auth:sanctum', 'admin'])->post('/users', [userController::class, 'index']);
Route::middleware(['auth:sanctum', 'admin'])->post('/users/{id}', [userController::class, 'index']);



// Groups ke liye 
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/groups', [groupController::class, 'index']);
    Route::post('/groups', [groupController::class, 'store']);
    Route::get('/groups/{id}', [groupController::class, 'show']);
    Route::put('/groups/{id}', [groupController::class, 'update']);
    Route::delete('/groups/{id}', [groupController::class, 'destroy']);
});
 
// routes/api.php

Route::middleware(['auth:sanctum'])->get('/my-groups', [groupController::class, 'myGroups']);





Route::middleware('auth:sanctum')->prefix('expenses')->group(function () {
    Route::get('/', [expenseController::class, 'index']);
    Route::post('/', [expenseController::class, 'store']);
    Route::get('{id}', [expenseController::class, 'show']);
    Route::put('{id}', [expenseController::class, 'update']);
    Route::delete('{id}', [expenseController::class, 'destroy']);
});



 
 

Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
 