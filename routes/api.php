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
use App\Http\Controllers\api\userChatController; 
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
 

//  Auth Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('lookup-user', [AuthController::class, 'lookupByPhone']);

//  Authenticated User Routes with JWT
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']); // logged-in user info
});

//  Expense Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/expenses', [apiExpenseController::class, 'index']);
    Route::post('/expenses', [apiExpenseController::class, 'store']);

    // Budget-related
    Route::get('/groups/{groupId}/budget-left', [apiExpenseController::class, 'getBudgetLeft']);
    Route::get('/groups/{groupId}/check-budget/{amount}', [apiExpenseController::class, 'checkBudget']);
    Route::get('/personal/check-budget/{amount}', [apiExpenseController::class, 'checkPersonalBudget']);
});

//  Group Routes
Route::middleware('auth:api')->group(function () {
   Route::get('/groups', [groupController::class, 'index']);

    // Create a new group
    Route::post('/groups', [groupController::class, 'store']);

    // Update a group
    Route::put('/groups/{id}', [groupController::class, 'update']);

    // Delete a group
    Route::delete('/groups/{id}', [groupController::class, 'destroy']);

    // Update group budget
    Route::patch('/groups/{id}/budget', [groupController::class, 'updateBudget']);

    // Add member to group
    Route::post('/groups/{groupId}/add-member', [groupController::class, 'addMember']);

    // Get users in a group
    Route::get('/groups/{id}/users', [groupController::class, 'getUsers']);

    // Monthly expenses
    Route::get('/groups/{groupId}/monthly-expenses', [groupController::class, 'monthlyExpenses']);

    // Weekly expenses
    Route::get('/groups/{id}/weekly-expenses', [groupController::class, 'weeklyExpenses']);

    // Monthly analytics
    Route::get('/groups/{groupId}/monthly-analytics', [groupController::class, 'monthlyAnalytics']);

    // Weekly analytics
    Route::get('/groups/{groupId}/analytics', [groupController::class, 'analytics']);
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


//  Dashboard
Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard', [dashboardController::class, 'index']);
});

 
 
Route::middleware('auth:api')->group(function () {
    // Get user with groups and members
    Route::get('/user/groups', [userController::class, 'index']);

    // Personal budget
    Route::get('/user/budget', [userController::class, 'showBudget']);
    Route::put('/user/budget', [userController::class, 'updateBudget']);
});


Route::middleware('auth:api')->group(function () {
    // fetch chat with a specific admin
    Route::get('/chat/{adminId}', [userChatController::class, 'fetchMessages']);

    // send message to admin
    Route::post('/chat/send', [userChatController::class, 'sendMessage']);
});


// Send reset link to email
Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink'])->name('api.password.email');

// Reset password
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('api.password.update');