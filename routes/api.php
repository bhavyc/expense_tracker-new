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
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


 
 
// Route::middleware(['auth:sanctum', 'admin'])->post('/users', [userController::class, 'index']);
// Route::middleware(['auth:sanctum', 'admin'])->post('/users/{id}', [userController::class, 'index']);



// // Groups ke liye 
// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/groups', [groupController::class, 'index']);
//     Route::post('/groups', [groupController::class, 'store']);
//     Route::get('/groups/{id}', [groupController::class, 'show']);
//     Route::put('/groups/{id}', [groupController::class, 'update']);
//     Route::delete('/groups/{id}', [groupController::class, 'destroy']);
// });
 
// // routes/api.php

// Route::middleware(['auth:sanctum'])->get('/my-groups', [groupController::class, 'myGroups']);





// Route::middleware('auth:sanctum')->prefix('expenses')->group(function () {
//     Route::get('/', [expenseController::class, 'index']);
//     Route::post('/', [expenseController::class, 'store']);
//     Route::get('{id}', [expenseController::class, 'show']);
//     Route::put('{id}', [expenseController::class, 'update']);
//     Route::delete('{id}', [expenseController::class, 'destroy']);
// });



 
 

// Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
 
 

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Authenticated user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']); // logged-in user info
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/expenses', [apiExpenseController::class, 'index']);
    Route::post('/expenses', [apiExpenseController::class, 'store']);
    Route::get('/groups', [apiExpenseController::class, 'groups']);
    Route::get('/groups/{groupId}/budget-left', [apiExpenseController::class, 'getBudgetLeft']);
});




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/groups', [groupController::class, 'index']);               // List groups
    Route::post('/groups', [groupController::class, 'store']);              // Create group
    Route::put('/groups/{id}', [groupController::class, 'update']);         // Update group
    Route::get('/groups/{id}/weekly-expenses', [groupController::class, 'weeklyExpenses']); // Weekly expenses summary
});


 

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [dashboardController::class, 'index']);
});

Route::post('lookup-user', [AuthController::class, 'lookupByPhone']);
