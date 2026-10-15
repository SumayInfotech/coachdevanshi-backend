<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\ResetPasswordController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\Admin\IngredientsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\PreparationCardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\MCQController;

/*
|--------------------------------------------------------------------------
| Admin Base Redirect
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
})->middleware('set.language');
Route::get('/send-test-mail', function () {
    \Illuminate\Support\Facades\Mail::raw('This is a test email from Laravel', function ($message) {
        $message->to('laraveldevsumay@gmail.com')
                ->subject('Test Mail from Spitmaan');
    });

    return 'Test email sent!';
});

//Customer ResetPassword
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetFormForCustomer'])->name('customer.password.reset')->middleware('set.language');
Route::post('/reset-password', [ResetPasswordController::class, 'resetCustomerPassword'])->name('customer.password.update')->middleware('set.language');


/*
|--------------------------------------------------------------------------
| Guest Routes (Admin Login)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['guest', 'set.language'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'index')->name('admin.login');
        Route::post('/post-login', 'postLogin')->name('admin.login-post');

        //Forgot Password Routes
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');

        //Reset Password Routes
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('admin.password.update');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Admin Dashboard & Management)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['admin.auth', 'set.language'])->group(function () {

    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    //Change Password
    Route::get('/change-password', [AuthController::class, 'changePassword'])
    ->name('change-password');

    //Update Password
    Route::post('/change-password', [AuthController::class, 'updatePassword'])
        ->name('update-password');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles');
        Route::get('/data', [RoleController::class, 'getRoles'])->name('roles.data');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/delete/{id}', [RoleController::class, 'delete'])->name('roles.delete');
        Route::put('/change-status/{uuid}', [RoleController::class, 'changeStatus'])->name('roles.change-status');

    });

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
        Route::put('/change-status/{uuid}', [UserController::class, 'changeStatus'])->name('user.change-status');
        Route::get('/get/{id}', [UserController::class, 'view'])->name('user.view');
        Route::post('/get-states', [UserController::class, 'getStates'])->name('user.get.states');
        Route::post('/get-cities', [UserController::class, 'getCities'])->name('user.get.cities');
        Route::post('/check-name-unique', [UserController::class, 'checkNameUnique'])->name('user.name-unique');
        Route::post('/check-email-unique', [UserController::class, 'checkEmailUnique'])->name('user.email-unique');
        Route::post('/check-mobile-unique', [UserController::class, 'checkMobileUnique'])->name('user.mobile-unique');
        Route::post('/check-user-name-unique', [UserController::class, 'checkUserNameUnique'])->name('user.user-name-unique');

        Route::get('/activity/{uuid}', [UserController::class, 'activity'])->name('user.activity');
        Route::get('/show-activity/{id}/changes',[UserController::class, 'changes'])->name('user.activity.changes');

        //Change Password
        Route::get('/change-password', [UserController::class, 'changePassword'])->name('user.change-password');
        Route::post('/update-password', [UserController::class, 'updatePassword'])->name('user.change-password.post');

        Route::post('/update-language', [UserController::class, 'updateLanguage'])->name('user.update-language');
    });
    /*

    /*
    |--------------------------------------------------------------------------
    | Settings Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings');
        Route::post('/update', [SettingsController::class, 'update'])->name('settings.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Preference Management
    |--------------------------------------------------------------------------
    */
    Route::resource('preferences', PreferenceController::class);
    Route::put('preferences/change-status/{uuid}',[PreferenceController::class, 'changeStatus'])->name('preferences.change-status');
});
