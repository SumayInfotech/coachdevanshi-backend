<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\Journey;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Option lists + limits used by the tracking forms, and the "Profile" tracker cards status.
     */
    public function options()
    {
        $user = auth()->user();
        $list = fn ($key) => collect(config('tracking.' . $key))->map(fn ($label, $value) => ['value' => (string) $value, 'label' => $label])->values();

        return jsonResponse(true, __('api.tracking_options_fetched'), [
            'meal_slots'          => $list('meal_slots'),
            'sleep_quality'       => $list('sleep_quality'),
            'nutrition_adherence' => $list('nutrition_adherence'),
            'cravings_levels'     => $list('cravings_levels'),
            'food_sensations'     => $list('food_sensations'),
            'measurements'        => $list('measurements'),
            'food_diary_days'     => collect(config('tracking.food_diary_days'))->map(fn ($day, $number) => ['day_number' => $number] + $day)->values(),
            'week_days'           => Journey::WEEK_DAYS,
            'limits'              => [
                'photo_max_kb'         => (int) config('tracking.photo_max_kb'),
                'photo_mimes'          => explode(',', config('tracking.photo_mimes')),
                'report_max_kb'        => (int) config('tracking.report_max_kb'),
                'report_mimes'         => explode(',', config('tracking.report_mimes')),
                'max_photos_per_entry' => (int) config('tracking.max_photos_per_entry'),
            ],
            'trackers'            => [
                'food_diary_completed'  => FoodDiaryEntry()->where('user_uuid', $user->uuid)->count(),
                'baseline'              => Journey::baselineStatus($user),
                'weekly'                => Journey::weeklyStatus($user),
                'progress_photos_count' => ProgressPhoto()->where('user_uuid', $user->uuid)->count(),
                'blood_reports_count'   => BloodReport()->where('user_uuid', $user->uuid)->count(),
            ],
        ]);
    }
}
