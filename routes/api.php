  <?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\expenseController as apiExpenseController;
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
 

// ✅ Auth Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('lookup-user', [AuthController::class, 'lookupByPhone']);

// ✅ Authenticated User Routes with JWT
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']); // logged-in user info
});

// ✅ Expense Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/expenses', [apiExpenseController::class, 'index']);
    Route::post('/expenses', [apiExpenseController::class, 'store']);
    Route::get('/groups', [apiExpenseController::class, 'groups']);
    Route::get('/groups/{groupId}/budget-left', [apiExpenseController::class, 'getBudgetLeft']);
});

// ✅ Group Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/groups', [groupController::class, 'index']);               
    Route::post('/groups', [groupController::class, 'store']);              
    Route::put('/groups/{id}', [groupController::class, 'update']);         
    Route::get('/groups/{id}/weekly-expenses', [groupController::class, 'weeklyExpenses']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/groups', [groupController::class, 'index']);
    Route::post('/groups', [groupController::class, 'store']);
    Route::get('/groups/{id}', [groupController::class, 'show']);
    Route::put('/groups/{id}', [groupController::class, 'update']);
    Route::delete('/groups/{id}', [groupController::class, 'destroy']);

    Route::patch('/groups/{id}/budget', [groupController::class, 'updateBudget']);
    Route::get('/groups/{id}/weekly-expenses', [groupController::class, 'weeklyExpenses']);
    Route::get('/groups/{id}/monthly-expenses', [groupController::class, 'monthlyExpenses']);
    Route::get('/groups/{id}/analytics', [groupController::class, 'analytics']);
    Route::get('/groups/{id}/monthly-analytics', [groupController::class, 'monthlyAnalytics']);

    Route::post('/groups/{id}/members', [groupController::class, 'addMember']);
    Route::get('/groups/{id}/users', [groupController::class, 'getUsers']);
});


// ✅ Dashboard
Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard', [dashboardController::class, 'index']);
});

 
 
