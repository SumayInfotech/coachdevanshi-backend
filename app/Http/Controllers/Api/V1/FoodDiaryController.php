<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\HandlesTrackingInput;
use App\Http\Controllers\Controller;
use App\Http\Resources\FoodDiaryEntryResource;
use App\Support\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Three-day food diary: two weekdays + one weekend day.
 */
class FoodDiaryController extends Controller
{
    use HandlesTrackingInput;

    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function index()
    {
        $entries = FoodDiaryEntry()->where('user_uuid', auth()->user()->uuid)->get()->keyBy('day_number');

        $days = [];
        foreach (config('tracking.food_diary_days') as $number => $day) {
            $entry = $entries[$number] ?? null;
            $days[] = [
                'day_number'   => $number,
                'label'        => $day['label'],
                'day_type'     => $day['day_type'],
                'is_completed' => (bool) $entry,
                'entry'        => $entry ? new FoodDiaryEntryResource($entry) : null,
            ];
        }

        return jsonResponse(true, __('api.food_diary_fetched'), [
            'days'         => $days,
            'completed'    => $entries->count(),
            'total'        => count($days),
            'is_completed' => $entries->count() >= count($days),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $this->decodeJsonInputs($request, ['meals', 'sensations']);
        if (is_string($request->sensations)) {
            $request->merge(['sensations' => array_filter(array_map('trim', explode(',', $request->sensations)))]);
        }

        $validator = Validator::make($request->all(), [
            'day_number'    => ['required', Rule::in(array_keys(config('tracking.food_diary_days')))],
            'record_date'   => 'required|date|before_or_equal:' . Journey::today($user)->addDay()->toDateString(),
            'meals'         => 'required|array',
            'meals.*'       => 'array',
            'meals.*.time'  => 'nullable|string|max:20',
            'meals.*.items' => 'nullable|string|max:2000',
            'sensations'    => 'nullable|array',
            'sensations.*'  => Rule::in(array_keys(config('tracking.food_sensations'))),
            'context_notes' => 'nullable|string|max:2000',
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }
        if (array_diff(array_keys($request->meals), array_keys(config('tracking.meal_slots')))) {
            return jsonResponse(false, __('api.invalid_option_selected', ['field' => 'meals']));
        }

        $day = (int) $request->day_number;
        $entry = FoodDiaryEntry()->updateOrCreate(
            ['user_uuid' => $user->uuid, 'day_number' => $day],
            [
                'day_type'      => config('tracking.food_diary_days.' . $day . '.day_type'),
                'record_date'   => date('Y-m-d', strtotime($request->record_date)),
                'meals'         => $request->meals,
                'sensations'    => array_values($request->sensations ?? []),
                'context_notes' => $request->context_notes,
            ]
        );

        activity_log(log: 'API Food Diary', event: 'food_diary_saved', subject: $entry, properties: ['day_number' => $day]);

        return jsonResponse(true, __('api.food_diary_saved'), new FoodDiaryEntryResource($entry));
    }
}
