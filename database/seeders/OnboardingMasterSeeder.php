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
            // ==========================================
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


            // ==========================================
            // CHAPTER 02: Goals & Starting Point
            // ==========================================
            [
                'category'    => 'goal_type',
                'title'       => 'Better digestion & energy vitality',
                'description' => 'Wake up without bloating and sustain natural daily stamina.',
                'sort_order'  => 1,
            ],
            [
                'category'    => 'goal_type',
                'title'       => 'Healthy & sustainable fat loss',
                'description' => 'Lose body fat without extreme or restrictive dieting.',
                'sort_order'  => 2,
            ],
            [
                'category'    => 'goal_type',
                'title'       => 'Build lean muscle & strength',
                'description' => 'Optimize protein intake and movement to improve body composition.',
                'sort_order'  => 3,
            ],
            [
                'category'    => 'goal_type',
                'title'       => 'Balance blood sugar & hormones',
                'description' => 'Support insulin balance, PCOS, thyroid, or cholesterol markers.',
                'sort_order'  => 4,
            ],
            [
                'category'    => 'goal_type',
                'title'       => 'Improve sleep quality & morning recovery',
                'description' => 'Sleep deeply through the night and wake feeling refreshed.',
                'sort_order'  => 5,
            ],
            [
                'category'    => 'goal_type',
                'title'       => 'Reduce stress-related or emotional eating',
                'description' => 'Build a calm, mindful, and intuitive relationship with food.',
                'sort_order'  => 6,
            ],
            [
                'category'    => 'goal_type',
                'title'       => 'Build long-term sustainable daily habits',
                'description' => 'Create routines that easily fit into career, travel, and family life.',
                'sort_order'  => 7,
            ],

            // ==========================================
            // CHAPTER 03: Relevant Health & Safety (Medical Conditions)
            // ==========================================
            ['category' => 'health_condition', 'title' => 'None / No known conditions', 'description' => 'No active physician-diagnosed conditions', 'sort_order' => 1],
            ['category' => 'health_condition', 'title' => 'Type 2 Diabetes', 'description' => null, 'sort_order' => 2],
            ['category' => 'health_condition', 'title' => 'Pre-diabetes / Insulin Resistance', 'description' => null, 'sort_order' => 3],
            ['category' => 'health_condition', 'title' => 'Hypertension (High Blood Pressure)', 'description' => null, 'sort_order' => 4],
            ['category' => 'health_condition', 'title' => 'Hypothyroidism / Hashimoto\'s', 'description' => null, 'sort_order' => 5],
            ['category' => 'health_condition', 'title' => 'PCOS / PCOD', 'description' => null, 'sort_order' => 6],
            ['category' => 'health_condition', 'title' => 'Fatty Liver (NAFLD)', 'description' => null, 'sort_order' => 7],
            ['category' => 'health_condition', 'title' => 'Cardiovascular Condition', 'description' => null, 'sort_order' => 8],
            ['category' => 'health_condition', 'title' => 'High Cholesterol / Dyslipidemia', 'description' => null, 'sort_order' => 9],
            ['category' => 'health_condition', 'title' => 'Autoimmune Condition', 'description' => null, 'sort_order' => 10],
            ['category' => 'health_condition', 'title' => 'Chronic Kidney / Liver Condition', 'description' => null, 'sort_order' => 11],
            ['category' => 'health_condition', 'title' => 'Other Condition', 'description' => null, 'sort_order' => 12],

            // Family Medical History
            ['category' => 'family_history', 'title' => 'Diabetes', 'description' => null, 'sort_order' => 1],
            ['category' => 'family_history', 'title' => 'Heart disease / High Blood Pressure', 'description' => null, 'sort_order' => 2],
            ['category' => 'family_history', 'title' => 'Thyroid condition', 'description' => null, 'sort_order' => 3],
            ['category' => 'family_history', 'title' => 'Autoimmune disease', 'description' => null, 'sort_order' => 4],
            ['category' => 'family_history', 'title' => 'Obesity / Metabolic issues', 'description' => null, 'sort_order' => 5],
            ['category' => 'family_history', 'title' => 'Cancer', 'description' => null, 'sort_order' => 6],
            ['category' => 'family_history', 'title' => 'None that I know of', 'description' => null, 'sort_order' => 7],
            ['category' => 'family_history', 'title' => 'Other', 'description' => null, 'sort_order' => 8],

            // Recent Lab Work Markers
            ['category' => 'lab_work_marker', 'title' => 'Fasting Blood Glucose', 'description' => null, 'sort_order' => 1],
            ['category' => 'lab_work_marker', 'title' => 'HbA1c', 'description' => null, 'sort_order' => 2],
            ['category' => 'lab_work_marker', 'title' => 'Lipid Panel (Cholesterol / Triglycerides)', 'description' => null, 'sort_order' => 3],
            ['category' => 'lab_work_marker', 'title' => 'Full Thyroid Panel (TSH, FT3, FT4)', 'description' => null, 'sort_order' => 4],
            ['category' => 'lab_work_marker', 'title' => 'Vitamin D / B12', 'description' => null, 'sort_order' => 5],
            ['category' => 'lab_work_marker', 'title' => 'CBC (Complete Blood Count)', 'description' => null, 'sort_order' => 6],
            ['category' => 'lab_work_marker', 'title' => 'Liver Function Test (LFT)', 'description' => null, 'sort_order' => 7],
            ['category' => 'lab_work_marker', 'title' => 'Kidney Function Test (KFT)', 'description' => null, 'sort_order' => 8],
            ['category' => 'lab_work_marker', 'title' => 'None in the last 12 months', 'description' => null, 'sort_order' => 9],

            // Allergies & Intolerance Level
            ['category' => 'allergy_tolerance_type', 'title' => 'No known allergies or intolerances', 'description' => null, 'sort_order' => 1],
            ['category' => 'allergy_tolerance_type', 'title' => 'Mild food sensitivities', 'description' => null, 'sort_order' => 2],
            ['category' => 'allergy_tolerance_type', 'title' => 'Confirmed food allergies', 'description' => null, 'sort_order' => 3],
            ['category' => 'allergy_tolerance_type', 'title' => 'Severe / Anaphylactic allergy', 'description' => null, 'sort_order' => 4],

            // ==========================================
            // CHAPTER 04: Food & Digestion
            // ==========================================
            ['category' => 'diet_type', 'title' => 'Vegetarian', 'description' => 'Plant-based diet including dairy products', 'sort_order' => 1],
            ['category' => 'diet_type', 'title' => 'Eggetarian', 'description' => 'Vegetarian diet that includes eggs', 'sort_order' => 2],
            ['category' => 'diet_type', 'title' => 'Vegan', 'description' => '100% plant-based with zero animal products', 'sort_order' => 3],
            ['category' => 'diet_type', 'title' => 'Non-Vegetarian', 'description' => 'Includes poultry, meats, and seafood', 'sort_order' => 4],
            ['category' => 'diet_type', 'title' => 'Jain', 'description' => 'Vegetarian without onion, garlic, or root vegetables', 'sort_order' => 5],
            ['category' => 'diet_type', 'title' => 'Pescatarian', 'description' => 'Plant-based plus fish and seafood', 'sort_order' => 6],
            ['category' => 'diet_type', 'title' => 'Other Diet', 'description' => 'Custom dietary pattern', 'sort_order' => 7],

            // Digestive Symptoms
            ['category' => 'digestive_symptom', 'title' => 'Bloating', 'description' => 'Abdominal fullness or swelling after eating', 'sort_order' => 1],
            ['category' => 'digestive_symptom', 'title' => 'Gas / Flatulence', 'description' => 'Excessive intestinal gas', 'sort_order' => 2],
            ['category' => 'digestive_symptom', 'title' => 'Acid Reflux / Heartburn', 'description' => 'Burning sensation in chest or throat', 'sort_order' => 3],
            ['category' => 'digestive_symptom', 'title' => 'Constipation', 'description' => 'Infrequent or difficult bowel movements', 'sort_order' => 4],
            ['category' => 'digestive_symptom', 'title' => 'Diarrhea / Loose Stools', 'description' => 'Frequent watery bowel movements', 'sort_order' => 5],
            ['category' => 'digestive_symptom', 'title' => 'Nausea', 'description' => 'Queasiness or upset stomach', 'sort_order' => 6],
            ['category' => 'digestive_symptom', 'title' => 'Abdominal pain / cramping', 'description' => 'Stomach aches or intestinal discomfort', 'sort_order' => 7],
            ['category' => 'digestive_symptom', 'title' => 'Post-meal fatigue / sluggishness', 'description' => 'Heavy energy crash within 1-2 hours of meals', 'sort_order' => 8],
            ['category' => 'digestive_symptom', 'title' => 'None / Rare discomfort', 'description' => 'Digestion feels smooth and comfortable', 'sort_order' => 9],

            // Bristol Stool Scale
            [
                'category'    => 'bristol_stool_type',
                'title'       => 'Type 1: Separate hard lumps',
                'description' => 'Hard to pass; like nuts (indicates severe constipation)',
                'sort_order'  => 1,
            ],
            [
                'category'    => 'bristol_stool_type',
                'title'       => 'Type 2: Sausage-shaped, but lumpy',
                'description' => 'Lumpy and firm (indicates mild constipation)',
                'sort_order'  => 2,
            ],
            [
                'category'    => 'bristol_stool_type',
                'title'       => 'Type 3: Like a sausage with cracks on surface',
                'description' => 'Formed stool with surface fissures (normal)',
                'sort_order'  => 3,
            ],
            [
                'category'    => 'bristol_stool_type',
                'title'       => 'Type 4: Like a sausage or snake, smooth and soft',
                'description' => 'Smooth, soft, easy to pass (the golden standard)',
                'sort_order'  => 4,
            ],
            [
                'category'    => 'bristol_stool_type',
                'title'       => 'Type 5: Soft blobs with clear-cut edges',
                'description' => 'Easy to pass (lacking dietary fiber)',
                'sort_order'  => 5,
            ],
            [
                'category'    => 'bristol_stool_type',
                'title'       => 'Type 6: Fluffy pieces with ragged edges, mushy stool',
                'description' => 'Mushy consistency (indicates mild diarrhea/inflammation)',
                'sort_order'  => 6,
            ],
            [
                'category'    => 'bristol_stool_type',
                'title'       => 'Type 7: Watery, no solid pieces',
                'description' => 'Entirely liquid (indicates diarrhea / acute gut upset)',
                'sort_order'  => 7,
            ],

            // Hydration Range
            ['category' => 'hydration_range', 'title' => 'Under 1 Liter', 'description' => null, 'sort_order' => 1],
            ['category' => 'hydration_range', 'title' => '1 - 2 Liters', 'description' => null, 'sort_order' => 2],
            ['category' => 'hydration_range', 'title' => '2 - 3 Liters', 'description' => null, 'sort_order' => 3],
            ['category' => 'hydration_range', 'title' => '3+ Liters', 'description' => null, 'sort_order' => 4],

            // ==========================================
            // CHAPTER 05: Lifestyle & Coaching Fit
            // ==========================================
            // Night waking frequency
            ['category' => 'sleep_waking_frequency', 'title' => 'Never', 'description' => 'Sleep straight through the night', 'sort_order' => 1],
            ['category' => 'sleep_waking_frequency', 'title' => 'Rarely', 'description' => 'Once every few weeks', 'sort_order' => 2],
            ['category' => 'sleep_waking_frequency', 'title' => '1-2 times weekly', 'description' => 'Occasional nighttime wakefulness', 'sort_order' => 3],
            ['category' => 'sleep_waking_frequency', 'title' => '3-4 times weekly', 'description' => 'Frequent sleep disruption', 'sort_order' => 4],
            ['category' => 'sleep_waking_frequency', 'title' => 'Most nights', 'description' => 'Wake up almost every single night', 'sort_order' => 5],

            // Waking state
            ['category' => 'waking_state', 'title' => 'Rested & clear', 'description' => 'Ready to take on the day with high clarity', 'sort_order' => 1],
            ['category' => 'waking_state', 'title' => 'Slow but steady', 'description' => 'Need 30-45 minutes to ease into the morning', 'sort_order' => 2],
            ['category' => 'waking_state', 'title' => 'Heavy and need recovery', 'description' => 'Feel groggy, fatigued, or unrefreshed', 'sort_order' => 3],
            ['category' => 'waking_state', 'title' => 'Variable from day to day', 'description' => 'Changes based on previous day demands', 'sort_order' => 4],

            // Daily activity level
            ['category' => 'activity_level', 'title' => 'Mostly seated with little movement', 'description' => 'Desk-bound work, minimal daily walking', 'sort_order' => 1],
            ['category' => 'activity_level', 'title' => 'Mostly seated but some regular walking', 'description' => 'Desk job with daily strolls or errands', 'sort_order' => 2],
            ['category' => 'activity_level', 'title' => 'On my feet for parts of the day', 'description' => 'Standing, moving between meetings/errands', 'sort_order' => 3],
            ['category' => 'activity_level', 'title' => 'Physically active throughout the day', 'description' => 'Constantly moving, manual or fitness role', 'sort_order' => 4],
            ['category' => 'activity_level', 'title' => 'Activity varies significantly day-to-day', 'description' => 'Unpredictable schedule or travel heavy', 'sort_order' => 5],

            // Daily step range
            ['category' => 'daily_step_range', 'title' => 'Under 3,000', 'description' => null, 'sort_order' => 1],
            ['category' => 'daily_step_range', 'title' => '3,000 - 5,000', 'description' => null, 'sort_order' => 2],
            ['category' => 'daily_step_range', 'title' => '5,000 - 7,500', 'description' => null, 'sort_order' => 3],
            ['category' => 'daily_step_range', 'title' => '7,500 - 10,000', 'description' => null, 'sort_order' => 4],
            ['category' => 'daily_step_range', 'title' => '10,000 - 12,000', 'description' => null, 'sort_order' => 5],
            ['category' => 'daily_step_range', 'title' => '12,000+ or don\'t track', 'description' => null, 'sort_order' => 6],
            ['category' => 'daily_step_range', 'title' => 'Varies widely', 'description' => null, 'sort_order' => 7],

            // Stress physical symptoms
            ['category' => 'stress_symptom', 'title' => 'Neck / shoulder tension or tightness', 'description' => null, 'sort_order' => 1],
            ['category' => 'stress_symptom', 'title' => 'Tension headaches', 'description' => null, 'sort_order' => 2],
            ['category' => 'stress_symptom', 'title' => 'Jaw clenching', 'description' => null, 'sort_order' => 3],
            ['category' => 'stress_symptom', 'title' => 'Shallow breathing', 'description' => null, 'sort_order' => 4],
            ['category' => 'stress_symptom', 'title' => 'Digestive flare-ups', 'description' => null, 'sort_order' => 5],
            ['category' => 'stress_symptom', 'title' => 'Difficulty falling or staying asleep', 'description' => null, 'sort_order' => 6],
            ['category' => 'stress_symptom', 'title' => 'Restlessness / fatigue', 'description' => null, 'sort_order' => 7],
            ['category' => 'stress_symptom', 'title' => 'Other physical symptom', 'description' => null, 'sort_order' => 8],

            // Lifestyle Challenges & Obstacles
            ['category' => 'lifestyle_challenge', 'title' => 'Time & scheduling', 'description' => null, 'sort_order' => 1],
            ['category' => 'lifestyle_challenge', 'title' => 'Travel', 'description' => null, 'sort_order' => 2],
            ['category' => 'lifestyle_challenge', 'title' => 'Work demands', 'description' => null, 'sort_order' => 3],
            ['category' => 'lifestyle_challenge', 'title' => 'Family responsibilities', 'description' => null, 'sort_order' => 4],
            ['category' => 'lifestyle_challenge', 'title' => 'Emotional eating', 'description' => null, 'sort_order' => 5],
            ['category' => 'lifestyle_challenge', 'title' => 'Low energy', 'description' => null, 'sort_order' => 6],
            ['category' => 'lifestyle_challenge', 'title' => 'Cravings & sugar cravings', 'description' => null, 'sort_order' => 7],
            ['category' => 'lifestyle_challenge', 'title' => 'Social events / dining out', 'description' => null, 'sort_order' => 8],
            ['category' => 'lifestyle_challenge', 'title' => 'Lack of food preparation', 'description' => null, 'sort_order' => 9],
            ['category' => 'lifestyle_challenge', 'title' => 'All-or-nothing mindset', 'description' => null, 'sort_order' => 10],
            ['category' => 'lifestyle_challenge', 'title' => 'Limited support at home', 'description' => null, 'sort_order' => 11],
            ['category' => 'lifestyle_challenge', 'title' => 'Other challenge', 'description' => null, 'sort_order' => 12],

            // Coaching support style
            [
                'category'    => 'coaching_support_style',
                'title'       => 'Daily check-ins & high accountability',
                'description' => 'Hands-on feedback and continuous daily momentum.',
                'sort_order'  => 1,
            ],
            [
                'category'    => 'coaching_support_style',
                'title'       => 'Weekly structured reviews',
                'description' => 'Independence during the week with a comprehensive weekly review.',
                'sort_order'  => 2,
            ],
            [
                'category'    => 'coaching_support_style',
                'title'       => 'Gentle guidance & flexibility',
                'description' => 'A non-judgmental, forgiving pace focused on gradual habits.',
                'sort_order'  => 3,
            ],
            [
                'category'    => 'coaching_support_style',
                'title'       => 'Data-driven & analytical feedback',
                'description' => 'Metrics, blood markers, sleep data, and quantitative tracking.',
                'sort_order'  => 4,
            ],
            [
                'category'    => 'coaching_support_style',
                'title'       => 'Direct & goal-oriented nudges',
                'description' => 'Clear, concise action items without ambiguity.',
                'sort_order'  => 5,
            ],

            // ==========================================
            // CHAPTER 06: Mindset & Sustainability
            // ==========================================
            ['category' => 'mindset_statement', 'title' => 'I do best with a clear routine', 'description' => null, 'sort_order' => 1],
            ['category' => 'mindset_statement', 'title' => 'I need flexibility or I feel restricted', 'description' => null, 'sort_order' => 2],
            ['category' => 'mindset_statement', 'title' => 'Numbers and data motivate me', 'description' => null, 'sort_order' => 3],
            ['category' => 'mindset_statement', 'title' => 'Too much tracking overwhelms me', 'description' => null, 'sort_order' => 4],
            ['category' => 'mindset_statement', 'title' => 'I prefer simple instructions and fewer choices', 'description' => null, 'sort_order' => 5],
            ['category' => 'mindset_statement', 'title' => 'I like understanding the reason behind every recommendation', 'description' => null, 'sort_order' => 6],
            ['category' => 'mindset_statement', 'title' => 'I can become all-or-nothing after one difficult day', 'description' => null, 'sort_order' => 7],
            ['category' => 'mindset_statement', 'title' => 'I am usually self-directed', 'description' => null, 'sort_order' => 8],
            ['category' => 'mindset_statement', 'title' => 'Regular accountability keeps me consistent', 'description' => null, 'sort_order' => 9],
            ['category' => 'mindset_statement', 'title' => 'I get bored when a routine feels repetitive', 'description' => null, 'sort_order' => 10],
            ['category' => 'mindset_statement', 'title' => 'I am hard on myself when I do not do something perfectly', 'description' => null, 'sort_order' => 11],
            ['category' => 'mindset_statement', 'title' => 'Small visible wins keep me motivated', 'description' => null, 'sort_order' => 12],
            ['category' => 'mindset_statement', 'title' => 'Other mindset tendency', 'description' => null, 'sort_order' => 13],
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
