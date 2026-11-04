<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\HandlesTrackingInput;
use App\Http\Controllers\Controller;
use App\Http\Resources\DailyCheckinResource;
use App\Support\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Daily wellness log / micro check-in (one entry per date, saving again updates it).
 */
class DailyCheckinController extends Controller
{
    use HandlesTrackingInput;

    protected const FIELDS = [
        'baseline_day', 'weight', 'weight_unit', 'wake_time', 'bed_time', 'sleep_hours', 'sleep_quality',
        'steps', 'water_intake', 'water_unit', 'water_goal_met', 'mood', 'energy', 'nutrition_adherence',
        'movement_done', 'movement_type', 'meditation_done', 'meditation_notes', 'journaling_done',
        'journaling_notes', 'meals', 'notes',
    ];

    protected const BOOLEANS = ['water_goal_met', 'movement_done', 'meditation_done', 'journaling_done'];

    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from'     => 'nullable|date',
            'to'       => 'nullable|date',
            'per_page' => pagination_length_validation_rule(),
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        $query = DailyCheckin()->where('user_uuid', $user->uuid);
        if ($request->filled('from')) {
            $query->whereDate('record_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('record_date', '<=', $request->to);
        }

        $checkins = $query->latest('record_date')->paginate((int) $request->input('per_page', 10));

        $data = paginatedResponse($checkins, DailyCheckinResource::class, 'checkins');
        $data['baseline'] = Journey::baselineStatus($user);
        $data['today'] = Journey::microStatus($user);

        return jsonResponse(true, __('api.daily_checkins_fetched'), $data);
    }

    /**
     * {key} is the check-in uuid or a date (Y-m-d).
     */
    public function show($key)
    {
        $checkin = $this->find($key);
        if (!$checkin) {
            return jsonResponse(false, __('api.record_not_found'), null, 404);
        }

        return jsonResponse(true, __('api.daily_checkin_fetched'), new DailyCheckinResource($checkin));
    }

    /**
     * Create or update the entry for record_date. Only the fields sent are changed.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $this->normalizeBooleans($request, self::BOOLEANS);
        $this->decodeJsonInputs($request, ['meals']);

        $slots = array_keys(config('tracking.meal_slots'));
        $validator = Validator::make($request->all(), array_merge([
            'record_date'         => 'required|date|before_or_equal:' . Journey::today($user)->addDay()->toDateString(),
            'baseline_day'        => 'nullable|integer|between:1,7',
            'weight'              => 'nullable|numeric|between:1,500',
            'weight_unit'         => 'nullable|in:kg,lbs',
            'wake_time'           => 'nullable|string|max:10',
            'bed_time'            => 'nullable|string|max:10',
            'sleep_hours'         => 'nullable|numeric|between:0,24',
            'sleep_quality'       => ['nullable', Rule::in(array_keys(config('tracking.sleep_quality')))],
            'steps'               => 'nullable|integer|between:0,200000',
            'water_intake'        => 'nullable|numeric|between:0,50',
            'water_unit'          => 'nullable|in:litres,glasses',
            'water_goal_met'      => 'nullable|boolean',
            'mood'                => 'nullable|integer|between:1,5',
            'energy'              => 'nullable|integer|between:1,5',
            'nutrition_adherence' => ['nullable', Rule::in(array_keys(config('tracking.nutrition_adherence')))],
            'movement_done'       => 'nullable|boolean',
            'movement_type'       => 'nullable|string|max:255',
            'meditation_done'     => 'nullable|boolean',
            'meditation_notes'    => 'nullable|string|max:2000',
            'journaling_done'     => 'nullable|boolean',
            'journaling_notes'    => 'nullable|string|max:2000',
            'meals'               => 'nullable|array',
            'meals.*'             => 'array',
            'meals.*.time'        => 'nullable|string|max:20',
            'meals.*.items'       => 'nullable|string|max:2000',
            'notes'               => 'nullable|string|max:2000',
        ], $this->photoRules()));
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }
        if ($request->filled('meals') && array_diff(array_keys($request->meals), $slots)) {
            return jsonResponse(false, __('api.invalid_option_selected', ['field' => 'meals']));
        }

        $date = date('Y-m-d', strtotime($request->record_date));
        $checkin = DailyCheckin()->where('user_uuid', $user->uuid)->whereDate('record_date', $date)->first()
            ?? DailyCheckin()->newInstance(['user_uuid' => $user->uuid, 'record_date' => $date]);

        $checkin->fill($this->presentInput($request, self::FIELDS));
        $checkin->photos = $this->syncPhotos($request, $checkin->photos, 'daily-checkins');
        $checkin->save();

        // A baseline day belongs to one date only
        if ($checkin->baseline_day) {
            DailyCheckin()->where('user_uuid', $user->uuid)->where('baseline_day', $checkin->baseline_day)
                ->where('id', '!=', $checkin->id)->update(['baseline_day' => null]);
        }

        activity_log(log: 'API Daily Checkin', event: $checkin->wasRecentlyCreated ? 'daily_checkin_created' : 'daily_checkin_updated', subject: $checkin);

        return jsonResponse(true, __('api.daily_checkin_saved'), [
            'checkin'  => new DailyCheckinResource($checkin),
            'baseline' => Journey::baselineStatus($user),
        ]);
    }

    protected function find($key)
    {
        $query = DailyCheckin()->where('user_uuid', auth()->user()->uuid);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $key)
            ? $query->whereDate('record_date', $key)->first()
            : $query->where('uuid', $key)->first();
    }
}
