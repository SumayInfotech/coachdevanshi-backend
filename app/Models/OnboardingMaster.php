<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class OnboardingMaster extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'onboarding_masters';

    protected $fillable = [
        'uuid',
        'category',
        'title',
        'description',
        'image',
        'sort_order',
        'status',
    ];

    /**
     * Predefined categories across all 6 onboarding chapters
     */
    public const CATEGORIES = [
        'coaching_journey'         => 'Coaching Journey (Chapter 01)',
        'goal_type'                => 'Primary Goal (Chapter 02)',

        'health_condition'         => 'Medical / Diagnosed Condition (Chapter 03)',
        'family_history'           => 'Family Medical History (Chapter 03)',
        'lab_work_marker'          => 'Recent Lab Work Markers (Chapter 03)',
        'allergy_tolerance_type'   => 'Allergies & Intolerance Level (Chapter 03)',
        'diet_type'                => 'Diet Type (Chapter 04)',
        'digestive_symptom'        => 'Digestive Symptoms (Chapter 04)',
        'bristol_stool_type'       => 'Bristol Stool Scale (Chapter 04)',
        'hydration_range'          => 'Daily Water Intake (Chapter 04)',
        'sleep_waking_frequency'   => 'Night Waking Frequency (Chapter 05)',
        'waking_state'             => 'Waking State (Chapter 05)',
        'activity_level'           => 'Daily Activity Level (Chapter 05)',
        'daily_step_range'         => 'Daily Step Range (Chapter 05)',
        'stress_symptom'           => 'Stress Physical Symptoms (Chapter 05)',
        'lifestyle_challenge'      => 'Challenges & Obstacles (Chapter 05)',
        'coaching_support_style'   => 'Coaching Support Style (Chapter 05)',
        'mindset_statement'        => 'Goal-Oriented Mindset Statement (Chapter 06)',
    ];


    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getCategoryLabelAttribute()
    {
        return self::CATEGORIES[$this->category] ?? ucwords(str_replace('_', ' ', $this->category));
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
