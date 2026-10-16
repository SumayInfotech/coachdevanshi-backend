<?php

namespace Database\Seeders;

use App\Models\OnboardingMaster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OnboardingMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $masters = [
            // ========================================================================================================================================================================
            // CHAPTER 01: Profile & Program (Coaching Journey)
            // ==========================================
            [
                'category'    => 'coaching_journey',
                'title'       => 'Fat Loss and Inch Loss',
                'description' => 'Sustainable metabolic tuning & mindful body recomp',
                'sort_order'  => 1,
            ],
            [
                'category'    => 'coaching_journey',
                'title'       => 'Healthy Weight Gain',
                'description' => 'Nourishing caloric density & strength optimization',
                'sort_order'  => 2,
            ],
            [
                'category'    => 'coaching_journey',
                'title'       => 'Rooted Recovery for New Moms',
                'description' => 'Postpartum restorative nourishment & gentle hormonal balance',
                'sort_order'  => 3,
            ],
            [
                'category'    => 'coaching_journey',
                'title'       => 'Maintenance and Lifestyle Management',
                'description' => 'Long-term nutritional intuition & energy stability',
                'sort_order'  => 4,
            ],
            [
                'category'    => 'coaching_journey',
                'title'       => 'Gut Reset',
                'description' => 'Microbiome rebalancing & anti-inflammatory food protocols',
                'sort_order'  => 5,
            ],

            // ========================================================================================================================================================================
            //CHAPTER 02: EXPERIENCE & FOUNDATIONS
            // ==========================================
            // ---- Approaches you have tried * ----
            ['category' => 'approaches_tried', 'title' => 'Calorie counting',        'description' => null, 'sort_order' => 1],
            ['category' => 'approaches_tried', 'title' => 'Macro tracking',          'description' => null, 'sort_order' => 2],
            ['category' => 'approaches_tried', 'title' => 'Portion control',         'description' => null, 'sort_order' => 3],
            ['category' => 'approaches_tried', 'title' => 'Intermittent fasting',    'description' => null, 'sort_order' => 4],
            ['category' => 'approaches_tried', 'title' => 'Low-carb / Keto',         'description' => null, 'sort_order' => 5],
            ['category' => 'approaches_tried', 'title' => 'Detox / Juice plan',      'description' => null, 'sort_order' => 6],
            ['category' => 'approaches_tried', 'title' => 'Meal replacement',        'description' => null, 'sort_order' => 7],
            ['category' => 'approaches_tried', 'title' => 'Weight medication',       'description' => null, 'sort_order' => 8],
            ['category' => 'approaches_tried', 'title' => 'Supplements',             'description' => null, 'sort_order' => 9],
            ['category' => 'approaches_tried', 'title' => 'Gym / Personal training', 'description' => null, 'sort_order' => 10],
            ['category' => 'approaches_tried', 'title' => 'Dietitian plan',          'description' => null, 'sort_order' => 11],
            ['category' => 'approaches_tried', 'title' => 'Online program/app',      'description' => null, 'sort_order' => 12],
            ['category' => 'approaches_tried', 'title' => 'Self-directed',           'description' => null, 'sort_order' => 13],
            ['category' => 'approaches_tried', 'title' => 'None',                    'description' => null, 'sort_order' => 14],
            ['category' => 'approaches_tried', 'title' => 'Other',                   'description' => null, 'sort_order' => 15],

            // ---- Which areas do you already feel confident about? * ----
            ['category' => 'confident_areas', 'title' => 'Portions',               'description' => null, 'sort_order' => 1],
            ['category' => 'confident_areas', 'title' => 'Protein',                'description' => null, 'sort_order' => 2],
            ['category' => 'confident_areas', 'title' => 'Balanced meals',         'description' => null, 'sort_order' => 3],
            ['category' => 'confident_areas', 'title' => 'Food labels',            'description' => null, 'sort_order' => 4],
            ['category' => 'confident_areas', 'title' => 'Calories or macros',     'description' => null, 'sort_order' => 5],
            ['category' => 'confident_areas', 'title' => 'Meal preparation',       'description' => null, 'sort_order' => 6],
            ['category' => 'confident_areas', 'title' => 'Hunger & fullness cues', 'description' => null, 'sort_order' => 7],
            ['category' => 'confident_areas', 'title' => 'Exercise',               'description' => null, 'sort_order' => 8],
            ['category' => 'confident_areas', 'title' => 'Sleep and stress',       'description' => null, 'sort_order' => 9],
            ['category' => 'confident_areas', 'title' => 'None yet',               'description' => null, 'sort_order' => 10],
            ['category' => 'confident_areas', 'title' => 'Other',                  'description' => null, 'sort_order' => 11],

            // ========================================================================================================================================================================
            //CHAPTER 03: Relevent health & safety
            // ========================================== 

            // ==========================================
            // MEDICAL DIAGNOSES (multi-select) *
            // ==========================================
            ['category' => 'medical_diagnoses', 'title' => 'Diabetes or prediabetes',                  'description' => null, 'sort_order' => 1],
            ['category' => 'medical_diagnoses', 'title' => 'High blood pressure',                      'description' => null, 'sort_order' => 2],
            ['category' => 'medical_diagnoses', 'title' => 'High cholesterol or triglycerides',        'description' => null, 'sort_order' => 3],
            ['category' => 'medical_diagnoses', 'title' => 'Thyroid condition',                        'description' => null, 'sort_order' => 4],
            ['category' => 'medical_diagnoses', 'title' => 'PCOS or PCOD',                             'description' => null, 'sort_order' => 5],
            ['category' => 'medical_diagnoses', 'title' => 'Endometriosis, fibroids or adenomyosis',   'description' => null, 'sort_order' => 6],
            ['category' => 'medical_diagnoses', 'title' => 'Fatty liver',                              'description' => null, 'sort_order' => 7],
            ['category' => 'medical_diagnoses', 'title' => 'Anaemia or nutrient deficiency',           'description' => null, 'sort_order' => 8],
            ['category' => 'medical_diagnoses', 'title' => 'Kidney, heart or autoimmune condition',    'description' => null, 'sort_order' => 9],
            ['category' => 'medical_diagnoses', 'title' => 'Digestive condition',                      'description' => null, 'sort_order' => 10],
            ['category' => 'medical_diagnoses', 'title' => 'Anxiety, depression or mental-health condition', 'description' => null, 'sort_order' => 11],
            ['category' => 'medical_diagnoses', 'title' => 'None of the above',                        'description' => null, 'sort_order' => 12],
            ['category' => 'medical_diagnoses', 'title' => 'Other',                                    'description' => null, 'sort_order' => 13],

            // ==========================================
            // FAMILY MEDICAL HISTORY (multi-select) *
            // ==========================================
            ['category' => 'family_medical_history', 'title' => 'Diabetes',            'description' => null, 'sort_order' => 1],
            ['category' => 'family_medical_history', 'title' => 'High blood pressure', 'description' => null, 'sort_order' => 2],
            ['category' => 'family_medical_history', 'title' => 'High cholesterol',    'description' => null, 'sort_order' => 3],
            ['category' => 'family_medical_history', 'title' => 'Heart disease',       'description' => null, 'sort_order' => 4],
            ['category' => 'family_medical_history', 'title' => 'Thyroid condition',   'description' => null, 'sort_order' => 5],
            ['category' => 'family_medical_history', 'title' => 'Obesity',             'description' => null, 'sort_order' => 6],
            ['category' => 'family_medical_history', 'title' => 'None known',          'description' => null, 'sort_order' => 7],
            ['category' => 'family_medical_history', 'title' => 'Other',               'description' => null, 'sort_order' => 8],

            // ==========================================
            // RECENT CLINICAL SYMPTOMS (multi-select) *
            // ==========================================
            ['category' => 'recent_clinical_symptoms', 'title' => 'Chest pain during rest or activity', 'description' => null, 'sort_order' => 1],
            ['category' => 'recent_clinical_symptoms', 'title' => 'Unexplained breathlessness',          'description' => null, 'sort_order' => 2],
            ['category' => 'recent_clinical_symptoms', 'title' => 'Dizziness or fainting',               'description' => null, 'sort_order' => 3],
            ['category' => 'recent_clinical_symptoms', 'title' => 'Uncontrolled blood pressure',         'description' => null, 'sort_order' => 4],
            ['category' => 'recent_clinical_symptoms', 'title' => 'Unexplained rapid weight loss',       'description' => null, 'sort_order' => 5],
            ['category' => 'recent_clinical_symptoms', 'title' => 'Blood in stool',                      'description' => null, 'sort_order' => 6],
            ['category' => 'recent_clinical_symptoms', 'title' => 'Persistent vomiting',                 'description' => null, 'sort_order' => 7],
            ['category' => 'recent_clinical_symptoms', 'title' => 'Severe abdominal or pelvic pain',     'description' => null, 'sort_order' => 8],
            ['category' => 'recent_clinical_symptoms', 'title' => 'None of the above',                   'description' => null, 'sort_order' => 9],

            // ==========================================
            // ALLERGIES & INTOLERANCES (single-select) *
            // ==========================================
            ['category' => 'allergies_intolerances', 'title' => 'No known allergies',                    'description' => null, 'sort_order' => 1],
            ['category' => 'allergies_intolerances', 'title' => 'Yes – medically diagnosed allergy',     'description' => null, 'sort_order' => 2],
            ['category' => 'allergies_intolerances', 'title' => 'Yes – suspected allergy',               'description' => null, 'sort_order' => 3],
            ['category' => 'allergies_intolerances', 'title' => 'Yes – intolerance or food triggers',    'description' => null, 'sort_order' => 4],

            // ==========================================
            // HORMONAL & LIFE STAGE (single-select) *
            // ==========================================
            ['category' => 'hormonal_life_stage', 'title' => 'Regular menstrual cycles',                          'description' => null, 'sort_order' => 1],
            ['category' => 'hormonal_life_stage', 'title' => 'Irregular menstrual cycles',                        'description' => null, 'sort_order' => 2],
            ['category' => 'hormonal_life_stage', 'title' => 'Trying to conceive',                                'description' => null, 'sort_order' => 3],
            ['category' => 'hormonal_life_stage', 'title' => 'Pregnant',                                          'description' => null, 'sort_order' => 4],
            ['category' => 'hormonal_life_stage', 'title' => 'Postpartum and breastfeeding',                      'description' => null, 'sort_order' => 5],
            ['category' => 'hormonal_life_stage', 'title' => 'Postpartum and not breastfeeding',                  'description' => null, 'sort_order' => 6],
            ['category' => 'hormonal_life_stage', 'title' => 'Perimenopause',                                     'description' => null, 'sort_order' => 7],
            ['category' => 'hormonal_life_stage', 'title' => 'Menopause',                                         'description' => null, 'sort_order' => 8],
            ['category' => 'hormonal_life_stage', 'title' => 'Periods absent due to medication or a health condition', 'description' => null, 'sort_order' => 9],
            ['category' => 'hormonal_life_stage', 'title' => 'Hysterectomy or reproductive surgery',              'description' => null, 'sort_order' => 10],
            ['category' => 'hormonal_life_stage', 'title' => 'Not applicable',                                    'description' => null, 'sort_order' => 11],
            ['category' => 'hormonal_life_stage', 'title' => 'Prefer not to answer',                              'description' => null, 'sort_order' => 12],
    

            // ========================================================================================================================================================================
            //CHAPTER 04: Food & Digestion
            // ==========================================
            // ==========================================

            // EATING PATTERN (single-select) *
            // ==========================================
            ['category' => 'eating_pattern', 'title' => 'Vegetarian',     'description' => null, 'sort_order' => 1],
            ['category' => 'eating_pattern', 'title' => 'Non-vegetarian', 'description' => null, 'sort_order' => 2],
            ['category' => 'eating_pattern', 'title' => 'Vegan',          'description' => null, 'sort_order' => 3],
            ['category' => 'eating_pattern', 'title' => 'Pure Jain',      'description' => null, 'sort_order' => 4],
            ['category' => 'eating_pattern', 'title' => 'Eggitarian',     'description' => null, 'sort_order' => 5],
            ['category' => 'eating_pattern', 'title' => 'Other',          'description' => null, 'sort_order' => 6],

            // ==========================================
            // CULTURAL & FASTING PRACTICES (multi-select) *
            // ==========================================
            ['category' => 'cultural_fasting_practices', 'title' => 'Jain',                             'description' => null, 'sort_order' => 1],
            ['category' => 'cultural_fasting_practices', 'title' => 'No onion or garlic',               'description' => null, 'sort_order' => 2],
            ['category' => 'cultural_fasting_practices', 'title' => 'Paryushan',                        'description' => null, 'sort_order' => 3],
            ['category' => 'cultural_fasting_practices', 'title' => 'Navratri fasting',                 'description' => null, 'sort_order' => 4],
            ['category' => 'cultural_fasting_practices', 'title' => 'Somvar or another regular fast',   'description' => null, 'sort_order' => 5],
            ['category' => 'cultural_fasting_practices', 'title' => 'Ramadan',                          'description' => null, 'sort_order' => 6],
            ['category' => 'cultural_fasting_practices', 'title' => 'Other',                            'description' => null, 'sort_order' => 7],
            ['category' => 'cultural_fasting_practices', 'title' => 'None',                             'description' => null, 'sort_order' => 8],

            // ==========================================
            // FOOD ENVIRONMENT *
            // ==========================================
            // Who usually cooks
            ['category' => 'food_env_who_cooks', 'title' => 'Self',                     'description' => null, 'sort_order' => 1],
            ['category' => 'food_env_who_cooks', 'title' => 'Family',                   'description' => null, 'sort_order' => 2],
            ['category' => 'food_env_who_cooks', 'title' => 'Cook',                     'description' => null, 'sort_order' => 3],
            ['category' => 'food_env_who_cooks', 'title' => 'Canteen',                  'description' => null, 'sort_order' => 4],
            ['category' => 'food_env_who_cooks', 'title' => 'Delivery or combination',  'description' => null, 'sort_order' => 5],

            // How supportive they are
            ['category' => 'food_env_support_level', 'title' => 'Supportive',      'description' => null, 'sort_order' => 1],
            ['category' => 'food_env_support_level', 'title' => 'Neutral',         'description' => null, 'sort_order' => 2],
            ['category' => 'food_env_support_level', 'title' => 'Resistant',       'description' => null, 'sort_order' => 3],
            ['category' => 'food_env_support_level', 'title' => 'Not applicable',  'description' => null, 'sort_order' => 4],

            // Meals ordered or eaten out per week
            ['category' => 'food_env_meals_out_per_week', 'title' => '0–1', 'description' => null, 'sort_order' => 1],
            ['category' => 'food_env_meals_out_per_week', 'title' => '2–3', 'description' => null, 'sort_order' => 2],
            ['category' => 'food_env_meals_out_per_week', 'title' => '4–6', 'description' => null, 'sort_order' => 3],
            ['category' => 'food_env_meals_out_per_week', 'title' => '7+',  'description' => null, 'sort_order' => 4],

            // ==========================================
            // DAILY INTAKE OVERVIEW *
            // ==========================================
            // Fruit
            ['category' => 'intake_fruit', 'title' => 'None',       'description' => null, 'sort_order' => 1],
            ['category' => 'intake_fruit', 'title' => '1 serving',  'description' => null, 'sort_order' => 2],
            ['category' => 'intake_fruit', 'title' => '2 servings', 'description' => null, 'sort_order' => 3],
            ['category' => 'intake_fruit', 'title' => '3+',         'description' => null, 'sort_order' => 4],

            // Vegetables
            ['category' => 'intake_vegetables', 'title' => 'None',       'description' => null, 'sort_order' => 1],
            ['category' => 'intake_vegetables', 'title' => '1 serving',  'description' => null, 'sort_order' => 2],
            ['category' => 'intake_vegetables', 'title' => '2 servings', 'description' => null, 'sort_order' => 3],
            ['category' => 'intake_vegetables', 'title' => '3+',         'description' => null, 'sort_order' => 4],

            // Water
            ['category' => 'intake_water', 'title' => 'Below 1 L', 'description' => null, 'sort_order' => 1],
            ['category' => 'intake_water', 'title' => '1–2 L',     'description' => null, 'sort_order' => 2],
            ['category' => 'intake_water', 'title' => '2–3 L',     'description' => null, 'sort_order' => 3],
            ['category' => 'intake_water', 'title' => '3–4 L',     'description' => null, 'sort_order' => 4],
            ['category' => 'intake_water', 'title' => 'Above 4 L', 'description' => null, 'sort_order' => 5],

            // Caffeinated drinks
            ['category' => 'intake_caffeine', 'title' => 'None', 'description' => null, 'sort_order' => 1],
            ['category' => 'intake_caffeine', 'title' => '1',    'description' => null, 'sort_order' => 2],
            ['category' => 'intake_caffeine', 'title' => '2',    'description' => null, 'sort_order' => 3],
            ['category' => 'intake_caffeine', 'title' => '3+',   'description' => null, 'sort_order' => 4],

            // Sugary foods or drinks
            ['category' => 'intake_sugary', 'title' => 'Rarely',     'description' => null, 'sort_order' => 1],
            ['category' => 'intake_sugary', 'title' => '1–2 weekly', 'description' => null, 'sort_order' => 2],
            ['category' => 'intake_sugary', 'title' => '3–4 weekly', 'description' => null, 'sort_order' => 3],
            ['category' => 'intake_sugary', 'title' => 'Daily',      'description' => null, 'sort_order' => 4],

            // ==========================================
            // SUBSTANCE HABITS (multi-select) *
            // ==========================================
            ['category' => 'substance_habits', 'title' => 'No',                      'description' => null, 'sort_order' => 1],
            ['category' => 'substance_habits', 'title' => 'Alcohol',                 'description' => null, 'sort_order' => 2],
            ['category' => 'substance_habits', 'title' => 'Cigarettes',              'description' => null, 'sort_order' => 3],
            ['category' => 'substance_habits', 'title' => 'Vaping',                  'description' => null, 'sort_order' => 4],
            ['category' => 'substance_habits', 'title' => 'Recreational substances', 'description' => null, 'sort_order' => 5],
            ['category' => 'substance_habits', 'title' => 'Prefer not to answer',    'description' => null, 'sort_order' => 6],

            // ==========================================
            // DIGESTIVE HEALTH (multi-select) *
            // ==========================================
            ['category' => 'digestive_health', 'title' => 'Bloating',                                   'description' => null, 'sort_order' => 1],
            ['category' => 'digestive_health', 'title' => 'Gas or frequent burping',                    'description' => null, 'sort_order' => 2],
            ['category' => 'digestive_health', 'title' => 'Acidity or reflux',                          'description' => null, 'sort_order' => 3],
            ['category' => 'digestive_health', 'title' => 'Constipation',                               'description' => null, 'sort_order' => 4],
            ['category' => 'digestive_health', 'title' => 'Loose stools',                               'description' => null, 'sort_order' => 5],
            ['category' => 'digestive_health', 'title' => 'Alternating constipation and loose stools',  'description' => null, 'sort_order' => 6],
            ['category' => 'digestive_health', 'title' => 'Abdominal pain or cramps',                   'description' => null, 'sort_order' => 7],
            ['category' => 'digestive_health', 'title' => 'Nausea',                                     'description' => null, 'sort_order' => 8],
            ['category' => 'digestive_health', 'title' => 'Feeling sleepy or very heavy after meals',   'description' => null, 'sort_order' => 9],
            ['category' => 'digestive_health', 'title' => 'None',                                       'description' => null, 'sort_order' => 10],

            // ========================================================================================================================================================================
            //CHAPTER 05: Life & Coaching Fit
            // ==========================================
            // ==========================================
            // ==========================================
            // SLEEP: Night waking frequency (single-select)
            // ==========================================
            ['category' => 'sleep_night_waking', 'title' => 'Never',           'description' => null, 'sort_order' => 1],
            ['category' => 'sleep_night_waking', 'title' => 'Monthly',         'description' => null, 'sort_order' => 2],
            ['category' => 'sleep_night_waking', 'title' => '1–2 times weekly', 'description' => null, 'sort_order' => 3],
            ['category' => 'sleep_night_waking', 'title' => '3+ times weekly', 'description' => null, 'sort_order' => 4],
            ['category' => 'sleep_night_waking', 'title' => 'Most nights',     'description' => null, 'sort_order' => 5],

            // SLEEP: First hour after waking (single-select)
            ['category' => 'sleep_first_hour', 'title' => 'Ready to go',             'description' => null, 'sort_order' => 1],
            ['category' => 'sleep_first_hour', 'title' => 'Slow but okay',           'description' => null, 'sort_order' => 2],
            ['category' => 'sleep_first_hour', 'title' => 'Foggy and reluctant',     'description' => null, 'sort_order' => 3],
            ['category' => 'sleep_first_hour', 'title' => 'Need caffeine to function', 'description' => null, 'sort_order' => 4],

            // ==========================================
            // ACTIVITY & MOVEMENT *
            // ==========================================
            // Usual daily activity (single-select)
            ['category' => 'activity_level', 'title' => 'Mostly seated with little movement',          'description' => null, 'sort_order' => 1],
            ['category' => 'activity_level', 'title' => 'Mostly seated but some regular walking',      'description' => null, 'sort_order' => 2],
            ['category' => 'activity_level', 'title' => 'On my feet for part of the day',              'description' => null, 'sort_order' => 3],
            ['category' => 'activity_level', 'title' => 'Physically active work or high daily movement', 'description' => null, 'sort_order' => 4],
            ['category' => 'activity_level', 'title' => 'Activity varies significantly by day',        'description' => null, 'sort_order' => 5],

            // Average daily steps (single-select)
            ['category' => 'activity_daily_steps', 'title' => 'Not tracked',   'description' => null, 'sort_order' => 1],
            ['category' => 'activity_daily_steps', 'title' => 'Below 3,000',   'description' => null, 'sort_order' => 2],
            ['category' => 'activity_daily_steps', 'title' => '3,000–4,999',   'description' => null, 'sort_order' => 3],
            ['category' => 'activity_daily_steps', 'title' => '5,000–7,499',   'description' => null, 'sort_order' => 4],
            ['category' => 'activity_daily_steps', 'title' => '7,500–9,999',   'description' => null, 'sort_order' => 5],
            ['category' => 'activity_daily_steps', 'title' => '10,000–12,499', 'description' => null, 'sort_order' => 6],
            ['category' => 'activity_daily_steps', 'title' => '12,500+',       'description' => null, 'sort_order' => 7],

            // Current workouts per week (single-select)
            ['category' => 'activity_workouts_per_week', 'title' => '0',  'description' => null, 'sort_order' => 1],
            ['category' => 'activity_workouts_per_week', 'title' => '1',  'description' => null, 'sort_order' => 2],
            ['category' => 'activity_workouts_per_week', 'title' => '2',  'description' => null, 'sort_order' => 3],
            ['category' => 'activity_workouts_per_week', 'title' => '3',  'description' => null, 'sort_order' => 4],
            ['category' => 'activity_workouts_per_week', 'title' => '4',  'description' => null, 'sort_order' => 5],
            ['category' => 'activity_workouts_per_week', 'title' => '5+', 'description' => null, 'sort_order' => 6],

            // Days per week you could realistically make time for movement (single-select)
            ['category' => 'activity_available_days', 'title' => '0',  'description' => null, 'sort_order' => 1],
            ['category' => 'activity_available_days', 'title' => '1',  'description' => null, 'sort_order' => 2],
            ['category' => 'activity_available_days', 'title' => '2',  'description' => null, 'sort_order' => 3],
            ['category' => 'activity_available_days', 'title' => '3',  'description' => null, 'sort_order' => 4],
            ['category' => 'activity_available_days', 'title' => '4',  'description' => null, 'sort_order' => 5],
            ['category' => 'activity_available_days', 'title' => '5+', 'description' => null, 'sort_order' => 6],

            // ==========================================
            // STRESS: What tends to change first when stress is high? (multi-select)
            // ==========================================
            ['category' => 'stress_first_changes', 'title' => 'Food choices or cravings',       'description' => null, 'sort_order' => 1],
            ['category' => 'stress_first_changes', 'title' => 'Meal timing',                    'description' => null, 'sort_order' => 2],
            ['category' => 'stress_first_changes', 'title' => 'Sleep',                          'description' => null, 'sort_order' => 3],
            ['category' => 'stress_first_changes', 'title' => 'Movement',                       'description' => null, 'sort_order' => 4],
            ['category' => 'stress_first_changes', 'title' => 'Mood or patience',               'description' => null, 'sort_order' => 5],
            ['category' => 'stress_first_changes', 'title' => 'Digestion',                      'description' => null, 'sort_order' => 6],
            ['category' => 'stress_first_changes', 'title' => 'Check-ins or communication',     'description' => null, 'sort_order' => 7],
            ['category' => 'stress_first_changes', 'title' => 'Nothing noticeable',             'description' => null, 'sort_order' => 8],
            ['category' => 'stress_first_changes', 'title' => 'Other',                          'description' => null, 'sort_order' => 9],

            // ==========================================
            // READINESS & COACHING PARTNERSHIP
            // ==========================================
            // How ready do you feel to make changes right now? (single-select, 1-10)
            ['category' => 'readiness_score', 'title' => '1',  'description' => 'Hesitant / exploring',      'sort_order' => 1],
            ['category' => 'readiness_score', 'title' => '2',  'description' => null,                        'sort_order' => 2],
            ['category' => 'readiness_score', 'title' => '3',  'description' => null,                        'sort_order' => 3],
            ['category' => 'readiness_score', 'title' => '4',  'description' => null,                        'sort_order' => 4],
            ['category' => 'readiness_score', 'title' => '5',  'description' => null,                        'sort_order' => 5],
            ['category' => 'readiness_score', 'title' => '6',  'description' => null,                        'sort_order' => 6],
            ['category' => 'readiness_score', 'title' => '7',  'description' => null,                        'sort_order' => 7],
            ['category' => 'readiness_score', 'title' => '8',  'description' => null,                        'sort_order' => 8],
            ['category' => 'readiness_score', 'title' => '9',  'description' => null,                        'sort_order' => 9],
            ['category' => 'readiness_score', 'title' => '10', 'description' => 'Fully committed & ready',   'sort_order' => 10],

            // What most commonly interrupts your consistency? (multi-select, up to 3)
            ['category' => 'consistency_interrupters', 'title' => 'Work pressure',                 'description' => null, 'sort_order' => 1],
            ['category' => 'consistency_interrupters', 'title' => 'Travel',                        'description' => null, 'sort_order' => 2],
            ['category' => 'consistency_interrupters', 'title' => 'Social events',                 'description' => null, 'sort_order' => 3],
            ['category' => 'consistency_interrupters', 'title' => 'Family responsibilities',       'description' => null, 'sort_order' => 4],
            ['category' => 'consistency_interrupters', 'title' => 'Poor sleep',                    'description' => null, 'sort_order' => 5],
            ['category' => 'consistency_interrupters', 'title' => 'Low energy',                    'description' => null, 'sort_order' => 6],
            ['category' => 'consistency_interrupters', 'title' => 'Cravings or emotional eating',  'description' => null, 'sort_order' => 7],
            ['category' => 'consistency_interrupters', 'title' => 'Pain or illness',               'description' => null, 'sort_order' => 8],
            ['category' => 'consistency_interrupters', 'title' => 'Lack of food preparation',      'description' => null, 'sort_order' => 9],
            ['category' => 'consistency_interrupters', 'title' => 'All-or-nothing thinking',       'description' => null, 'sort_order' => 10],
            ['category' => 'consistency_interrupters', 'title' => 'Limited support at home',       'description' => null, 'sort_order' => 11],
            ['category' => 'consistency_interrupters', 'title' => 'Other',                         'description' => null, 'sort_order' => 12],


            // ========================================================================================================================================================================
            //CHAPTER 06: Mindset & Sustainability
            // ==========================================
            // ==========================================

             // ==========================================
            // GOAL-ORIENTED MINDSET (multi-select, up to 3) *
            // ==========================================
            ['category' => 'goal_mindset', 'title' => 'I do best with a clear routine',                          'description' => null, 'sort_order' => 1],
            ['category' => 'goal_mindset', 'title' => 'I need flexibility or I feel restricted',                 'description' => null, 'sort_order' => 2],
            ['category' => 'goal_mindset', 'title' => 'Numbers and data motivate me',                            'description' => null, 'sort_order' => 3],
            ['category' => 'goal_mindset', 'title' => 'Too much tracking overwhelms me',                         'description' => null, 'sort_order' => 4],
            ['category' => 'goal_mindset', 'title' => 'I prefer simple instructions and fewer choices',          'description' => null, 'sort_order' => 5],
            ['category' => 'goal_mindset', 'title' => 'I like understanding the reason behind every recommendation', 'description' => null, 'sort_order' => 6],
            ['category' => 'goal_mindset', 'title' => 'I can become all-or-nothing after one difficult day',      'description' => null, 'sort_order' => 7],
            ['category' => 'goal_mindset', 'title' => 'I am usually self-directed',                              'description' => null, 'sort_order' => 8],
            ['category' => 'goal_mindset', 'title' => 'Regular accountability keeps me consistent',              'description' => null, 'sort_order' => 9],
            ['category' => 'goal_mindset', 'title' => 'I get bored when a routine feels repetitive',             'description' => null, 'sort_order' => 10],
            ['category' => 'goal_mindset', 'title' => 'I am hard on myself when I do not do something perfectly', 'description' => null, 'sort_order' => 11],
            ['category' => 'goal_mindset', 'title' => 'Small visible wins keep me motivated',                    'description' => null, 'sort_order' => 12],
            ['category' => 'goal_mindset', 'title' => 'Other',   'description' => null, 'sort_order' => 13],

        ];

        foreach ($masters as $item) {
            OnboardingMaster::updateOrCreate(
                [
                    'category' => $item['category'],
                    'title'    => $item['title'],
                ],
                [
                    'uuid'        => Str::uuid(),
                    'description' => $item['description'] ?? null,
                    'sort_order'  => $item['sort_order'] ?? 0,
                    'status'      => 'Active',
                ]
            );
        }
    }
}
