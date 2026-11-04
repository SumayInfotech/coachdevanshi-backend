<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Client <-> coach conversation (one thread per client). The app polls / refreshes on push.
 */
class ChatController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Newest first. ?after_uuid= returns only messages newer than that message (for polling).
     * Opening the thread marks coach & system messages as read.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ChatMessage()->with('sender')->where('user_uuid', $user->uuid);

        if ($request->filled('after_uuid')) {
            $after = ChatMessage()->where('user_uuid', $user->uuid)->where('uuid', $request->after_uuid)->first();
            if ($after) {
                $query->where('id', '>', $after->id);
            }
        }

        $messages = $query->orderByDesc('id')->paginate((int) $request->input('per_page', 30));

        ChatMessage()->unread()->where('user_uuid', $user->uuid)->where('sender_type', '!=', 'client')->update(['read_at' => now()]);

        return jsonResponse(true, __('api.chat_messages_fetched'), paginatedResponse($messages, ChatMessageResource::class, 'messages'));
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message'       => 'nullable|string|max:5000',
            'attachments'   => 'nullable|array|max:' . config('tracking.max_photos_per_entry'),
            'attachments.*' => 'file|mimes:' . config('tracking.photo_mimes') . '|max:' . config('tracking.photo_max_kb'),
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }
        if (!$request->filled('message') && !$request->hasFile('attachments')) {
            return jsonResponse(false, __('api.chat_message_required'));
        }

        $user = auth()->user();
        $message = ChatMessage()->create([
            'user_uuid'   => $user->uuid,
            'sender_type' => 'client',
            'sender_uuid' => $user->uuid,
            'message'     => $request->message,
            'attachments' => $request->hasFile('attachments') ? uploadFiles($request->file('attachments'), 'chat') : null,
        ]);

        return jsonResponse(true, __('api.chat_message_sent'), new ChatMessageResource($message->load('sender')));
    }

    public function unreadCount()
    {
        $count = ChatMessage()->unread()->where('user_uuid', auth()->user()->uuid)->where('sender_type', '!=', 'client')->count();

        return jsonResponse(true, __('api.chat_messages_fetched'), ['unread_count' => $count]);
    }
}
