<?php

/*
|--------------------------------------------------------------------------
| Pathway protocol questionnaires
|--------------------------------------------------------------------------
| Each pathway (pathways.protocol_key) unlocks one of these forms after onboarding.
| Same field format as config/onboarding.php (see App\Support\FormSchema).
*/

$habitScale = [
    'manageable'                => 'Manageable',
    'could_reduce_with_support' => 'Could reduce with support',
    'very_difficult'            => 'Very difficult',
    'not_relevant'              => 'Not relevant',
];

return [

    'gut_reset' => [
        'title'  => 'Gut Reset Protocol',
        'fields' => [
            'primary_symptoms' => ['label' => 'Which digestive symptoms are affecting you most?', 'type' => 'multi', 'max' => 4, 'required' => true, 'options' => [
                'bloating_after_meals' => 'Bloating after meals',
                'bloating_all_day'     => 'Bloating all day',
                'gas_flatulence'       => 'Gas & flatulence',
                'frequent_burping'     => 'Frequent burping',
                'acidity_reflux'       => 'Acidity or reflux',
                'constipation'         => 'Constipation',
                'loose_stools'         => 'Loose stools',
                'alternating_pattern'  => 'Alternating pattern',
                'abdominal_pain'       => 'Abdominal pain',
                'nausea'               => 'Nausea',
                'early_fullness'       => 'Early fullness',
                'none'                 => 'None of these',
            ]],
            'symptom_onset' => ['label' => 'Symptom chronic / onset duration', 'type' => 'single', 'required' => true, 'options' => [
                'lt_3_months'   => '< 3 months',
                '3_6_months'    => '3 to 6 months',
                '6_12_months'   => '6 to 12 months',
                '1_plus_years'  => '1+ years',
                '5_plus_years'  => '5+ years / Chronic',
            ]],
            'occurrence_frequency' => ['label' => 'Occurrence frequency', 'type' => 'single', 'required' => true, 'options' => [
                'daily'          => 'Daily / Constant',
                'multiple_weekly'=> 'Multiple times / week',
                'weekly'         => 'Weekly',
                'periodic'       => 'Periodic flare-ups',
            ]],
            'symptom_peak_timing' => ['label' => 'When are symptoms usually strongest?', 'type' => 'multi', 'required' => true, 'options' => [
                'immediately_after_eating' => 'Immediately after eating',
                'within_1_2_hours'         => 'Within 1-2 hours',
                'by_evening'               => 'By evening',
                'during_night'             => 'During the night',
                'all_day'                  => 'All day uniformly',
                'stressful_periods'        => 'During stressful periods',
                'menstrual_cycle'          => 'Around menstrual cycle',
                'no_clear_pattern'         => 'No clear pattern',
            ]],
            'bowel_cadence' => ['label' => 'Bowel movements cadence', 'type' => 'single', 'required' => true, 'options' => [
                '1_2_per_day'    => '1-2 times per day',
                'every_2_3_days' => 'Every 2-3 days',
                '3_plus_per_day' => '3+ times per day',
                'irregular'      => 'Irregular / Variable',
            ]],
            'feel_fully_emptied' => ['label' => 'Usually feel fully emptied?', 'type' => 'single', 'required' => true, 'options' => [
                'always' => 'Always', 'sometimes' => 'Sometimes', 'rarely' => 'Rarely',
            ]],
            'needs_stimulant' => ['label' => 'Need coffee, tea or stimulant to initiate transit?', 'type' => 'single', 'required' => true, 'options' => [
                'yes' => 'Yes - morning warm tea/coffee is essential',
                'no'  => 'No - spontaneous morning rhythm without hot liquids',
            ]],
            'trigger_foods' => ['label' => 'Which foods or situations seem to worsen your symptoms?', 'type' => 'multi', 'required' => true, 'options' => [
                'milk' => 'Milk', 'curd_yoghurt' => 'Curd or yoghurt', 'paneer' => 'Paneer', 'wheat' => 'Wheat',
                'onion_garlic' => 'Onion or garlic', 'beans_legumes' => 'Beans or legumes',
                'fried_food' => 'Fried food', 'sweets' => 'Sweets', 'coffee' => 'Coffee',
                'restaurant_meals' => 'Restaurant meals', 'large_meals' => 'Large meals',
                'emotional_stress' => 'Emotional stress', 'not_sure' => 'Not sure yet', 'other' => 'Other',
            ]],
            'other_triggers'        => ['label' => 'Other specific food or sensory triggers', 'type' => 'textarea'],
            'prior_diagnosis'       => ['label' => 'Any medical diagnosis, clinical test, or prescription treatment for these symptoms?', 'type' => 'boolean', 'required' => true],
            'clinical_history'      => ['label' => 'Clinical history & test findings', 'type' => 'textarea', 'required_if' => ['prior_diagnosis', true]],
            'priority_relief_focus' => ['label' => 'If we improved only one digestive concern, which would make the biggest difference?', 'type' => 'textarea'],
        ],
    ],

    'fat_loss' => [
        'title'  => 'Fat Loss & Inch Loss Protocol',
        'fields' => [
            'weight_unit'          => ['label' => 'Weight unit', 'type' => 'single', 'options' => ['kg' => 'kg', 'lbs' => 'lbs']],
            'highest_weight'       => ['label' => 'Highest adult weight', 'type' => 'number', 'min' => 1, 'max' => 500],
            'highest_weight_year'  => ['label' => 'Year (highest weight)', 'type' => 'number', 'min' => 1900, 'max' => 2100],
            'lowest_weight'        => ['label' => 'Lowest adult weight', 'type' => 'number', 'min' => 1, 'max' => 500],
            'lowest_weight_year'   => ['label' => 'Year (lowest weight)', 'type' => 'number', 'min' => 1900, 'max' => 2100],
            'major_weight_periods' => ['label' => 'Periods of major gain or loss', 'type' => 'textarea'],
            'past_transitions'     => ['label' => 'When weight or habits returned before, what was happening?', 'type' => 'textarea', 'required' => true],
            'habit_readiness'      => ['label' => 'Habit readiness & boundary matrix', 'type' => 'group', 'required' => true, 'fields' => [
                'eating_out'        => ['label' => 'Eating out', 'type' => 'single', 'required' => true, 'options' => $habitScale],
                'late_night_eating' => ['label' => 'Late-night eating', 'type' => 'single', 'required' => true, 'options' => $habitScale],
                'sweets'            => ['label' => 'Sweets & confectionery', 'type' => 'single', 'required' => true, 'options' => $habitScale],
                'packaged_foods'    => ['label' => 'Packaged & processed foods', 'type' => 'single', 'required' => true, 'options' => $habitScale],
                'alcohol'           => ['label' => 'Alcohol consumption', 'type' => 'single', 'required' => true, 'options' => $habitScale],
                'skipping_meals'    => ['label' => 'Skipping meals', 'type' => 'single', 'required' => true, 'options' => $habitScale],
            ]],
            'maintenance_support'  => ['label' => 'Once you reach your goal, what would help you maintain it?', 'type' => 'textarea', 'required' => true],
        ],
    ],

    'weight_gain' => [
        'title'  => 'Healthy Weight Gain Protocol',
        'fields' => [
            'weight_loss_intentional' => ['label' => 'Is your current low weight or recent weight loss intentional?', 'type' => 'single', 'required' => true, 'options' => [
                'yes' => 'Yes', 'no' => 'No', 'partly' => 'Partly', 'not_sure' => 'Not sure',
            ]],
            'weight_unit'         => ['label' => 'Weight unit', 'type' => 'single', 'options' => ['kg' => 'Kilograms (kg)', 'lbs' => 'Pounds (lbs)']],
            'starting_weight_6m'  => ['label' => 'Approximate starting weight (6 months ago)', 'type' => 'number', 'min' => 1, 'max' => 500],
            'current_weight'      => ['label' => 'Current weight', 'type' => 'number', 'min' => 1, 'max' => 500, 'required' => true],
            'trajectory_nature'   => ['label' => 'Trajectory nature', 'type' => 'single', 'required' => true, 'options' => [
                'planned' => 'Planned change', 'unplanned' => 'Unplanned change', 'fluctuating' => 'Fluctuating / Unstable',
            ]],
            'appetite_sensations' => ['label' => 'Sensations that currently affect your meal capacity', 'type' => 'multi', 'required' => true, 'options' => [
                'low_appetite' => 'Low appetite', 'feel_full_quickly' => 'Feel full very quickly',
                'difficulty_swallowing' => 'Difficulty swallowing', 'nausea' => 'Nausea',
                'digestive_discomfort' => 'Digestive discomfort', 'none' => 'None of these',
            ]],
            'weight_gain_goals'   => ['label' => 'What would healthy weight gain mean for you?', 'type' => 'multi', 'max' => 3, 'required' => true, 'options' => [
                'more_strength' => 'More strength', 'more_muscle' => 'More muscle', 'better_energy' => 'Better energy',
                'improved_appetite' => 'Improved appetite', 'healthy_weight_restoration' => 'Healthy weight restoration',
                'sports_performance' => 'Sports performance',
            ]],
            'other_goals'         => ['label' => 'Other specific personal goals', 'type' => 'textarea'],
        ],
    ],

    'new_moms' => [
        'title'  => 'Rooted Recovery for New Moms',
        'fields' => [
            'delivery_date'          => ['label' => 'Date of delivery', 'type' => 'date', 'required' => true],
            'delivery_type'          => ['label' => 'Type of delivery', 'type' => 'single', 'required' => true, 'options' => [
                'vaginal' => 'Vaginal Birth', 'assisted_vaginal' => 'Assisted Vaginal Birth',
                'c_section' => 'C-section', 'prefer_not_to_answer' => 'Prefer Not to Answer',
            ]],
            'feeding_status'         => ['label' => 'Are you currently breastfeeding or expressing milk?', 'type' => 'single', 'required' => true, 'options' => [
                'direct_or_pumping' => 'Direct feeding or exclusive pumping', 'combination' => 'Combination feeding',
                'occasionally' => 'Occasionally', 'no' => 'No',
            ]],
            'has_complications'      => ['label' => 'Pregnancy, delivery or postpartum complications that still affect your daily care?', 'type' => 'boolean', 'required' => true],
            'complication_details'   => ['label' => 'Details of complication or continuing care', 'type' => 'textarea', 'required_if' => ['has_complications', true]],
            'exercise_clearance'     => ['label' => 'Formal medical clearance to begin or resume exercise?', 'type' => 'single', 'required' => true, 'options' => [
                'yes' => 'Yes', 'no' => 'No', 'not_discussed' => 'Not yet discussed', 'not_relevant' => 'Exercise clearance is not currently relevant',
            ]],
            'postpartum_sensations'  => ['label' => 'Current physical or emotional symptoms', 'type' => 'multi', 'required' => true, 'options' => [
                'pelvic_floor' => 'Pelvic-floor heaviness or leaking', 'diastasis' => 'Abdominal separation or diastase concern',
                'c_section_discomfort' => 'C-section or scar discomfort', 'back_hip_pelvic_pain' => 'Back, hip or pelvic pain',
                'low_mood' => 'Very low mood, anxiety or feeling unable to cope', 'none' => 'None of these',
            ]],
            'longest_sleep_stretch'  => ['label' => 'Longest usual stretch of sleep', 'type' => 'text', 'required' => true],
            'caregiving_support'     => ['label' => 'Caregiving and meal support', 'type' => 'textarea'],
            'top_priorities'         => ['label' => 'What are your top two priorities right now?', 'type' => 'multi', 'max' => 2, 'required' => true, 'options' => [
                'recovery' => 'Recovery', 'energy' => 'Energy', 'strength' => 'Strength', 'milk_supply' => 'Milk Supply',
                'digestion' => 'Digestion', 'gentle_inch_loss' => 'Gentle Inch Loss', 'sleep_routine' => 'Sleep & Routine',
                'feeling_myself' => 'Feeling Myself',
            ]],
            'other_priority'         => ['label' => 'Other specific priority', 'type' => 'text'],
        ],
    ],

    'maintenance' => [
        'title'  => 'Maintenance & Lifestyle Management',
        'fields' => [
            'achieved_milestones'     => ['label' => 'What progress have you already made, and for how long have you maintained it?', 'type' => 'textarea', 'required' => true],
            'sustainable_equilibrium' => ['label' => 'What weight, measurement or routine range feels sustainable for you?', 'type' => 'textarea', 'required' => true],
            'maintenance_disruptions' => ['label' => 'Which situations make maintenance most difficult?', 'type' => 'multi', 'max' => 3, 'required' => true, 'options' => [
                'travel' => 'Travel', 'festivals_social' => 'Festivals or social events', 'work_pressure' => 'Work pressure',
                'routine_changes' => 'Changes in routine', 'emotional_eating' => 'Emotional eating', 'poor_sleep' => 'Poor sleep',
                'reduced_exercise' => 'Reduced exercise', 'meal_planning' => 'Meal planning',
            ]],
            'other_disruption'        => ['label' => 'Other specific situation', 'type' => 'text'],
            'coaching_partnership'    => ['label' => 'What support would help you stay consistent without feeling controlled?', 'type' => 'textarea', 'required' => true],
        ],
    ],

    'reproductive' => [
        'title'  => 'Reproductive Life Stage Modules',
        'fields' => [
            'avg_cycle_length'       => ['label' => 'Average cycle length', 'type' => 'text'],
            'cycle_length_not_sure'  => ['label' => 'Not sure of cycle length', 'type' => 'boolean'],
            'bleeding_duration'      => ['label' => 'Bleeding duration', 'type' => 'text'],
            'cycle_regularity'       => ['label' => 'Regularity', 'type' => 'single', 'required' => true, 'options' => [
                'regular' => 'Regular', 'sometimes_irregular' => 'Sometimes irregular', 'irregular' => 'Irregular',
                'absent' => 'Currently absent (Amenorrhea)',
            ]],
            'cyclical_markers'       => ['label' => 'Noticeable cyclical physical or emotional markers', 'type' => 'multi', 'required' => true, 'options' => [
                'cramps' => 'Cramps', 'heavy_flow' => 'Heavy flow', 'clotting' => 'Clotting', 'mood_changes' => 'Mood changes',
                'acne' => 'Acne', 'bloating' => 'Bloating', 'breast_tenderness' => 'Breast tenderness',
                'food_cravings' => 'Food cravings', 'fatigue' => 'Fatigue or energy dip', 'none' => 'None',
            ]],
            'under_gynae_care'       => ['label' => 'Currently under the care of a gynaecologist or obstetrician?', 'type' => 'boolean'],
            'attending_clinician'    => ['label' => 'Attending clinician / hospital', 'type' => 'text'],
            'has_doctor_guidance'    => ['label' => 'Has your doctor given any nutrition, activity, or weight guidance?', 'type' => 'boolean'],
            'doctor_guidance_details'=> ['label' => 'Physician guidance details', 'type' => 'textarea'],
            'conception_status'      => ['label' => 'Estimated due date or current week of pregnancy', 'type' => 'single', 'options' => [
                'trying_to_conceive' => 'Currently Trying to Conceive (Pre-conception)', 'pregnant' => 'Currently Pregnant',
            ]],
            'estimated_due_date'     => ['label' => 'Estimated due date (EDD)', 'type' => 'date'],
            'gestational_week'       => ['label' => 'Current gestational week', 'type' => 'text'],
            'last_menstrual_period'  => ['label' => 'Last menstrual period (approx.)', 'type' => 'date'],
            'lmp_not_sure'           => ['label' => 'Not sure of last period', 'type' => 'boolean'],
            'menopause_changes'      => ['label' => 'Which changes are currently affecting you?', 'type' => 'multi', 'options' => [
                'hot_flashes' => 'Hot flashes or night sweats', 'sleep_disruption' => 'Sleep disruption', 'mood_changes' => 'Mood changes',
                'energy_changes' => 'Energy changes', 'joint_discomfort' => 'Joint discomfort', 'weight_body_comp' => 'Weight / body composition',
                'vaginal_urinary' => 'Vaginal or urinary symptoms', 'none' => 'None',
            ]],
            'other_cyclical_notes'   => ['label' => 'Other cyclical changes or notes', 'type' => 'text'],
        ],
    ],
];
