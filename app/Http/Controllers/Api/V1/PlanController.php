<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserPlanResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Active plan, past plans and the pending renewal request (if any).
     */
    public function show()
    {
        $user = auth()->user();

        $plans = UserPlan()->with('pathway')->where('user_uuid', $user->uuid)->latest('end_date')->get();
        $active = $plans->firstWhere('status', 'Active');
        $pendingRenewal = PlanRenewalRequest()->where('user_uuid', $user->uuid)->where('status', 'Pending')->latest()->first();

        return jsonResponse(true, $active ? __('api.plan_fetched') : __('api.no_active_plan'), [
            'active_plan'     => $active ? new UserPlanResource($active) : null,
            'history'         => UserPlanResource::collection($plans->where('status', '!=', 'Active')->values()),
            'pending_renewal' => $pendingRenewal ? [
                'uuid'         => $pendingRenewal->uuid,
                'message'      => $pendingRenewal->message,
                'status'       => $pendingRenewal->status,
                'requested_at' => dateFormat($pendingRenewal->created_at),
            ] : null,
        ]);
    }

    /**
     * "Talk to Devanshi about renewing": records a renewal request and drops it into the coach chat.
     */
    public function requestRenewal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        if (PlanRenewalRequest()->where('user_uuid', $user->uuid)->where('status', 'Pending')->exists()) {
            return jsonResponse(false, __('api.renewal_already_requested'));
        }

        $plan = UserPlan()->active()->where('user_uuid', $user->uuid)->latest('end_date')->first()
            ?? UserPlan()->where('user_uuid', $user->uuid)->latest('end_date')->first();

        $renewal = PlanRenewalRequest()->create([
            'user_uuid'      => $user->uuid,
            'user_plan_uuid' => $plan?->uuid,
            'message'        => $request->message,
            'status'         => 'Pending',
        ]);

        ChatMessage()->create([
            'user_uuid'   => $user->uuid,
            'sender_type' => 'client',
            'sender_uuid' => $user->uuid,
            'message'     => $request->message ?: __('api.renewal_chat_message'),
        ]);

        activity_log(log: 'API Plan', event: 'renewal_requested', subject: $renewal, explicitCauser: $user);

        return jsonResponse(true, __('api.renewal_requested'), [
            'uuid'         => $renewal->uuid,
            'status'       => $renewal->status,
            'requested_at' => dateFormat($renewal->created_at),
        ]);
    }

    public function coachNotes(Request $request)
    {
        $notes = CoachNote()->with('coach')->where('user_uuid', auth()->user()->uuid)
            ->latest()->paginate((int) $request->input('per_page', 10));

        return jsonResponse(true, __('api.coach_notes_fetched'), [
            'notes' => collect($notes->items())->map(fn ($note) => [
                'uuid'       => $note->uuid,
                'note'       => $note->note,
                'coach_name' => $note->coach?->name,
                'created_at' => dateFormat($note->created_at),
            ]),
            'meta'  => [
                'current_page' => $notes->currentPage(),
                'last_page'    => $notes->lastPage(),
                'per_page'     => $notes->perPage(),
                'total'        => $notes->total(),
            ],
        ]);
    }
}
