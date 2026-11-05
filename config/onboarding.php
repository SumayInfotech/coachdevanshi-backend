<?php

/*
|--------------------------------------------------------------------------
| Client Onboarding (6 chapters + consent)
|--------------------------------------------------------------------------
| Field definitions are read by App\Support\FormSchema. Supported types:
|   text, textarea, email, number, date, time, boolean,
|   single / multi  (options from 'master' => onboarding_masters.category, or inline 'options'),
|   group           (nested 'fields', stored as an object),
|   repeater        (list of objects with nested 'fields'),
|   state / city    (states.id / cities.id; options come from GET get-location,
|                    a city's 'state_field' names the state field it must belong to).
| 'master' option values are onboarding_masters.uuid.
| 'required_if' => [field, value] / 'required_with' => field make a field conditionally required.
*/

return [

    'chapters' => [

        1 => [
            'title'    => 'Profile & Programme',
            'subtitle' => 'Personal details, location, daily rhythm and coaching journey',
            'fields'   => [
                'email'            => ['label' => 'Email address', 'type' => 'email', 'required' => true],
                'full_name'        => ['label' => 'Full name', 'type' => 'text', 'required' => true],
                'nickname'         => ['label' => 'Preferred name', 'type' => 'text'],
                'phone_code'       => ['label' => 'Phone code', 'type' => 'text', 'max' => 6, 'required' => true],
                'mobile'           => ['label' => 'Mobile number', 'type' => 'text', 'max' => 15, 'required' => true],
                'dob'              => ['label' => 'Date of birth', 'type' => 'date', 'required' => true],
                'gender'           => ['label' => 'Biological context', 'type' => 'single', 'required' => true, 'options' => [
                    'female'               => 'Female',
                    'male'                 => 'Male',
                    'prefer_not_to_answer' => 'Prefer not to answer',
                ]],
                'country'          => ['label' => 'Country', 'type' => 'text', 'required' => true],
                'state_id'         => ['label' => 'State', 'type' => 'state', 'required' => true],
                'city_id'          => ['label' => 'City', 'type' => 'city', 'state_field' => 'state_id', 'required' => true],
                'timezone'         => ['label' => 'Time zone', 'type' => 'text', 'max' => 64, 'required' => true],
                'daily_rhythm'     => ['label' => 'Your daily rhythm', 'type' => 'textarea', 'min' => 50, 'required' => true],
                'coaching_journey' => ['label' => 'Which coaching journey are you joining?', 'type' => 'single', 'master' => 'coaching_journey', 'required' => true],
            ],
        ],

        2 => [
            'title'    => 'Goals & Starting Point',
            'subtitle' => 'Baseline measurements and past experience',
            'fields'   => [
                'height'              => ['label' => 'Current height', 'type' => 'number', 'min' => 1, 'max' => 300, 'required' => true],
                'height_unit'         => ['label' => 'Height unit', 'type' => 'single', 'required' => true, 'options' => ['cm' => 'cm', 'ft_in' => 'Feet & inches']],
                'weight'              => ['label' => 'Current weight', 'type' => 'number', 'min' => 1, 'max' => 500, 'required' => true],
                'weight_unit'         => ['label' => 'Weight unit', 'type' => 'single', 'required' => true, 'options' => ['kg' => 'Kilograms (kg)', 'lbs' => 'Pounds (lbs)']],
                'approaches_tried'    => ['label' => 'Approaches you have tried', 'type' => 'multi', 'master' => 'approaches_tried', 'required' => true],
                'approaches_learning' => ['label' => 'What did you understand or learn from those experiences?', 'type' => 'textarea'],
                'confident_areas'     => ['label' => 'Which areas do you already feel confident about?', 'type' => 'multi', 'master' => 'confident_areas', 'required' => true],
                'confusing_areas'     => ['label' => 'What still feels confusing or difficult?', 'type' => 'textarea', 'required' => true],
                'what_helped'         => ['label' => 'What helped, what made you stop, and what contributed to progress returning?', 'type' => 'textarea', 'required' => true],
            ],
        ],

        3 => [
            'title'    => 'Relevant Health & Safety',
            'subtitle' => 'Medical history, medications and life stage',
            'fields'   => [
                'medical_diagnoses'          => ['label' => 'Medical diagnoses', 'type' => 'multi', 'master' => 'medical_diagnoses', 'required' => true],
                'medical_diagnoses_other'    => ['label' => 'Other diagnosis', 'type' => 'text'],
                'family_medical_history'     => ['label' => 'Family medical history', 'type' => 'multi', 'master' => 'family_medical_history', 'required' => true],
                'family_history_other'       => ['label' => 'Other family history', 'type' => 'text'],
                'taking_medications'         => ['label' => 'Currently taking prescribed medicine or regular supplements?', 'type' => 'boolean', 'required' => true],
                'medications'                => ['label' => 'Medications & supplements', 'type' => 'repeater', 'required_if' => ['taking_medications', true], 'fields' => [
                    'name'      => ['label' => 'Medication / supplement name', 'type' => 'text', 'required' => true],
                    'dose'      => ['label' => 'Dose', 'type' => 'text'],
                    'frequency' => ['label' => 'Frequency', 'type' => 'text'],
                    'reason'    => ['label' => 'Reason / condition', 'type' => 'text'],
                ]],
                'has_movement_limitations'   => ['label' => 'Any injury, pain or physical limitation affecting movement?', 'type' => 'boolean', 'required' => true],
                'movement_limitation_details'=> ['label' => 'Movement limitation details', 'type' => 'textarea'],
                'has_physician_restrictions' => ['label' => 'Has a doctor asked you to avoid or limit any food, exercise or lifestyle practice?', 'type' => 'boolean', 'required' => true],
                'physician_guidance'         => ['label' => 'Specific medical guidance', 'type' => 'textarea', 'required_if' => ['has_physician_restrictions', true]],
                'recent_clinical_symptoms'   => ['label' => 'Recent clinical symptoms (past six months)', 'type' => 'multi', 'master' => 'recent_clinical_symptoms', 'required' => true],
                'allergies_intolerances'     => ['label' => 'Food allergy or intolerance', 'type' => 'single', 'master' => 'allergies_intolerances', 'required' => true],
                'allergy_details'            => ['label' => 'Foods & observed symptoms', 'type' => 'textarea'],
                'hormonal_life_stage'        => ['label' => 'Reproductive or hormonal life stage', 'type' => 'single', 'master' => 'hormonal_life_stage', 'required' => true],
            ],
        ],

        4 => [
            'title'    => 'Food & Digestion',
            'subtitle' => 'Eating pattern, meal rhythm, food environment and digestion',
            'fields'   => [
                'eating_pattern'             => ['label' => 'Eating pattern', 'type' => 'single', 'master' => 'eating_pattern', 'required' => true],
                'eating_pattern_other'       => ['label' => 'Other eating pattern', 'type' => 'text'],
                'cultural_fasting_practices' => ['label' => 'Religious, cultural or fasting practices', 'type' => 'multi', 'master' => 'cultural_fasting_practices', 'required' => true],
                'weekday_meals'              => ['label' => 'Weekday meal rhythm', 'type' => 'group', 'required' => true, 'fields' => [
                    'first_intake'  => ['label' => 'First food or drink after waking', 'type' => 'text', 'required' => true],
                    'breakfast'     => ['label' => 'Breakfast', 'type' => 'text', 'required' => true],
                    'lunch'         => ['label' => 'Lunch', 'type' => 'text', 'required' => true],
                    'evening_snack' => ['label' => 'Evening snack', 'type' => 'text'],
                    'dinner'        => ['label' => 'Dinner', 'type' => 'text', 'required' => true],
                    'late_night'    => ['label' => 'Late-night food or drinks', 'type' => 'text'],
                    'meal_timings'  => ['label' => 'Usual meal timings', 'type' => 'text'],
                ]],
                'weekend_routine'            => ['label' => 'Weekend / eating-out routine', 'type' => 'textarea'],
                'foods_enjoyed'              => ['label' => 'Foods you enjoy and eat often', 'type' => 'textarea', 'required' => true],
                'foods_disliked'             => ['label' => 'Foods you dislike or avoid', 'type' => 'textarea'],
                'foods_not_ready_to_give_up' => ['label' => 'Foods or drinks you are not ready to give up', 'type' => 'textarea'],
                'food_env_who_cooks'         => ['label' => 'Who usually cooks', 'type' => 'single', 'master' => 'food_env_who_cooks', 'required' => true],
                'food_env_share_meals_with'  => ['label' => 'Who you share most meals with', 'type' => 'text'],
                'food_env_support_level'     => ['label' => 'How supportive they are', 'type' => 'single', 'master' => 'food_env_support_level', 'required' => true],
                'food_env_meals_out_per_week'=> ['label' => 'Meals ordered or eaten out per week', 'type' => 'single', 'master' => 'food_env_meals_out_per_week', 'required' => true],
                'intake_fruit'               => ['label' => 'Fruit', 'type' => 'single', 'master' => 'intake_fruit', 'required' => true],
                'intake_vegetables'          => ['label' => 'Vegetables', 'type' => 'single', 'master' => 'intake_vegetables', 'required' => true],
                'intake_water'               => ['label' => 'Water', 'type' => 'single', 'master' => 'intake_water', 'required' => true],
                'intake_caffeine'            => ['label' => 'Caffeinated drinks', 'type' => 'single', 'master' => 'intake_caffeine', 'required' => true],
                'intake_sugary'              => ['label' => 'Sugary foods or drinks', 'type' => 'single', 'master' => 'intake_sugary', 'required' => true],
                'substance_habits'           => ['label' => 'Alcohol, cigarettes, vaping or recreational substances', 'type' => 'multi', 'master' => 'substance_habits', 'required' => true],
                'digestive_health'           => ['label' => 'Digestive concerns', 'type' => 'multi', 'master' => 'digestive_health', 'required' => true],
            ],
        ],

        5 => [
            'title'    => 'Lifestyle & Coaching Fit',
            'subtitle' => 'Sleep, activity, stress and readiness',
            'fields'   => [
                'bedtime'                    => ['label' => 'Bedtime', 'type' => 'time', 'required' => true],
                'wake_time'                  => ['label' => 'Wake time', 'type' => 'time', 'required' => true],
                'sleep_duration'             => ['label' => 'Typical sleep duration', 'type' => 'text', 'required' => true],
                'sleep_night_waking'         => ['label' => 'Night waking frequency', 'type' => 'single', 'master' => 'sleep_night_waking', 'required' => true],
                'sleep_first_hour'           => ['label' => 'First hour after waking', 'type' => 'single', 'master' => 'sleep_first_hour', 'required' => true],
                'activity_level'             => ['label' => 'Usual daily activity', 'type' => 'single', 'master' => 'activity_level', 'required' => true],
                'activity_daily_steps'       => ['label' => 'Average daily steps', 'type' => 'single', 'master' => 'activity_daily_steps', 'required' => true],
                'activity_workouts_per_week' => ['label' => 'Current workouts per week', 'type' => 'single', 'master' => 'activity_workouts_per_week', 'required' => true],
                'movement_types'             => ['label' => 'Movement or workout types', 'type' => 'text'],
                'wearable_used'              => ['label' => 'Wearable or health app used', 'type' => 'text'],
                'movement_preferences'       => ['label' => 'Movement you enjoy or dislike', 'type' => 'text'],
                'activity_available_days'    => ['label' => 'Days you could make time for intentional movement', 'type' => 'single', 'master' => 'activity_available_days', 'required' => true],
                'stress_sources'             => ['label' => 'Three biggest sources of stress', 'type' => 'list', 'max' => 3, 'required' => true],
                'stress_first_changes'       => ['label' => 'What tends to change first when stress is high?', 'type' => 'multi', 'master' => 'stress_first_changes', 'required' => true],
                'stress_recovery'            => ['label' => 'What helps you come back down?', 'type' => 'textarea'],
                'readiness_score'            => ['label' => 'How ready do you feel to make changes right now?', 'type' => 'single', 'master' => 'readiness_score', 'required' => true],
                'consistency_interrupters'   => ['label' => 'What most commonly interrupts your consistency?', 'type' => 'multi', 'master' => 'consistency_interrupters', 'max' => 3, 'required' => true],
            ],
        ],

        6 => [
            'title'    => 'Mindset & Sustainability',
            'subtitle' => 'Self-talk, boundaries and your vision of success',
            'fields'   => [
                'goal_mindset'           => ['label' => 'Which statements sound most like you when working toward a goal?', 'type' => 'multi', 'master' => 'goal_mindset', 'max' => 3, 'required' => true],
                'slow_progress_response' => ['label' => 'When progress is slower than you hoped, what happens?', 'type' => 'textarea', 'required' => true],
                'one_change_to_start'    => ['label' => 'One change you could genuinely start this week', 'type' => 'text', 'required' => true],
                'not_ready_to_change'    => ['label' => 'One thing you are not ready to change yet', 'type' => 'text', 'required' => true],
                'vision_of_success'      => ['label' => 'If coaching worked well, what would feel different?', 'type' => 'textarea', 'required' => true],
                'open_reflections'       => ['label' => 'Anything important we have not asked?', 'type' => 'textarea'],
            ],
        ],
    ],

    // Final consent screen. required => must be accepted to complete onboarding.
    'consents' => [
        'info_accurate'      => ['required' => true,  'label' => 'I confirm that the information provided is accurate to the best of my knowledge and I will update my coach if my health, medication, pregnancy status or medical advice changes.'],
        'not_medical_advice' => ['required' => true,  'label' => 'I understand that nutrition and lifestyle coaching does not diagnose or treat medical conditions and does not replace care from a doctor or other qualified healthcare professional.'],
        'data_usage'         => ['required' => true,  'label' => 'I consent to my information being used to create, deliver and monitor my coaching plan, as explained in the Privacy Notice.'],
        'communication'      => ['required' => true,  'label' => 'I agree to receive coaching and service-related communication through the contact details I provided.'],
        'marketing'          => ['required' => false, 'label' => 'I would like to receive offers, newsletters or promotional updates.'],
    ],
];
