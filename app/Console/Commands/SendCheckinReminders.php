<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Journey;
use Illuminate\Console\Command;

/**
 * Background reminders from the customer journey (runs hourly, every notification is de-duplicated):
 *  - micro check-in:    no daily check-in for X days (config tracking.micro_reminder_after_days), sent in the evening
 *  - weekly check-in:   on the client's chosen day, plus an evening follow-up if still not submitted
 *  - protocol pending:  pathway selected but its protocol questionnaire not submitted (once a day)
 *  - plan validity:     14 / 7 / 1 days before the plan end date; past plans are marked Expired
 */
class SendCheckinReminders extends Command
{
    protected $signature = 'app:send-checkin-reminders';

    protected $description = 'Send micro / weekly check-in, protocol and plan validity reminders to clients';

    public function handle(): int
    {
        // Close plans that have ended
        $expired = UserPlan()->active()->whereDate('end_date', '<', now()->toDateString())->update(['status' => 'Expired']);
        if ($expired) {
            $this->info("Marked {$expired} plan(s) as expired.");
        }

        $sent = 0;
        User::customer()->active()->where('is_signup', true)->chunkById(200, function ($users) use (&$sent) {
            foreach ($users as $user) {
                $sent += $this->remind($user);
            }
        });

        $this->info("Reminders sent: {$sent}");

        return self::SUCCESS;
    }

    protected function remind(User $user): int
    {
        $sent = 0;
        $now = Journey::now($user);
        $today = $now->startOfDay();
        $lang = $user->language ?: 'en';

        // Plan validity
        $plan = UserPlan()->active()->where('user_uuid', $user->uuid)->latest('end_date')->first();
        if ($plan && in_array($plan->days_left, config('tracking.plan_expiry_reminder_days'))) {
            $sent += (int) (bool) notifyUser($user, 'plan_expiry',
                __('api.reminder_plan_title', ['days' => $plan->days_left], $lang),
                __('api.reminder_plan_body', [], $lang),
                [
                    'badge'      => __('api.badge_days_left', ['days' => $plan->days_left], $lang),
                    'action'     => 'plan_renewal',
                    'data'       => ['plan_uuid' => $plan->uuid, 'days_left' => $plan->days_left],
                    'dedupe_key' => "plan_expiry:{$plan->uuid}:{$plan->days_left}",
                ]
            );
        }

        if (!$user->is_onboarding_completed) {
            return $sent;
        }

        // Pathway protocol still pending
        if ($user->pathway_uuid && $now->hour >= (int) config('tracking.weekly_reminder_hour')) {
            $pathway = Pathway()->where('uuid', $user->pathway_uuid)->first();
            $submitted = UserPathwayProtocol()->where('user_uuid', $user->uuid)->where('pathway_uuid', $user->pathway_uuid)->where('status', 'submitted')->exists();
            if ($pathway && $pathway->protocol_key && !$submitted) {
                $sent += (int) (bool) notifyUser($user, 'protocol_pending',
                    __('api.reminder_protocol_title', ['pathway' => $pathway->title], $lang),
                    null,
                    [
                        'badge'      => __('api.badge_due_today', [], $lang),
                        'action'     => 'pathway_protocol',
                        'data'       => ['pathway_uuid' => $pathway->uuid],
                        'dedupe_key' => 'protocol_pending:' . $pathway->uuid . ':' . $today->toDateString(),
                    ]
                );
            }
        }

        // Micro check-in
        if ($now->hour >= (int) config('tracking.micro_reminder_hour')) {
            $last = DailyCheckin()->where('user_uuid', $user->uuid)->max('record_date');
            $missingDays = $last ? (int) \Carbon\Carbon::parse($last)->startOfDay()->diffInDays($today->toDateString()) : PHP_INT_MAX;
            if ($missingDays >= (int) config('tracking.micro_reminder_after_days')) {
                $sent += (int) (bool) notifyUser($user, 'micro_checkin_reminder',
                    __('api.reminder_micro_title', [], $lang),
                    __('api.reminder_micro_body', [], $lang),
                    [
                        'badge'      => __('api.badge_due_today', [], $lang),
                        'action'     => 'micro_checkin',
                        'data'       => ['date' => $today->toDateString()],
                        'dedupe_key' => 'micro:' . $today->toDateString(),
                    ]
                );
            }
        }

        // Weekly check-in (on the chosen day)
        $weekly = Journey::weeklyStatus($user);
        if ($weekly['status'] === 'due' && $weekly['due_date'] === $today->toDateString()) {
            $badge = __('api.badge_due_on', ['day' => ucfirst($weekly['due_day'])], $lang);

            // Evening follow-up replaces the morning reminder when the job first runs late in the day
            if ($now->hour >= (int) config('tracking.weekly_followup_hour')) {
                $sent += (int) (bool) notifyUser($user, 'weekly_checkin_reminder',
                    __('api.reminder_weekly_followup_title', [], $lang),
                    __('api.reminder_weekly_followup_body', [], $lang),
                    ['badge' => $badge, 'action' => 'weekly_checkin', 'data' => ['week_start' => $weekly['week_start']], 'dedupe_key' => 'weekly_followup:' . $weekly['week_start']]
                );
            } elseif ($now->hour >= (int) config('tracking.weekly_reminder_hour')) {
                $sent += (int) (bool) notifyUser($user, 'weekly_checkin_reminder',
                    __('api.reminder_weekly_title', [], $lang),
                    __('api.reminder_weekly_body', [], $lang),
                    ['badge' => $badge, 'action' => 'weekly_checkin', 'data' => ['week_start' => $weekly['week_start']], 'dedupe_key' => 'weekly:' . $weekly['week_start']]
                );
            }
        }

        return $sent;
    }
}
