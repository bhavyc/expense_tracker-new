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

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/dashboard', [dashboardController::class, 'index'])->name('account.dashboard');
        Route::get('/logout', [loginController::class, 'logout'])->name('account.logout');
       Route::get('/index', [userController::class, 'index'])->name('account.index');
         Route::get('/expenses', [eController::class, 'index'])->name('user.expenses.index');
    Route::get('/expenses/create', [eController::class, 'create'])->name('user.expenses.create');
    Route::post('/expenses', [eController::class, 'store'])->name('user.expenses.store');

    Route::get('user/groups', [grController::class, 'index'])->name('user.groups.index');

// Show create form
Route::get('user/groups/create', function () {
    return view('user.groups.create');
})->name('user.groups.create.form');

// Handle create form submission
Route::post('user/groups/create', [grController::class, 'create'])->name('user.groups.create');

// Show edit form
Route::get('user/groups/{id}/edit', [grController::class, 'edit'])->name('user.groups.edit');

// Handle update
Route::put('user/groups/{id}', [grController::class, 'update'])->name('user.groups.update');

// Delete group
Route::delete('user/groups/{id}', [grController::class, 'destroy'])->name('user.groups.destroy');

});

 

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
    

//  categories
Route::resource('categories', categoriesController::class)->names('admin.categories');


// Analytics
// Route::get('/analytics/user', [AnalyticsController::class, 'analyticsForm'])->name('admin.analytics.user');
// Route::post('/analytics/user', [AnalyticsController::class, 'analyticsResult'])->name('admin.analytics.user.result');

 Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/user/{user}', [AnalyticsController::class, 'userAnalytics'])->name('admin.analytics.user');
 
  Route::get('/users',[adminUserController::class,'index'])->name('admin.users.index');  

  Route::get('/reports', [reportController::class, 'index'])->name('reports.index');
Route::get('/reports/export/pdf', [reportController::class, 'exportPdf'])->name('reports.export.pdf');
Route::get('/reports/export/csv', [reportController::class, 'exportCsv'])->name('reports.export.csv');
Route::get('/admin/group-members/{groupId}', [GroupController::class, 'getGroupMembers']);


  
  Route::get('/users/{id}/edit', [adminUserController::class, 'edit'])->name('admin.users.edit');
  Route::put('/users/{id}', [adminUserController::class, 'update'])->name('admin.users.update');
  Route::delete('/users/{id}', [adminUserController::class, 'destroy'])->name('admin.users.destroy');


  Route::get('/get-groups-by-user/{id}', [ExpenseController::class, 'getGroupsByUser']);

    Route::get('/apis', function () {
    return view('admin.apis');
})->name('admin.apis');
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


 

 