<?php
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \App\Helpers\FlangeHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ProjectRequest;
use App\Models\ProjectGasResult;
use App\Models\ValveDimension;
use App\Models\FlangeDimension;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use App\Models\UserFcmToken;


//Check Permission
if (!function_exists('hasPermission')) {
    /**
     * Check if the authenticated user has the specified permission.
     *
     * @param string $permissionName
     * @return bool
     */
    function hasPermission($permissionName)
    {
         // For testing purposes, always return true,When needed to check permission, comment this line
        // return true;

        // Get the authenticated user
        $user = auth()->user();
        
        // echo "<pre>";print_r($user);"</pre>";die;
        if (!$user) {   
            return false; // No user logged in
            // \Log::info('Has Permission Helper Funcion Call And No Logged In User Found');
        }
        // \Log::info('Has Permission Helper Funcion Call And User Login Check Successfully');

        // $role = Role::where('uuid', $user->role_uuid)->where('status','active')->first();
        $role = Role::withoutGlobalScope('exclude_super_admin')
                    ->where('uuid', $user->role_uuid)
                    ->where('status', 'Active')
                    ->first();
        if (!$role) {
            return false; 
        }
        return $role->permissions->contains('name', $permissionName);
    }
}

//File Upload Function
if (!function_exists('uploadFile')) {
    function uploadFile($file, string $folderName, ?string $existingFilePath = null)
    {
        try {
            // Define the storage path based on the module
            $folderPath = "uploads/{$folderName}"; // Example: 'uploads/users'

            // Delete old file if exists
            if ($existingFilePath) {
                $storagePath = "uploads/{$existingFilePath}"; // Convert "users/filename" to "uploads/users/filename"

                if (Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->delete($storagePath);
                }
            }

            // Generate a unique name for the file
            $uniqueName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store new file with the unique name
            $filePath = $file->storeAs($folderPath, $uniqueName, 'public');

            // Remove "uploads/" from path before saving to DB (e.g., "users/filename")
            $dbFilePath = str_replace('uploads/', '', $filePath);

           return $dbFilePath;
        } catch (\Exception $e) {
            return false; 
        }
    }
}

//Get File URL
if (!function_exists('getFileUrl')) {
    function getFileUrl($imagePath)
    {
        return asset('storage/uploads/' . $imagePath);
    }
}

if (!function_exists('jsonResponse')) {
    // function jsonResponse($status = true, $message = '', $data = '')
    function jsonResponse($success = true, $message = '', $data = null, $statusCode = 200)
    {
        return response()->json([
            // 'status_code' => $statusCode,
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ], $statusCode);
    }
}
if (!function_exists('formatName')) {
    function formatName($string)
    {
        if($string === null || $string === '') {
            return null;
        }
        return ucwords(str_replace('_', ' ', $string));
    }
}
if (!function_exists('pagination_length_options')) {
    function pagination_length_options(): array
    {
        return [
            10,
            25,
            50,
            100,
            500,
            1000,
        ];
    }
}

if (!function_exists('datatable_length_menu')) {
    function datatable_length_menu(): array
    {
        $lengths = pagination_length_options();

        return [
            array_merge($lengths, [-1]),
            array_merge($lengths, ['All']),
        ];
    }
}
if (!function_exists('pagination_length_validation_rule')) {
    function pagination_length_validation_rule(): string
    {
        return 'nullable|in:' . implode(',', pagination_length_options());
    }
}


if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Setting::where('key', $key)->value('value') ?? $default;
    }
}

if (!function_exists('appLanguage')) {
    function appLanguage()
    {
        // Logged-in user's language
        if (Auth::check() && !empty(Auth::user()->language)) {
            return Auth::user()->language;
        }

        // Default language from admin
        $adminUser = User()->where('email', 'admin@gmail.com')->first();
        return isset($adminUser) ? $adminUser->language : 'it';
    }
}
//Manage For Mobile Application
if (!function_exists('setApiLocale')) {
    function setApiLocale($lang = 'en')
    {
        app()->setLocale(in_array($lang, ['en', 'it']) ? $lang : 'en');
    }
}
if (!function_exists('dateFormat')) {
    function dateFormat($date, $format = 'd-m-Y h:i:s')
    {
        if (!$date) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return '-';
        }
    }
}
if (!function_exists('sendOtp')) {
    /**
     * Send OTP via Authyo.io SMS API.
     * Returns ['success' => true, 'maskId' => '...'] on success.
     * The maskId must be stored temporarily and passed to verifyOtp().
     */
    function sendOtp(string $phoneNumber, string $otp = null)
    {
        // echo $phoneNumber;die;
        // echo $otp;die;
        try {
            $appId        = env('AUTHYO_APP_ID');
            $clientId     = env('AUTHYO_CLIENT_ID');
            $clientSecret = env('AUTHYO_CLIENT_SECRET');
            $params = [
                'to'        => $phoneNumber,
                'expiry'    => 10,
                'otpLength' => 6,
                'authWay'   => 'SMS',
            ];

            // Pass a custom OTP if provided (e.g., for master/debug OTPs)
            if ($otp !== null) {
                $params['otp'] = $otp;
            }

            $response = Http::withHeaders([
                'clientId'     => $clientId,
                'clientSecret' => $clientSecret,
                'origin'       => $appId,
            ])->get('https://app.authyo.io/api/v1/auth/sendotp', $params);

            $body = $response->json();

            if ($response->successful() && isset($body['status']) && $body['status'] === true) {
                return [
                    'success' => true,
                    'maskId'  => $body['maskId'] ?? null,
                    'message' => $body['message'] ?? 'OTP sent successfully',
                ];
            }

            \Log::error("Authyo OTP Send Failed", [
                'phone'    => $phoneNumber,
                'response' => $body,
            ]);
            return [
                'success' => false,
                'message' => $body['message'] ?? 'Failed to send OTP',
            ];
        } catch (\Exception $e) {
            \Log::error("Authyo OTP Send Error", [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

if (!function_exists('verifyOtp')) {
    /**
     * Verify OTP via Authyo.io.
     * $maskId is the value returned by sendOtp().
     * $code   is the OTP entered by the user.
     */
    function verifyOtp(string $maskId, string $code)
    {
        try {
            $appId        = env('AUTHYO_APP_ID');
            $clientId     = env('AUTHYO_CLIENT_ID');
            $clientSecret = env('AUTHYO_CLIENT_SECRET');

            $response = Http::withHeaders([
                'clientId'     => $clientId,
                'clientSecret' => $clientSecret,
                'origin'       => $appId,
            ])->post('https://app.authyo.io/api/v1/auth/verifyotp', [
                'maskId' => $maskId,
                'otp'    => $code,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['status']) && $body['status'] === true) {
                return [
                    'success' => true,
                    'message' => $body['message'] ?? 'OTP verified successfully',
                ];
            }

            Log::warning("Authyo OTP Verification Failed", [
                'maskId'   => $maskId,
                'response' => $body,
            ]);

            return [
                'success' => false,
                'message' => $body['message'] ?? 'Invalid or expired OTP',
            ];
        } catch (\Exception $e) {
            Log::error("Authyo OTP Verify Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}


if (!function_exists('firebaseCredentials')) {
    /**
     * The Firebase service-account JSON as an array, or null when it isn't configured.
     */
    function firebaseCredentials(): ?array
    {
        $path = config('services.firebase.credentials');
        if (!$path) {
            return null;
        }
        if (!is_file($path)) {
            $path = base_path($path);
        }
        if (!is_file($path)) {
            return null;
        }
        $credentials = json_decode((string) file_get_contents($path), true);

        return is_array($credentials) && !empty($credentials['private_key']) ? $credentials : null;
    }
}

if (!function_exists('sendNotification')) {
    /**
     * Send an FCM push to one or more device tokens. Returns the FCM responses, or false when nothing was sent.
     * Tokens FCM reports as no longer valid are removed from user_fcm_tokens.
     */
    function sendNotification($tokens, $title, $body, $data = [])
    {
        $tokens = array_values(array_unique(array_filter((array) $tokens)));
        if (empty($tokens)) {
            return false;
        }

        $credentials = firebaseCredentials();
        if (!$credentials) {
            \Log::warning('FCM credentials not configured, push skipped. Set FIREBASE_CREDENTIALS in .env.', [
                'path' => config('services.firebase.credentials'),
            ]);
            return false;
        }

        $projectId   = config('services.firebase.project_id') ?: ($credentials['project_id'] ?? null);
        $accessToken = getAccessToken($credentials);
        $url         = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        // FCM data values must be strings
        $data = collect($data)->map(fn ($v) => is_scalar($v) ? (string) $v : json_encode($v))->all();

        $responses = [];
        foreach ($tokens as $token) {
            $message = [
                'message' => [
                    'token'        => $token,
                    'notification' => ['title' => (string) $title, 'body' => (string) $body],
                    'data'         => (object) $data,
                    'android'      => ['priority' => 'high', 'notification' => ['sound' => 'default']],
                    'apns'         => [
                        'headers' => ['apns-priority' => '10'],
                        'payload' => ['aps' => ['sound' => 'default']],
                    ],
                ],
            ];

            try {
                $response = \Illuminate\Support\Facades\Http::withToken($accessToken)->timeout(15)->post($url, $message);
            } catch (\Throwable $e) {
                \Log::error('FCM request failed', ['error' => $e->getMessage()]);
                continue;
            }

            $responses[] = $response->json();
            if ($response->failed()) {
                $status  = $response->json('error.status');
                $message = (string) $response->json('error.message');
                \Log::error('FCM push rejected', ['http' => $response->status(), 'status' => $status, 'error' => $message]);

                // The app was uninstalled or the token rotated: stop sending to it.
                if (in_array($status, ['UNREGISTERED', 'NOT_FOUND'], true)
                    || ($status === 'INVALID_ARGUMENT' && str_contains($message, 'registration token'))) {
                    UserFcmToken()->where('fcm_token', $token)->delete();
                }
            }
        }

        return $responses;
    }
}

if (!function_exists('getAccessToken')) {
    /**
     * OAuth2 access token for FCM, cached until shortly before it expires.
     */
    function getAccessToken($credentials)
    {
        if (is_string($credentials)) {
            $credentials = json_decode((string) file_get_contents($credentials), true);
        }

        return \Illuminate\Support\Facades\Cache::remember('fcm_access_token_' . md5($credentials['client_email']), 3300, function () use ($credentials) {
            $response = \Illuminate\Support\Facades\Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => createJWT($credentials),
            ]);

            if (!$response->successful() || !$response->json('access_token')) {
                throw new \RuntimeException('FCM access token request failed: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }
}

if (!function_exists('createJWT')) {
    function createJWT($credentials)
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $iat = time();
        $exp = $iat + 3600;
        $payload = json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $iat,
            'exp' => $exp
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = '';
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $credentials['private_key'], 'SHA256');

        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}


if (!function_exists('storeUserFcmToken')) {
    function storeUserFcmToken($user, $request)
    {
        if (!$request->filled('fcm_token')) {
            return;
        }


        UserFcmToken::updateOrCreate(
            [
                'user_uuid' => $user->uuid,
                'fcm_token' => $request->fcm_token,
            ],
            [
                'device_type' => $request->device_type ?? null,
                'device_name' => $request->device_name ?? null,
            ]
        );
    }
}

if (!function_exists('paginatedResponse')) {

    function paginatedResponse($collection, $resource, $key = 'items')
    {
        return [
            $key => $resource::collection($collection->items()),

            
            'meta' => [
                'current_page' => $collection->currentPage(),
                'last_page' => $collection->lastPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
                'from' => $collection->firstItem(),
                'to' => $collection->lastItem()                
            ],
        ];
    }
}
// if (!function_exists('paginatedResponse')) {

//     function paginatedResponse($collection, $resource): array
//     {
//         return [
//             'data' => $resource::collection($collection->items()),

//             'current_page' => $collection->currentPage(),
//             'last_page' => $collection->lastPage(),
//             'per_page' => $collection->perPage(),
//             'total' => $collection->total(),
//             'from' => $collection->firstItem(),
//             'to' => $collection->lastItem(),
//             'has_more_pages' => $collection->hasMorePages(),
//         ];
//     }
// }
if (!function_exists('onboarding_chapter_categories')) {
    /**
     * Onboarding chapter => category slugs.
     * Pass a chapter number to get that chapter's slugs, or nothing for the whole map.
     */
    function onboarding_chapter_categories(?int $chapter = null): array
    {
        $map = [
            1 => ['coaching_journey'],
            2 => ['approaches_tried', 'confident_areas'],
            3 => ['medical_diagnoses', 'family_medical_history', 'recent_clinical_symptoms', 'allergies_intolerances', 'hormonal_life_stage'],
            4 => ['eating_pattern', 'cultural_fasting_practices', 'food_env_who_cooks', 'food_env_support_level', 'food_env_meals_out_per_week', 'intake_fruit', 'intake_vegetables', 'intake_water', 'intake_caffeine', 'intake_sugary', 'substance_habits', 'digestive_health'],
            5 => ['sleep_night_waking', 'sleep_first_hour', 'activity_level', 'activity_daily_steps', 'activity_workouts_per_week', 'activity_available_days', 'stress_first_changes', 'readiness_score', 'consistency_interrupters'],
            6 => ['goal_mindset'],
        ];

        if ($chapter === null) {
            return $map;
        }

        return $map[$chapter] ?? [];
    }
}

if (!function_exists('notifyUser')) {
    /**
     * Store an in-app notification and push it to the user's devices.
     * $options: badge, action, data (array), dedupe_key (skip if already sent), push (bool, default true),
     *           store (bool, default true - false only pushes, e.g. chat messages)
     */
    function notifyUser($user, string $type, string $title, ?string $body = null, array $options = [])
    {
        $dedupeKey = $options['dedupe_key'] ?? null;
        if ($dedupeKey && AppNotification()->where('user_uuid', $user->uuid)->where('dedupe_key', $dedupeKey)->exists()) {
            return null;
        }

        $notification = AppNotification()->newInstance([
            'user_uuid'  => $user->uuid,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'badge'      => $options['badge'] ?? null,
            'action'     => $options['action'] ?? null,
            'data'       => $options['data'] ?? null,
            'dedupe_key' => $dedupeKey,
        ]);
        if ($options['store'] ?? true) {
            $notification->save();
        }

        if (($options['push'] ?? true) && $user->push_notification) {
            try {
                $tokens = UserFcmToken()->where('user_uuid', $user->uuid)->pluck('fcm_token')->toArray();
                // FCM data payload values must be strings
                $payload = collect(array_merge($options['data'] ?? [], [
                    'type'              => $type,
                    'action'            => $options['action'] ?? '',
                    'notification_uuid' => (string) $notification->uuid,
                ]))->map(fn ($v) => is_scalar($v) ? (string) $v : json_encode($v))->all();

                sendNotification($tokens, $title, (string) $body, $payload);
            } catch (\Throwable $e) {
                Log::error('Push notification failed: ' . $e->getMessage(), ['user_uuid' => $user->uuid, 'type' => $type]);
            }
        }

        return $notification;
    }
}

if (!function_exists('uploadFiles')) {
    /**
     * Upload many files to the public disk, returns the stored paths ("<folder>/<file>").
     */
    function uploadFiles($files, string $folderName): array
    {
        $paths = [];
        foreach ((array) $files as $file) {
            if ($file && ($path = uploadFile($file, $folderName))) {
                $paths[] = $path;
            }
        }

        return $paths;
    }
}

if (!function_exists('deleteFiles')) {
    function deleteFiles($paths): void
    {
        foreach (array_filter((array) $paths) as $path) {
            Storage::disk('public')->delete('uploads/' . $path);
        }
    }
}

if (!function_exists('fileUrls')) {
    function fileUrls($paths): array
    {
        return array_values(array_map(fn ($path) => getFileUrl($path), array_filter((array) $paths)));
    }
}

if (!function_exists('filesPayload')) {
    /**
     * Stored paths -> [{path, url}] (path is what the app sends back in remove_photos[]).
     */
    function filesPayload($paths): array
    {
        return array_values(array_map(
            fn ($path) => ['path' => $path, 'url' => getFileUrl($path)],
            array_filter((array) $paths)
        ));
    }
}
