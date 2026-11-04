<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PathwayResource;
use App\Http\Resources\UserPlanResource;
use App\Support\Journey;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Home screen: greeting, plan validity, coach note, today's overview, check-in status, calendar strip.
     */
    public function index()
    {
        $user = auth()->user();
        $today = Journey::today($user);

        $plan = UserPlan()->with('pathway')->active()->where('user_uuid', $user->uuid)->latest('end_date')->first();
        $note = CoachNote()->where('user_uuid', $user->uuid)->latest()->first();
        $pathway = $user->pathway_uuid ? Pathway()->where('uuid', $user->pathway_uuid)->first() : null;
        $protocol = $pathway
            ? UserPathwayProtocol()->where('user_uuid', $user->uuid)->where('pathway_uuid', $pathway->uuid)->first()
            : null;

        // Calendar strip: current week, Monday - Sunday
        $weekStart = $today->startOfWeek();
        $checkinDates = DailyCheckin()->where('user_uuid', $user->uuid)
            ->whereBetween('record_date', [$weekStart->toDateString(), $weekStart->addDays(6)->toDateString()])
            ->pluck('record_date')->map(fn ($date) => $date->toDateString())->all();
        $strip = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->addDays($i);
            $strip[] = [
                'date'              => $day->toDateString(),
                'day'               => $day->format('D'),
                'day_number'        => $day->day,
                'is_today'          => $day->equalTo($today),
                'has_daily_checkin' => in_array($day->toDateString(), $checkinDates),
            ];
        }

        $steps = Journey::stepsFor($user, $today->toDateString());

        return jsonResponse(true, __('api.dashboard_fetched'), [
            'greeting'    => Journey::greeting($user),
            'name'        => $user->nickname ?: $user->name,
            'date'        => $today->toDateString(),
            'date_label'  => $today->format('l, F j'),
            'quote'       => setting('dashboard_quote', 'Small, grounded rituals build sustainable nourishment.'),
            'plan'        => $plan ? new UserPlanResource($plan) : null,
            'renewal_requested' => PlanRenewalRequest()->where('user_uuid', $user->uuid)->where('status', 'Pending')->exists(),
            'coach_note'  => $note ? ['uuid' => $note->uuid, 'note' => $note->note, 'created_at' => dateFormat($note->created_at)] : null,
            'today_overview' => [
                'steps'               => $steps['steps'],
                'steps_source'        => $steps['source'],
                'steps_synced_at'     => $steps['synced_at'],
                'is_health_connected' => (bool) $user->is_health_connected,
                'health_source'       => $user->health_source,
                'weight'              => Journey::weightOverview($user),
            ],
            'checkins' => [
                'micro'    => Journey::microStatus($user),
                'weekly'   => Journey::weeklyStatus($user),
                'baseline' => Journey::baselineStatus($user),
            ],
            'calendar_strip' => $strip,
            'onboarding'     => [
                'is_completed'       => (bool) $user->is_onboarding_completed,
                'completed_chapters' => UserOnboardingChapter()->where('user_uuid', $user->uuid)->where('status', 'completed')->count(),
                'total_chapters'     => count(config('onboarding.chapters')),
                'pathway'            => $pathway ? new PathwayResource($pathway) : null,
                'protocol_status'    => $pathway?->protocol_key ? ($protocol?->status ?? 'pending') : null,
            ],
            'is_home_tour_completed' => (bool) $user->is_home_tour_completed,
            'unread_notifications'   => AppNotification()->unread()->where('user_uuid', $user->uuid)->count(),
            'unread_chat_messages'   => ChatMessage()->unread()->where('user_uuid', $user->uuid)->where('sender_type', '!=', 'client')->count(),
        ]);
    }

    public function completeHomeTour()
    {
        $user = auth()->user();
        $user->update(['is_home_tour_completed' => true]);

        activity_log(log: 'API Dashboard', event: 'home_tour_completed', subject: $user);

        return jsonResponse(true, __('api.home_tour_completed'));
    }
}
