    <?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\PreferenceController;
use App\Http\Controllers\Api\V1\OnboardingMasterController;
use App\Http\Controllers\Api\V1\OnboardingController;
use App\Http\Controllers\Api\V1\PathwayController;
use App\Http\Controllers\Api\V1\PlanController;
use App\Http\Controllers\Api\V1\TrackingController;
use App\Http\Controllers\Api\V1\DailyCheckinController;
use App\Http\Controllers\Api\V1\WeeklyCheckinController;
use App\Http\Controllers\Api\V1\ProgressPhotoController;
use App\Http\Controllers\Api\V1\FoodDiaryController;
use App\Http\Controllers\Api\V1\BloodReportController;
use App\Http\Controllers\Api\V1\CalendarController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ChatController;

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

        // Home
        Route::post('home-tour/complete', [DashboardController::class, 'completeHomeTour']);

        // Onboarding (6 chapters + consent)
        Route::prefix('onboarding')->group(function () {
            Route::get('status', [OnboardingController::class, 'status']);
            Route::get('answers', [OnboardingController::class, 'answers']);
            Route::get('chapters/{chapter}', [OnboardingController::class, 'chapter'])->whereNumber('chapter');
            Route::post('chapters/{chapter}', [OnboardingController::class, 'saveChapter'])->whereNumber('chapter');
            Route::post('consent', [OnboardingController::class, 'consent']);
        });

        // Pathways & protocol questionnaires
        Route::get('pathways', [PathwayController::class, 'index']);
        Route::post('pathways/select', [PathwayController::class, 'select']);
        Route::get('pathways/{uuid}/protocol', [PathwayController::class, 'protocol']);
        Route::post('pathways/{uuid}/protocol', [PathwayController::class, 'saveProtocol']);

        // Plan validity & renewal
        Route::get('plan', [PlanController::class, 'show']);
        Route::post('plan/renewal-request', [PlanController::class, 'requestRenewal']);
        Route::get('coach-notes', [PlanController::class, 'coachNotes']);

        // Tracking
        Route::get('tracking/options', [TrackingController::class, 'options']);
        Route::get('daily-checkins', [DailyCheckinController::class, 'index']);
        Route::post('daily-checkins', [DailyCheckinController::class, 'store']);
        Route::get('daily-checkins/{key}', [DailyCheckinController::class, 'show']);
        Route::get('weekly-checkins', [WeeklyCheckinController::class, 'index']);
        Route::post('weekly-checkins', [WeeklyCheckinController::class, 'store']);
        Route::get('weekly-checkins/{key}', [WeeklyCheckinController::class, 'show']);
        Route::get('progress-photos', [ProgressPhotoController::class, 'index']);
        Route::post('progress-photos', [ProgressPhotoController::class, 'store']);
        Route::get('food-diary', [FoodDiaryController::class, 'index']);
        Route::post('food-diary', [FoodDiaryController::class, 'store']);
        Route::get('blood-reports', [BloodReportController::class, 'index']);
        Route::post('blood-reports', [BloodReportController::class, 'store']);
        Route::post('blood-reports/{uuid}/delete', [BloodReportController::class, 'destroy']);
        Route::post('blood-reports/files/{uuid}/delete', [BloodReportController::class, 'destroyFile']);

        // Calendar & daily log
        Route::get('calendar', [CalendarController::class, 'month']);
        Route::get('calendar/day', [CalendarController::class, 'day']);
        Route::post('daily-notes', [CalendarController::class, 'saveNote']);

        // Progress
        Route::get('progress', [ProgressController::class, 'summary']);

        // Apple Health / Google Fit
        Route::post('health/connect', [HealthController::class, 'connect']);
        Route::post('health/sync', [HealthController::class, 'sync']);
        Route::get('health/activity', [HealthController::class, 'index']);

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/read-all', [NotificationController::class, 'readAll']);
        Route::get('notifications/{uuid}', [NotificationController::class, 'show']);
        Route::post('notifications/{uuid}/read', [NotificationController::class, 'read']);
        Route::post('fcm-token', [NotificationController::class, 'saveFcmToken']);

        // Coach chat
        Route::get('chat/messages', [ChatController::class, 'index']);
        Route::post('chat/messages', [ChatController::class, 'send']);
        Route::get('chat/unread-count', [ChatController::class, 'unreadCount']);
    });
});