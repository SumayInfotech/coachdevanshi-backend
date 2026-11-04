<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgressController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Progress tab: weight graph, water intake, steps, sleep and measurement history.
     * ?range=7d|30d|90d|all (default 30d)
     */
    public function summary(Request $request)
    {
        $validator = Validator::make($request->all(), ['range' => 'nullable|in:7d,30d,90d,all']);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        $range = $request->input('range', '30d');
        $to = Journey::today($user);
        $from = $range === 'all' ? null : $to->subDays((int) $range - 1)->toDateString();
        $toDate = $to->toDateString();

        $daily = DailyCheckin()->where('user_uuid', $user->uuid)
            ->when($from, fn ($q) => $q->whereDate('record_date', '>=', $from))
            ->whereDate('record_date', '<=', $toDate)
            ->orderBy('record_date')
            ->get(['record_date', 'water_intake', 'water_unit', 'sleep_hours', 'sleep_quality', 'steps']);

        $synced = ActivitySync()->where('user_uuid', $user->uuid)
            ->when($from, fn ($q) => $q->whereDate('activity_date', '>=', $from))
            ->whereDate('activity_date', '<=', $toDate)
            ->get(['activity_date', 'steps'])
            ->keyBy(fn ($row) => $row->activity_date->toDateString());

        // Synced steps win over manually entered ones
        $steps = $daily->whereNotNull('steps')->mapWithKeys(fn ($row) => [$row->record_date->toDateString() => $row->steps])->all();
        foreach ($synced as $date => $row) {
            $steps[$date] = $row->steps;
        }
        ksort($steps);

        $weekly = WeeklyCheckin()->where('user_uuid', $user->uuid)
            ->when($from, fn ($q) => $q->whereDate('record_date', '>=', $from))
            ->whereDate('record_date', '<=', $toDate)
            ->orderBy('record_date')
            ->get();

        $weights = Journey::weightSeries($user, $from, $toDate);
        $allWeights = Journey::weightSeries($user);
        $baseline = $allWeights[0] ?? null;
        $latest = $allWeights ? end($allWeights) : null;

        $baselineMeasure = WeeklyCheckin()->where('user_uuid', $user->uuid)->orderByDesc('is_baseline')->orderBy('record_date')->first();
        $latestMeasure = WeeklyCheckin()->where('user_uuid', $user->uuid)->latest('record_date')->first();
        $measurementChange = [];
        foreach (array_keys(config('tracking.measurements')) as $key) {
            $measurementChange[$key] = ($baselineMeasure?->{$key} && $latestMeasure?->{$key} && $baselineMeasure->measurement_unit === $latestMeasure->measurement_unit)
                ? round($latestMeasure->{$key} - $baselineMeasure->{$key}, 2)
                : null;
        }

        return jsonResponse(true, __('api.progress_fetched'), [
            'range'   => $range,
            'from'    => $from,
            'to'      => $toDate,
            'weight'  => [
                'series'   => $weights,
                'baseline' => $baseline,
                'current'  => $latest,
                'change'   => ($baseline && $latest && $baseline['unit'] === $latest['unit']) ? round($latest['weight'] - $baseline['weight'], 2) : null,
            ],
            'water'   => $daily->whereNotNull('water_intake')->map(fn ($row) => [
                'date' => $row->record_date->toDateString(), 'value' => (float) $row->water_intake, 'unit' => $row->water_unit,
            ])->values(),
            'steps'   => [
                'series'  => collect($steps)->map(fn ($value, $date) => ['date' => $date, 'steps' => (int) $value])->values(),
                'average' => $steps ? (int) round(array_sum($steps) / count($steps)) : null,
            ],
            'sleep'   => $daily->whereNotNull('sleep_hours')->map(fn ($row) => [
                'date' => $row->record_date->toDateString(), 'hours' => (float) $row->sleep_hours, 'quality' => $row->sleep_quality,
            ])->values(),
            'measurements' => [
                'series' => $weekly->map(fn ($row) => [
                    'date'    => $row->record_date->toDateString(),
                    'unit'    => $row->measurement_unit,
                    'chest'   => $row->chest,
                    'abdomen' => $row->abdomen,
                    'waist'   => $row->waist,
                    'hip'     => $row->hip,
                    'thigh'   => $row->thigh,
                    'arm'     => $row->arm,
                ])->values(),
                'change_since_baseline' => $measurementChange,
                'unit' => $latestMeasure?->measurement_unit,
            ],
            'counts' => [
                'daily_checkins'  => $daily->count(),
                'weekly_checkins' => $weekly->count(),
            ],
        ]);
    }
}
