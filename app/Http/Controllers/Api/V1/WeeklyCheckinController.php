<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\HandlesTrackingInput;
use App\Http\Controllers\Controller;
use App\Http\Resources\WeeklyCheckinResource;
use App\Support\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Baseline & progress measurements + weekly reflections (one entry per record_date).
 */
class WeeklyCheckinController extends Controller
{
    use HandlesTrackingInput;

    protected const FIELDS = [
        'weight', 'weight_unit', 'chest', 'abdomen', 'waist', 'hip', 'thigh', 'arm', 'measurement_unit',
        'self_confidence', 'last_period_start', 'cycle_day', 'cycle_notes', 'cravings_level', 'cravings_notes',
        'travelled', 'travel_notes', 'wins', 'proud_moments', 'struggles', 'notes', 'extra',
    ];

    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $checkins = WeeklyCheckin()->where('user_uuid', $user->uuid)
            ->latest('record_date')
            ->paginate((int) $request->input('per_page', 10));

        $data = paginatedResponse($checkins, WeeklyCheckinResource::class, 'checkins');
        $data['this_week'] = Journey::weeklyStatus($user);

        return jsonResponse(true, __('api.weekly_checkins_fetched'), $data);
    }

    public function show($key)
    {
        $query = WeeklyCheckin()->where('user_uuid', auth()->user()->uuid);
        $checkin = preg_match('/^\d{4}-\d{2}-\d{2}$/', $key)
            ? $query->whereDate('record_date', $key)->first()
            : $query->where('uuid', $key)->first();

        if (!$checkin) {
            return jsonResponse(false, __('api.record_not_found'), null, 404);
        }

        return jsonResponse(true, __('api.weekly_checkin_fetched'), new WeeklyCheckinResource($checkin));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $this->normalizeBooleans($request, ['travelled', 'is_baseline']);
        $this->decodeJsonInputs($request, ['extra']);

        $measure = 'nullable|numeric|between:0,300';
        $validator = Validator::make($request->all(), array_merge([
            'record_date'       => 'required|date|before_or_equal:' . Journey::today($user)->addDay()->toDateString(),
            'is_baseline'       => 'nullable|boolean',
            'weight'            => 'required|numeric|between:1,500',
            'weight_unit'       => 'nullable|in:kg,lbs',
            'chest'             => $measure,
            'abdomen'           => $measure,
            'waist'             => $measure,
            'hip'               => $measure,
            'thigh'             => $measure,
            'arm'               => $measure,
            'measurement_unit'  => 'nullable|in:cm,in',
            'self_confidence'   => 'nullable|string|max:3000',
            'last_period_start' => 'nullable|date',
            'cycle_day'         => 'nullable|string|max:50',
            'cycle_notes'       => 'nullable|string|max:2000',
            'cravings_level'    => ['nullable', Rule::in(array_keys(config('tracking.cravings_levels')))],
            'cravings_notes'    => 'nullable|string|max:2000',
            'travelled'         => 'nullable|boolean',
            'travel_notes'      => 'nullable|string|max:2000',
            'wins'              => 'nullable|string|max:3000',
            'proud_moments'     => 'nullable|string|max:3000',
            'struggles'         => 'nullable|string|max:3000',
            'notes'             => 'nullable|string|max:3000',
            'extra'             => 'nullable|array',
        ], $this->photoRules()));
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $date = date('Y-m-d', strtotime($request->record_date));
        $checkin = WeeklyCheckin()->where('user_uuid', $user->uuid)->whereDate('record_date', $date)->first()
            ?? WeeklyCheckin()->newInstance(['user_uuid' => $user->uuid, 'record_date' => $date]);

        $checkin->fill($this->presentInput($request, self::FIELDS));
        if ($request->has('is_baseline')) {
            $checkin->is_baseline = (bool) $request->is_baseline;
        } elseif (!$checkin->exists) {
            // The very first measurement becomes the baseline
            $checkin->is_baseline = !WeeklyCheckin()->where('user_uuid', $user->uuid)->exists();
        }
        $checkin->photos = $this->syncPhotos($request, $checkin->photos, 'weekly-checkins');
        $checkin->save();

        activity_log(log: 'API Weekly Checkin', event: $checkin->wasRecentlyCreated ? 'weekly_checkin_created' : 'weekly_checkin_updated', subject: $checkin);

        return jsonResponse(true, __('api.weekly_checkin_saved'), [
            'checkin'   => new WeeklyCheckinResource($checkin),
            'this_week' => Journey::weeklyStatus($user),
        ]);
    }
}
