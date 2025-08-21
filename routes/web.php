<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\admin\loginController as AdminLoginController;
use App\Http\Controllers\admin\dashboardController as AdminDashboardController;
use App\Http\Controllers\admin\ExpenseController    ;
use App\Http\Controllers\admin\groupController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\groupMemberController;
use App\Http\Controllers\Admin\SplitController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\admin\categoriesController ;
use App\Http\Controllers\admin\adminController as AdminRegisterController;
use App\Http\Controllers\admin\adminUserController;
use App\Http\Controllers\reportController;
use App\Http\Controllers\expenseController as eController;
use App\Http\Controllers\userController;
use App\Http\Controllers\groupController as grController ;
use  App\Http\Controllers\FeedbackController as fController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/admin/register', [AdminRegisterController::class, 'showForm'])->name('admin.register');
Route::post('/admin/register', [AdminRegisterController::class, 'register']);
 
Route::group(['prefix' => 'account'], function () {















    
     Route::group(['middleware' => 'guest'], function () {
        Route::get('/login', [loginController::class, 'index'])->name('account.login');
    Route::post('/authenticate', [loginController::class, 'authenticate'])->name('account.authenticate');  
    Route::get('/register', [loginController::class, 'register'])->name('account.register');
    Route::post('/registerUser', [loginController::class, 'registerUser'])->name('account.registerUser');
    });
    
    // Email form show karega
Route::get('/forgot-password', function () {
    return view('auth.forgot-password'); // email form
})->name('password.request');

// Email pe reset link bhejne ka kaam karega
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');

// Link click hone ke baad reset form show karega
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

// Naya password save karega
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password)
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('account.login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->name('password.update');
 Route::get('/group-budget-left/{groupId}', [eController::class, 'getBudgetLeft'])->name('group.budget-left');

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/dashboard', [dashboardController::class, 'index'])->name('account.dashboard');
        Route::get('/logout', [loginController::class, 'logout'])->name('account.logout');
        Route::get('/index', [userController::class, 'index'])->name('account.index');
         Route::get('/expenses', [eController::class, 'index'])->name('user.expenses.index');
    Route::get('/expenses/create', [eController::class, 'create'])->name('user.expenses.create');
    Route::post('/expenses', [eController::class, 'store'])->name('user.expenses.store');
     Route::get('/feedback', function () {
        return view('user.feedbacks.form');  // The form page
    })->name('feedback.form');
 Route::post('/feedback', [fController::class, 'store'])->name('feedback.store');
    Route::get('user/groups', [grController::class, 'index'])->name('user.groups.index');
Route::post('/user/groups', [grController::class, 'store'])->name('user.groups.store');
 // routes/web.php (auth middleware के अंदर)
Route::post('/user/groups/{groupId}/members', [grController::class, 'addMember'])
    ->name('user.groups.members.add');

Route::get('/group/{id}/users', [grController::class, 'getUsers']);
// Show create form
Route::get('user/groups/create', function () {
    return view('user.groups.create');
})->name('user.groups.create.form');

// Handle create form submission
Route::post('user/groups/create', [grController::class, 'create'])->name('user.groups.create');


Route::get('/groups/{group}/analytics', [grController::class, 'analytics'])
     ->name('user.groups.analytics');
// monthly analytics
Route::get('groups/{group}/monthly-analytics', [grController::class, 'monthlyAnalytics'])->name('groups.monthlyAnalytics');

// Show edit form
Route::get('user/groups/{id}/edit', [grController::class, 'edit'])->name('user.groups.edit');

// Handle update
Route::put('user/groups/{id}', [grController::class, 'update'])->name('user.groups.update');

// Delete group
Route::delete('user/groups/{id}', [grController::class, 'destroy'])->name('user.groups.destroy');

// routes/web.php
Route::get('/weekly-expenses/{id}', [grController::class, 'weeklyExpenses'])
    ->name('group.weekly-expenses');

Route::patch('/groups/{id}/budget', [grController::class, 'updateBudget'])
    ->name('user.groups.updateBudget');


});

 Route::middleware('auth')->get('/my-feedbacks', function () {
    $feedbacks = \App\Models\Feedback::where('user_id', auth()->id())->latest()->get();
    return view('user.feedbacks.my_feedbacks', compact('feedbacks'));
})->name('feedback.my');  

 

}); 





Route::group(['prefix' => 'admin'], function () {
     Route::group(['middleware' => 'admin.guest'], function () {
         Route::get('login', [AdminLoginController::class, 'index'])->name('admin.login');
 
Route::post(' authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
   
    });
    

    Route::group(['middleware' => 'admin.auth'], function () {
       Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
 
    Route::get('logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
       Route::get('/expenses', [ExpenseController::class, 'index'])->name('admin.expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('admin.expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::get('/expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('admin.expenses.edit');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('admin.expenses.update');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('admin.expenses.destroy');
// Groups
       Route::resource('groups', groupController::class)->names('admin.groups');
   
   // Group members
  Route::resource('group-members', GroupMemberController::class)->names('admin.group-members');
    });

// split ka route
 Route::resource('splits', SplitController::class)->names('admin.splits');
   Route::get('/get-groups-users/{id}', [ExpenseController::class, 'getUsersByGroup']);
 

//  categories
Route::resource('categories', categoriesController::class)->names('admin.categories');

Route::get('/groups/{id}/weekly-expenses', [groupController::class, 'userWeeklyExpenses'])
     ->name('admin.groups.user_weekly_expenses');
Route::get('/groups/{id}/analytics', [groupController::class, 'analytics'])->name('admin.groups.analytics');
Route::get('/groups/{id}/monthly-analytics', [groupController::class, 'monthlyAnalytics'])->name('admin.groups.monthly_analytics');


// Analytics
// Route::get('/analytics/user', [AnalyticsController::class, 'analyticsForm'])->name('admin.analytics.user');
// Route::post('/analytics/user', [AnalyticsController::class, 'analyticsResult'])->name('admin.analytics.user.result');

 Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/user/{user}', [AnalyticsController::class, 'userAnalytics'])->name('admin.analytics.user');
 
  Route::get('/users',[adminUserController::class,'index'])->name('admin.users.index');  

  Route::get('/reports', [reportController::class, 'index'])->name('reports.index');
Route::get('/reports/export/pdf', [reportController::class, 'exportPdf'])->name('reports.export.pdf');
Route::get('/reports/export/csv', [reportController::class, 'exportCsv'])->name('reports.export.csv');
// Route::get('/group-members/{groupId}', [groupController::class, 'getGroupMembers']);

// web.php
Route::get('/get-users-by-group/{id}', [ExpenseController::class, 'getUsersByGroup']);
Route::get('/get-groups-by-user/{id}', [ExpenseController::class, 'getGroupsByUser']);
 

  
  Route::get('/users/{id}/edit', [adminUserController::class, 'edit'])->name('admin.users.edit');
  Route::put('/users/{id}', [adminUserController::class, 'update'])->name('admin.users.update');
  Route::delete('/users/{id}', [adminUserController::class, 'destroy'])->name('admin.users.destroy');


  Route::get('/get-groups-by-user/{id}', [ExpenseController::class, 'getGroupsByUser']);

    Route::get('/apis', function () {
    return view('admin.apis');
})->name('admin.apis');


Route::get('/feedbacks', [fController::class, 'index'])->name('admin.feedbacks.index');
    Route::post('/feedbacks/{id}/reply', [fController::class, 'reply'])->name('admin.feedbacks.reply');

}); 

//  Route::get('/change-language', function () {
//     $lang = request('lang');

//     if (in_array($lang, ['en', 'hi'])) {
//         session()->put('locale', $lang);
//     }

//     // Debug output
//     return back()->with('lang_set', session('locale'));
// })->name('change.language');

// routes/web.php
// web.php
Route::get('/change-language', function () {
    $lang = request('lang');
    session(['locale' => $lang]);
    app()->setLocale($lang);
    return back();
})->name('change.language');




Route::get('/test-mail', function () {
    $details = [
        'name' => 'Bhavya Choudhary',
        'total' => 1234,
        'month' => 'July 2025',
       
    ];

    Mail::to('123@gmail.com')->send(new MonthlyExpenseSummary($details));

    return " Mail Sent Successfully!";
});


 

 // routes/web.php

// For users
 
