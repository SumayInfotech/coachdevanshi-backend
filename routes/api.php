    <?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\PreferenceController;
use App\Http\Controllers\Api\V1\OnboardingMasterController;

Route::prefix('v1')->group(function () {

    


    /*
    |--------------------------------------------------------------------------
    | Guest Routes (Public APIs)
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        //Signup 
        Route::post('signup', [AuthController::class, 'signup']);
        Route::post('verify-signup-otp', [AuthController::class, 'verifyForgotOtp']);
        Route::post('resend-signup-otp', [AuthController::class, 'resendSignupOtp']);

        //Forgot Password
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('verify-forgot-otp', [AuthController::class, 'verifyForgotOtp']);

        //Login
        Route::post('login',  [AuthController::class, 'login']);
        Route::post('login-signup-verify-otp', [AuthController::class, 'verifyLoginSignupOtp']);

        //Social Login
        Route::post('social-login', [AuthController::class, 'socialLogin']);
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:api')->group(function () {
        Route::post('reset-password', [AuthController::class, 'resetPassword']);//Set New Password after forgot password
        Route::get('get-profile', [ProfileController::class, 'show']);
        Route::get('get-location', [ProfileController::class, 'getStatesOrCities']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('update-profile', [ProfileController::class, 'updateProfile']);
        Route::post('update-settings', [ProfileController::class, 'updateSettings']);
        Route::post('delete-account', [ProfileController::class, 'deleteAccount']);

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Change Password
        Route::post('change-password', [ProfileController::class, 'changePassword']);

        //Get Preferences List
        Route::get('get-preferences', [PreferenceController::class, 'index']);
        Route::post('update-preferences',[PreferenceController::class, 'updatePreferences']);

        //SendVerifyMobileOtp
        Route::post('send-mobile-otp', [ProfileController::class, 'sendMobileOtp']);
        Route::post('verify-mobile-otp', [ProfileController::class, 'verifyMobileOtp']);

        // Onboarding Masters
        Route::get('onboarding-masters', [OnboardingMasterController::class, 'index']);
    });
});