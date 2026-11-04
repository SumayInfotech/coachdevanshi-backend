<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\HandlesTrackingInput;
use App\Http\Controllers\Controller;
use App\Http\Resources\DailyCheckinResource;
use App\Http\Resources\DailyNoteResource;
use App\Http\Resources\FoodDiaryEntryResource;
use App\Http\Resources\ProgressPhotoResource;
use App\Http\Resources\WeeklyCheckinResource;
use App\Support\Journey;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    use HandlesTrackingInput;

    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Month view: per-day indicators. ?month=YYYY-MM (defaults to the current month).
     */
    public function month(Request $request)
    {
        $validator = Validator::make($request->all(), ['month' => 'nullable|date_format:Y-m']);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        $today = Journey::today($user);
        $start = $request->filled('month')
            ? CarbonImmutable::createFromFormat('Y-m-d', $request->month . '-01')->startOfDay()
            : $today->startOfMonth();
        $end = $start->endOfMonth();
        $range = [$start->toDateString(), $end->toDateString()];

        $dates = fn ($model, $column) => $model->where('user_uuid', $user->uuid)->whereBetween($column, $range)
            ->pluck($column)->map(fn ($date) => $date->toDateString())->unique()->values()->all();

        $daily = DailyCheckin()->where('user_uuid', $user->uuid)->whereBetween('record_date', $range)->get(['record_date', 'steps', 'photos'])
            ->keyBy(fn ($row) => $row->record_date->toDateString());
        $notes = DailyNote()->where('user_uuid', $user->uuid)->whereBetween('note_date', $range)->get(['note_date', 'photos'])
            ->keyBy(fn ($row) => $row->note_date->toDateString());
        $steps = ActivitySync()->where('user_uuid', $user->uuid)->whereBetween('activity_date', $range)->get(['activity_date', 'steps'])
            ->keyBy(fn ($row) => $row->activity_date->toDateString());
        $weekly = $dates(WeeklyCheckin(), 'record_date');
        $photos = $dates(ProgressPhoto(), 'record_date');
        $diary = $dates(FoodDiaryEntry(), 'record_date');

        $days = [];
        for ($day = $start; $day->lte($end); $day = $day->addDay()) {
            $date = $day->toDateString();
            $days[] = [
                'date'               => $date,
                'is_today'           => $day->equalTo($today),
                'is_future'          => $day->gt($today),
                'steps'              => $steps[$date]->steps ?? $daily[$date]->steps ?? null,
                'has_daily_checkin'  => isset($daily[$date]),
                'has_weekly_checkin' => in_array($date, $weekly),
                'has_food_diary'     => in_array($date, $diary),
                'has_note'           => isset($notes[$date]),
                'has_photos'         => !empty($daily[$date]->photos ?? null) || !empty($notes[$date]->photos ?? null) || in_array($date, $photos),
            ];
        }

        return jsonResponse(true, __('api.calendar_fetched'), [
            'month'      => $start->format('Y-m'),
            'month_label'=> $start->format('F Y'),
            'days'       => $days,
        ]);
    }

    /**
     * Daily log for one date: steps, micro check-in, weekly check-in indicator, notes & photos.
     */
    public function day(Request $request)
    {
        $validator = Validator::make($request->all(), ['date' => 'required|date_format:Y-m-d']);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        $date = $request->date;
        $weekStart = CarbonImmutable::parse($date)->startOfWeek();

        $checkin = DailyCheckin()->where('user_uuid', $user->uuid)->whereDate('record_date', $date)->first();
        $weekly = WeeklyCheckin()->where('user_uuid', $user->uuid)->whereDate('record_date', $date)->first();
        $weekHasCheckin = $weekly ?: WeeklyCheckin()->where('user_uuid', $user->uuid)
            ->whereBetween('record_date', [$weekStart->toDateString(), $weekStart->addDays(6)->toDateString()])->first();
        $note = DailyNote()->where('user_uuid', $user->uuid)->whereDate('note_date', $date)->first();
        $diary = FoodDiaryEntry()->where('user_uuid', $user->uuid)->whereDate('record_date', $date)->first();
        $photos = ProgressPhoto()->where('user_uuid', $user->uuid)->whereDate('record_date', $date)->first();

        return jsonResponse(true, __('api.calendar_day_fetched'), [
            'date'                => $date,
            'date_label'          => CarbonImmutable::parse($date)->format('l, F j'),
            'activity'            => Journey::stepsFor($user, $date),
            'daily_checkin'       => $checkin ? new DailyCheckinResource($checkin) : null,
            'weekly_checkin'      => $weekly ? new WeeklyCheckinResource($weekly) : null,
            'week_has_checkin'    => (bool) $weekHasCheckin,
            'week_checkin_uuid'   => $weekHasCheckin?->uuid,
            'note'                => $note ? new DailyNoteResource($note) : null,
            'food_diary'          => $diary ? new FoodDiaryEntryResource($diary) : null,
            'progress_photos'     => $photos ? new ProgressPhotoResource($photos) : null,
        ]);
    }

    /**
     * Add / edit the "Notes & Photos" entry of a date.
     */
    public function saveNote(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), array_merge([
            'note_date' => 'required|date_format:Y-m-d|before_or_equal:' . Journey::today($user)->addDay()->toDateString(),
            'note'      => 'nullable|string|max:5000',
        ], $this->photoRules()));
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $note = DailyNote()->where('user_uuid', $user->uuid)->whereDate('note_date', $request->note_date)->first()
            ?? DailyNote()->newInstance(['user_uuid' => $user->uuid, 'note_date' => $request->note_date]);

        if ($request->exists('note')) {
            $note->note = $request->note;
        }
        $note->photos = $this->syncPhotos($request, $note->photos, 'daily-notes');
        $note->save();

        activity_log(log: 'API Daily Note', event: 'daily_note_saved', subject: $note);

        return jsonResponse(true, __('api.daily_note_saved'), new DailyNoteResource($note));
    }
}
