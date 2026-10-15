<?php
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \App\Helpers\FlangeHelper;
use Twilio\Rest\Client;
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
    function sendOtp(string $phoneNumber)
    {
        // echo $phoneNumber;die;
        try {
            $sid    = env('TWILIO_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $verifySid = env('TWILIO_VERIFY_SID');
            $twilio = new Client($sid, $token);

            $verification = $twilio->verify->v2
                ->services($verifySid)
                ->verifications
                ->create($phoneNumber, "sms");

            return [
                'success' => true,
                'sid'     => $verification->sid,
                'status'  => $verification->status,
            ];
        } catch (\Exception $e) {
            \Log::error("Twilio OTP Send Error", [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

if (!function_exists('verifyOtp')) {
    function verifyOtp(string $phoneNumber, string $code)
    {
        try {
            $sid    = env('TWILIO_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $verifySid = env('TWILIO_VERIFY_SID');
            $twilio = new Client($sid, $token);

            $verificationCheck = $twilio->verify->v2
                ->services($verifySid)
                ->verificationChecks
                ->create([
                    "to" => $phoneNumber,
                    "code" => $code
                ]);
                // echo "<pre>";print_r($verificationCheck);"</pre>";die;
            if ($verificationCheck->status === "approved") {
                return [
                    'success' => true,
                    'message' => 'OTP verified successfully',
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ];
        } catch (\Exception $e) {
            Log::error("Twilio Verify OTP Check Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

if (!function_exists('sendNotification')) {
    function sendNotification($tokens, $title, $body, $data = [])
    {
        if (empty($tokens)) {
            return false;
        }
        
        // Normalize to array
        if (!is_array($tokens)) {
            $tokens = [$tokens];
        }
        // Path to your Firebase service account JSON file
        $credentialsPath = base_path('2m1k-213e7-firebase-adminsdk-fbsvc-f45dd7b09b.json');

        // Generate an OAuth2 access token
        $accessToken = getAccessToken($credentialsPath);
        // Firebase Cloud Messaging API URL
        $url = 'https://fcm.googleapis.com/v1/projects/men1kitchen/messages:send';
        $responses  = [];
        foreach ($tokens as $token) {

            if (!$token) {
                continue;
            }

            // Prepare the message payload
            $message = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'data' => $data,
                ]
            ];

            // Set up the HTTP headers
            $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ];

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

            // Execute the request
            $response = curl_exec($ch);
            // Check for errors
            if ($response === FALSE) {
                //die('Curl failed: ' . curl_error($ch));
                \Log::error('FCM Curl Error', [
                    'error' => curl_error($ch),
                    'token' => $token
                ]);
            }else {
                $responses[] = json_decode($response, true);
            }

            // Close cURL
            curl_close($ch);
        }
        return $responses;
    }
}

if (!function_exists('getAccessToken')) {
    function getAccessToken($credentialsPath)
    {
        
        // Read the service account JSON file
        $credentials = json_decode(file_get_contents($credentialsPath), true);

        // Define the OAuth2 token URL
        $url = 'https://oauth2.googleapis.com/token';

        // Prepare the token request payload
        $jwt = createJWT($credentials);

        $data = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        // Execute the request
        $response = curl_exec($ch);
        // Check for errors
        if ($response === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close cURL
        curl_close($ch);

        // Parse the response
        $responseData = json_decode($response, true);

        // Return the access token
        return $responseData['access_token'];
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