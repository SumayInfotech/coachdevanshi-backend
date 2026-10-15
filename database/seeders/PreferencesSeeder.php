<?php

namespace Database\Seeders;

use App\Models\Preference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PreferencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preferences = [

            // Dietary Preferences
            [
                'title' => 'Everything',
                'description' => 'No restrictions',
                'type' => 'dietary',
            ],
            [
                'title' => 'Vegetarian',
                'description' => 'Plant & dairy',
                'type' => 'dietary',
            ],
            [
                'title' => 'Vegan',
                'description' => '100% plant-based',
                'type' => 'dietary',
            ],
            [
                'title' => 'Pescatarian',
                'description' => 'Fish & plants',
                'type' => 'dietary',
            ],
            [
                'title' => 'Keto',
                'description' => 'Low carb life',
                'type' => 'dietary',
            ],
            [
                'title' => 'Paleo',
                'description' => 'Primal eating',
                'type' => 'dietary',
            ],

            // Goals
            [
                'title' => 'Weight loss',
                'description' => null,
                'type' => 'goals',
            ],
            [
                'title' => 'Muscle gain',
                'description' => null,
                'type' => 'goals',
            ],
            [
                'title' => 'Maintain',
                'description' => null,
                'type' => 'goals',
            ],
            [
                'title' => 'Eat Healthier',
                'description' => null,
                'type' => 'goals',
            ],
            [
                'title' => 'Save Time',
                'description' => null,
                'type' => 'goals',
            ],
            [
                'title' => 'Learn Cooking',
                'description' => null,
                'type' => 'goals',
            ],

            // Food Allergies
            [
                'title' => 'No Allergies',
                'description' => null,
                'type' => 'food_allergies',
            ],
            [
                'title' => 'Dairy',
                'description' => null,
                'type' => 'food_allergies',
            ],
            [
                'title' => 'Nuts',
                'description' => null,
                'type' => 'food_allergies',
            ],
            [
                'title' => 'Gluten',
                'description' => null,
                'type' => 'food_allergies',
            ],
            [
                'title' => 'Shellfish',
                'description' => null,
                'type' => 'food_allergies',
            ],
            [
                'title' => 'Soy',
                'description' => null,
                'type' => 'food_allergies',
            ],
        ];

        foreach ($preferences as $preference) {

            Preference::updateOrCreate(
                [
                    'title' => $preference['title'],
                    'type' => $preference['type'],
                ],
                [
                    'uuid' => Str::uuid(),
                    'description' => $preference['description'],
                    'status' => 'Active',
                ]
            );
        }
    }
}