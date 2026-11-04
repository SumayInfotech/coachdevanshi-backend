<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodReport;
use App\Models\ChatMessage;
use App\Models\DailyCheckin;
use App\Models\Pathway;
use App\Models\PlanRenewalRequest;
use App\Models\ProgressPhoto;
use App\Models\User;
use App\Models\UserOnboardingChapter;
use App\Models\UserPathwayProtocol;
use App\Models\UserPlan;
use App\Models\WeeklyCheckin;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Admin home: client, plan, onboarding and tracking analytics.
 * All grouping is done in PHP or on plain date columns so it runs on MySQL and SQLite alike.
 */
class DashboardController extends Controller
{
    /** A client on an active plan with no daily check-in for this many days needs attention. */
    private const INACTIVE_DAYS = 3;

    /** Plans ending within this many days count as "expiring soon". */
    private const EXPIRING_DAYS = 14;

    public function index()
    {
        $today = Carbon::today();
        $customerUuids = User::customer()->pluck('uuid');

        $activePlans = UserPlan::active()->whereDate('end_date', '>=', $today)->get();
        $activeClientUuids = $activePlans->pluck('user_uuid')->unique()->values();

        return view('admin.dashboard.index', [
            'canViewClients' => hasPermission('view_client'),
            'kpis' => $this->kpis($today, $customerUuids, $activePlans, $activeClientUuids),
            'charts' => [
                'signups' => $this->signupSeries($today),
                'activity' => $this->activitySeries($today, 30),
                'pathways' => $this->pathwaySplit(),
                'funnel' => $this->funnel($customerUuids, $activeClientUuids),
                'plans' => $this->planSplit($today),
                'wellness' => $this->wellnessSeries($today, 30),
            ],
            'wellness' => $this->wellnessSnapshot($today, 30),
            'attention' => $this->needsAttention($today, $activePlans),
            'expiringPlans' => $this->expiringPlans($today),
            'renewals' => $this->pendingRenewals(),
            'messages' => $this->unreadMessages(),
            'recentClients' => User::customer()->latest()->limit(6)->get(),
        ]);
    }

    private function kpis(Carbon $today, Collection $customerUuids, Collection $activePlans, Collection $activeClientUuids): array
    {
        $monthStart = $today->copy()->startOfMonth();
        $lastMonthStart = $monthStart->copy()->subMonthNoOverflow();
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);

        $totalClients = $customerUuids->count();
        $newThisMonth = User::customer()->where('created_at', '>=', $monthStart)->count();
        $newLastMonth = User::customer()->whereBetween('created_at', [$lastMonthStart, $monthStart])->count();

        $onboarded = User::customer()->where('is_onboarding_completed', true)->count();

        $loggedLast7 = DailyCheckin::whereIn('user_uuid', $activeClientUuids)
            ->whereDate('record_date', '>=', $today->copy()->subDays(6))
            ->distinct()->count('user_uuid');

        return [
            'total_clients' => $totalClients,
            'active_clients' => User::customer()->where('status', 'Active')->count(),
            'new_this_month' => $newThisMonth,
            'growth' => $this->growth($newThisMonth, $newLastMonth),
            'active_plans' => $activePlans->count(),
            'expiring_soon' => $activePlans->filter(fn ($p) => $p->end_date->lte($today->copy()->addDays(self::EXPIRING_DAYS)))->count(),
            'onboarded' => $onboarded,
            'onboarded_rate' => $this->percent($onboarded, $totalClients),
            'checkins_today' => DailyCheckin::whereDate('record_date', $today)->count(),
            'weekly_this_week' => WeeklyCheckin::whereDate('record_date', '>=', $weekStart)->whereDate('record_date', '<=', $today)->count(),
            'pending_renewals' => PlanRenewalRequest::where('status', 'Pending')->count(),
            'unread_messages' => ChatMessage::where('sender_type', 'client')->unread()->count(),
            'unread_threads' => ChatMessage::where('sender_type', 'client')->unread()->distinct()->count('user_uuid'),
            'health_connected' => User::customer()->where('is_health_connected', true)->count(),
            'health_rate' => $this->percent(User::customer()->where('is_health_connected', true)->count(), $totalClients),
            'engagement_rate' => $this->percent($loggedLast7, $activeClientUuids->count()),
            'engaged_clients' => $loggedLast7,
            'active_plan_clients' => $activeClientUuids->count(),
            'photos_total' => ProgressPhoto::count(),
            'reports_total' => BloodReport::count(),
        ];
    }

    /** New clients and plans started per month for the last 12 months. */
    private function signupSeries(Carbon $today): array
    {
        $start = $today->copy()->startOfMonth()->subMonths(11);
        $months = collect(range(0, 11))->map(fn ($i) => $start->copy()->addMonths($i));

        $clients = User::customer()->where('created_at', '>=', $start)->pluck('created_at')
            ->countBy(fn ($d) => Carbon::parse($d)->format('Y-m'));
        $plans = UserPlan::whereDate('start_date', '>=', $start)->pluck('start_date')
            ->countBy(fn ($d) => Carbon::parse($d)->format('Y-m'));

        return [
            'labels' => $months->map(fn ($m) => $m->format('M y'))->all(),
            'clients' => $months->map(fn ($m) => $clients[$m->format('Y-m')] ?? 0)->all(),
            'plans' => $months->map(fn ($m) => $plans[$m->format('Y-m')] ?? 0)->all(),
        ];
    }

    /** Daily and weekly check-ins plus progress photo uploads per day. */
    private function activitySeries(Carbon $today, int $days): array
    {
        $start = $today->copy()->subDays($days - 1);
        $range = collect(range(0, $days - 1))->map(fn ($i) => $start->copy()->addDays($i));

        $count = fn ($model) => $model::whereDate('record_date', '>=', $start)->pluck('record_date')
            ->countBy(fn ($d) => Carbon::parse($d)->toDateString());

        $daily = $count(DailyCheckin::class);
        $weekly = $count(WeeklyCheckin::class);
        $photos = $count(ProgressPhoto::class);

        return [
            'labels' => $range->map(fn ($d) => $d->format('d M'))->all(),
            'daily' => $range->map(fn ($d) => $daily[$d->toDateString()] ?? 0)->all(),
            'weekly' => $range->map(fn ($d) => $weekly[$d->toDateString()] ?? 0)->all(),
            'photos' => $range->map(fn ($d) => $photos[$d->toDateString()] ?? 0)->all(),
        ];
    }

    /** Clients per selected pathway, plus those who have not picked one yet. */
    private function pathwaySplit(): array
    {
        $counts = User::customer()->whereNotNull('pathway_uuid')->pluck('pathway_uuid')->countBy();
        $titles = Pathway::orderBy('sort_order')->pluck('title', 'uuid');

        $rows = $titles->map(fn ($title, $uuid) => ['label' => $title, 'value' => $counts[$uuid] ?? 0])
            ->filter(fn ($row) => $row['value'] > 0)
            ->sortByDesc('value')->values();

        $none = User::customer()->whereNull('pathway_uuid')->count();
        if ($none > 0) {
            $rows->push(['label' => 'Not selected', 'value' => $none]);
        }

        return ['labels' => $rows->pluck('label')->all(), 'values' => $rows->pluck('value')->all()];
    }

    /** Client journey from signup to an active plan. */
    private function funnel(Collection $customerUuids, Collection $activeClientUuids): array
    {
        $steps = [
            'Signed up' => $customerUuids->count(),
            'Started onboarding' => UserOnboardingChapter::whereIn('user_uuid', $customerUuids)->distinct()->count('user_uuid'),
            'Onboarding done' => User::customer()->where('is_onboarding_completed', true)->count(),
            'Pathway selected' => User::customer()->whereNotNull('pathway_uuid')->count(),
            'Protocol submitted' => UserPathwayProtocol::whereIn('user_uuid', $customerUuids)->where('status', 'submitted')->distinct()->count('user_uuid'),
            'Active plan' => $activeClientUuids->intersect($customerUuids)->count(),
        ];

        return ['labels' => array_keys($steps), 'values' => array_values($steps)];
    }

    private function planSplit(Carbon $today): array
    {
        $soon = $today->copy()->addDays(self::EXPIRING_DAYS);
        $plans = UserPlan::get(['status', 'end_date']);

        $active = $plans->filter(fn ($p) => $p->status === 'Active' && $p->end_date->gte($today));

        return [
            'labels' => ['Active', 'Expiring ≤ '.self::EXPIRING_DAYS.' days', 'Expired', 'Cancelled'],
            'values' => [
                $active->filter(fn ($p) => $p->end_date->gt($soon))->count(),
                $active->filter(fn ($p) => $p->end_date->lte($soon))->count(),
                $plans->filter(fn ($p) => $p->status === 'Expired' || ($p->status === 'Active' && $p->end_date->lt($today)))->count(),
                $plans->where('status', 'Cancelled')->count(),
            ],
        ];
    }

    /** Average mood and energy (1–5) across all daily check-ins per day. */
    private function wellnessSeries(Carbon $today, int $days): array
    {
        $start = $today->copy()->subDays($days - 1);
        $rows = DailyCheckin::whereDate('record_date', '>=', $start)->get(['record_date', 'mood', 'energy'])
            ->groupBy(fn ($r) => $r->record_date->toDateString());

        $range = collect(range(0, $days - 1))->map(fn ($i) => $start->copy()->addDays($i));
        $avg = function ($date, $field) use ($rows) {
            $value = $rows->get($date, collect())->whereNotNull($field)->avg($field);

            return $value !== null ? round($value, 1) : null;
        };

        return [
            'labels' => $range->map(fn ($d) => $d->format('d M'))->all(),
            'mood' => $range->map(fn ($d) => $avg($d->toDateString(), 'mood'))->all(),
            'energy' => $range->map(fn ($d) => $avg($d->toDateString(), 'energy'))->all(),
        ];
    }

    private function wellnessSnapshot(Carbon $today, int $days): array
    {
        $rows = DailyCheckin::whereDate('record_date', '>=', $today->copy()->subDays($days - 1))
            ->get(['mood', 'energy', 'sleep_hours', 'water_goal_met', 'movement_done', 'meditation_done']);

        $rate = function ($field) use ($rows) {
            $answered = $rows->whereNotNull($field);

            return $answered->count() ? $this->percent($answered->filter(fn ($r) => (bool) $r->$field)->count(), $answered->count()) : null;
        };
        $avg = fn ($field) => ($v = $rows->whereNotNull($field)->avg($field)) !== null ? round($v, 1) : null;

        return [
            'entries' => $rows->count(),
            'mood' => $avg('mood'),
            'energy' => $avg('energy'),
            'sleep' => $avg('sleep_hours'),
            'water' => $rate('water_goal_met'),
            'movement' => $rate('movement_done'),
            'meditation' => $rate('meditation_done'),
        ];
    }

    /** Clients on an active plan who have not logged a daily check-in recently. */
    private function needsAttention(Carbon $today, Collection $activePlans): Collection
    {
        $uuids = $activePlans->pluck('user_uuid')->unique();
        if ($uuids->isEmpty()) {
            return collect();
        }

        $lastLog = DailyCheckin::whereIn('user_uuid', $uuids)
            ->selectRaw('user_uuid, MAX(record_date) as last_date')
            ->groupBy('user_uuid')
            ->pluck('last_date', 'user_uuid');

        $cutoff = $today->copy()->subDays(self::INACTIVE_DAYS);

        $quiet = $uuids->filter(fn ($uuid) => ! isset($lastLog[$uuid]) || Carbon::parse($lastLog[$uuid])->lt($cutoff));
        $users = User::whereIn('uuid', $quiet)->get()->keyBy('uuid');

        return $quiet->map(function ($uuid) use ($users, $lastLog, $today) {
            $last = isset($lastLog[$uuid]) ? Carbon::parse($lastLog[$uuid]) : null;

            return [
                'user' => $users->get($uuid),
                'last_date' => $last,
                'days' => $last ? (int) $last->diffInDays($today) : null,
            ];
        })->filter(fn ($row) => $row['user'])
            ->sortByDesc(fn ($row) => $row['days'] ?? PHP_INT_MAX)
            ->take(6)->values();
    }

    private function expiringPlans(Carbon $today): Collection
    {
        return UserPlan::active()->with('user')
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $today->copy()->addDays(self::EXPIRING_DAYS))
            ->orderBy('end_date')->limit(6)->get();
    }

    private function pendingRenewals(): Collection
    {
        return PlanRenewalRequest::with(['user', 'plan'])->where('status', 'Pending')->latest()->limit(6)->get();
    }

    /** Latest unread client message per conversation. */
    private function unreadMessages(): Collection
    {
        $unread = ChatMessage::where('sender_type', 'client')->unread()->latest()->limit(100)->get();
        $counts = $unread->countBy('user_uuid');
        $latest = $unread->unique('user_uuid')->take(6);
        $users = User::whereIn('uuid', $latest->pluck('user_uuid'))->get()->keyBy('uuid');

        return $latest->map(fn ($m) => [
            'message' => $m,
            'user' => $users->get($m->user_uuid),
            'count' => $counts[$m->user_uuid] ?? 1,
        ])->filter(fn ($row) => $row['user'])->values();
    }

    private function percent(int $part, int $whole): int
    {
        return $whole > 0 ? (int) round($part * 100 / $whole) : 0;
    }

    private function growth(int $current, int $previous): ?int
    {
        if ($previous === 0) {
            return $current > 0 ? null : 0;
        }

        return (int) round(($current - $previous) * 100 / $previous);
    }
}
