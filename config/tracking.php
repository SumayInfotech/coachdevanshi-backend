<?php

/*
|--------------------------------------------------------------------------
| Tracking option lists (daily check-in, weekly check-in, food diary, uploads)
|--------------------------------------------------------------------------
| Served to the app by GET /api/v1/tracking/options so labels are not hardcoded in Flutter.
*/

return [

    'meal_slots' => [
        'first_intake' => 'First food or drink after waking',
        'breakfast'    => 'Morning / Breakfast',
        'mid_morning'  => 'Mid-Morning Nourishment',
        'lunch'        => 'Afternoon / Lunch',
        'snacks'       => 'Snacks & Beverages',
        'dinner'       => 'Evening / Dinner',
        'late_night'   => 'Late-night food or drinks',
    ],

    'sleep_quality' => [
        'deep_restorative'     => 'Deep & Restorative',
        'moderately_refreshed' => 'Moderately refreshed',
        'light_restless'       => 'Light / Restless',
        'interrupted'          => 'Interrupted',
    ],

    'nutrition_adherence' => [
        'on_track'  => 'On track',
        'mostly'    => 'Mostly',
        'off_track' => 'Off track',
    ],

    'cravings_levels' => [
        'none'     => 'None / Calm',
        'mild'     => 'Mild & manageable',
        'moderate' => 'Moderate',
        'intense'  => 'Intense / late evening',
    ],

    'food_diary_days' => [
        1 => ['label' => 'Day 1', 'day_type' => 'weekday'],
        2 => ['label' => 'Day 2', 'day_type' => 'weekday'],
        3 => ['label' => 'Day 3', 'day_type' => 'weekend'],
    ],

    'food_sensations' => [
        'bloating'        => 'Bloating',
        'light_energized' => 'Light & energized',
        'brain_fog'       => 'Brain fog',
        'acid_reflux'     => 'Acid reflux',
        'calm_nourished'  => 'Calm & nourished',
        'sluggish'        => 'Sluggish',
    ],

    'measurements' => [
        'chest'   => 'Chest',
        'abdomen' => 'Abdomen at Navel',
        'waist'   => 'Waist',
        'hip'     => 'Hip',
        'thigh'   => 'Thigh',
        'arm'     => 'Arm',
    ],

    // Upload limits in KB
    'photo_max_kb'  => env('PHOTO_MAX_KB', 15360),
    'photo_mimes'   => env('PHOTO_MIMES', 'jpg,jpeg,png,heic,webp'),
    'report_max_kb' => env('REPORT_MAX_KB', 25600),
    'report_mimes'  => env('REPORT_MIMES', 'pdf,jpg,jpeg,png'),
    'max_photos_per_entry' => 10,

    // Reminder settings
    'micro_reminder_after_days' => env('MICRO_REMINDER_AFTER_DAYS', 1),
    'micro_reminder_hour'       => env('MICRO_REMINDER_HOUR', 19),
    'weekly_reminder_hour'      => env('WEEKLY_REMINDER_HOUR', 9),
    'weekly_followup_hour'      => env('WEEKLY_FOLLOWUP_HOUR', 19),
    'plan_expiry_reminder_days' => [14, 7, 1],
];
