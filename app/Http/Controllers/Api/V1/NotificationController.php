<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppNotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $notifications = AppNotification()->where('user_uuid', $user->uuid)
            ->when(filter_var($request->input('unread_only', false), FILTER_VALIDATE_BOOLEAN), fn ($q) => $q->whereNull('read_at'))
            ->latest()
            ->paginate((int) $request->input('per_page', 20));

        $data = paginatedResponse($notifications, AppNotificationResource::class, 'notifications');
        $data['unread_count'] = AppNotification()->unread()->where('user_uuid', $user->uuid)->count();

        return jsonResponse(true, __('api.notifications_fetched'), $data);
    }

    public function show($uuid)
    {
        $notification = AppNotification()->where('user_uuid', auth()->user()->uuid)->where('uuid', $uuid)->first();
        if (!$notification) {
            return jsonResponse(false, __('api.record_not_found'), null, 404);
        }
        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return jsonResponse(true, __('api.notifications_fetched'), new AppNotificationResource($notification));
    }

    public function read($uuid)
    {
        $notification = AppNotification()->where('user_uuid', auth()->user()->uuid)->where('uuid', $uuid)->first();
        if (!$notification) {
            return jsonResponse(false, __('api.record_not_found'), null, 404);
        }
        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return jsonResponse(true, __('api.notification_read'));
    }

    public function readAll()
    {
        AppNotification()->unread()->where('user_uuid', auth()->user()->uuid)->update(['read_at' => now()]);

        return jsonResponse(true, __('api.notifications_read_all'));
    }

    /**
     * Register / refresh the device FCM token (also accepted on login & OTP verify).
     */
    public function saveFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token'   => 'required|string|max:500',
            'device_type' => 'nullable|string|max:20',
            'device_name' => 'nullable|string|max:100',
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        storeUserFcmToken(auth()->user(), $request);

        return jsonResponse(true, __('api.fcm_token_saved'));
    }
}
