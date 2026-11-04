<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * Shared date / status calculations for the client journey (dashboard, calendar, reminders).
 * All "today" values are in the client's timezone (users.timezone, falls back to app timezone).
 */
class Journey
{
    public const WEEK_DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public static function timezone($user): string
    {
        $timezone = $user->timezone;

        return $timezone && in_array($timezone, timezone_identifiers_list()) ? $timezone : config('app.timezone');
    }

    public static function now($user): CarbonImmutable
    {
        return CarbonImmutable::now(self::timezone($user));
    }

    public static function today($user): CarbonImmutable
    {
        return self::now($user)->startOfDay();
    }

    /**
     * Weekly check-in state for the current week (Monday - Sunday).
     * status: submitted | due (on/after the chosen day) | upcoming
     */
    public static function weeklyStatus($user): array
    {
        $today = self::today($user);
        $weekStart = $today->startOfWeek(CarbonImmutable::MONDAY);
        $weekEnd = $weekStart->addDays(6);

        $day = in_array($user->weekly_checkin_day, self::WEEK_DAYS) ? $user->weekly_checkin_day : 'sunday';
        $dueDate = $weekStart->addDays(array_search($day, self::WEEK_DAYS));

        $submitted = WeeklyCheckin()
            ->where('user_uuid', $user->uuid)
            ->whereBetween('record_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->latest('record_date')
            ->first();

        $status = $submitted ? 'submitted' : ($today->gte($dueDate) ? 'due' : 'upcoming');

        return [
            'status'         => $status,
            'is_due'         => $status === 'due',
            'is_submitted'   => (bool) $submitted,
            'submitted_uuid' => $submitted?->uuid,
            'due_day'        => $day,
            'due_date'       => $dueDate->toDateString(),
            'week_start'     => $weekStart->toDateString(),
            'week_end'       => $weekEnd->toDateString(),
        ];
    }

    public static function microStatus($user): array
    {
        $today = self::today($user)->toDateString();
        $checkin = DailyCheckin()->where('user_uuid', $user->uuid)->whereDate('record_date', $today)->first();

        return [
            'status'       => $checkin ? 'done' : 'due',
            'is_done'      => (bool) $checkin,
            'checkin_uuid' => $checkin?->uuid,
            'date'         => $today,
        ];
    }

    /**
     * 7-day baseline log progress ("Day 3 of 7").
     */
    public static function baselineStatus($user): array
    {
        $days = DailyCheckin()->where('user_uuid', $user->uuid)->whereNotNull('baseline_day')
            ->orderBy('baseline_day')->pluck('baseline_day')->unique()->values()->all();

        $next = null;
        foreach (range(1, 7) as $day) {
            if (!in_array($day, $days)) {
                $next = $day;
                break;
            }
        }

        return [
            'completed_days' => $days,
            'completed'      => count($days),
            'total'          => 7,
            'next_day'       => $next,
            'is_completed'   => count($days) >= 7,
        ];
    }

    /**
     * Weight entries from daily and weekly check-ins, oldest first. Weekly wins on the same date.
     */
    public static function weightSeries($user, ?string $from = null, ?string $to = null): array
    {
        $series = [];
        foreach ([['daily', DailyCheckin()], ['weekly', WeeklyCheckin()]] as [$source, $model]) {
            $query = $model->where('user_uuid', $user->uuid)->whereNotNull('weight');
            if ($from) {
                $query->whereDate('record_date', '>=', $from);
            }
            if ($to) {
                $query->whereDate('record_date', '<=', $to);
            }
            foreach ($query->get(['record_date', 'weight', 'weight_unit']) as $row) {
                $series[$row->record_date->toDateString()] = [
                    'date'   => $row->record_date->toDateString(),
                    'weight' => (float) $row->weight,
                    'unit'   => $row->weight_unit,
                    'source' => $source,
                ];
            }
        }
        ksort($series);

        return array_values($series);
    }

    /**
     * Latest weight with the change since the previous entry (positive = gained).
     */
    public static function weightOverview($user): ?array
    {
        $series = self::weightSeries($user);
        if (!$series) {
            return null;
        }

        $latest = end($series);
        $previous = count($series) > 1 ? $series[count($series) - 2] : null;
        $change = $previous && $previous['unit'] === $latest['unit'] ? round($latest['weight'] - $previous['weight'], 2) : null;

        return [
            'current'     => $latest['weight'],
            'unit'        => $latest['unit'],
            'recorded_on' => $latest['date'],
            'change'      => $change,
            'trend'       => is_null($change) ? null : ($change > 0 ? 'gained' : ($change < 0 ? 'lost' : 'same')),
        ];
    }

    public static function stepsFor($user, string $date): array
    {
        $sync = ActivitySync()->where('user_uuid', $user->uuid)->whereDate('activity_date', $date)->first();
        if ($sync) {
            return ['steps' => $sync->steps, 'source' => $sync->source, 'synced_at' => $sync->synced_at ? dateFormat($sync->synced_at) : null];
        }

        $checkin = DailyCheckin()->where('user_uuid', $user->uuid)->whereDate('record_date', $date)->whereNotNull('steps')->first();

        return ['steps' => $checkin?->steps, 'source' => $checkin ? 'manual' : null, 'synced_at' => null];
    }

    public static function greeting($user): string
    {
        $hour = self::now($user)->hour;

        return $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    }
}
