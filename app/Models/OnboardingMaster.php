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
     * Categories used by the onboarding chapters (config/onboarding.php), keyed by slug.
     * Label: "<question label> (Chapter 0X)"
     */
    public static function categories(): array
    {
        $categories = [];
        foreach (config('onboarding.chapters', []) as $number => $chapter) {
            foreach ($chapter['fields'] as $field) {
                if (!empty($field['master'])) {
                    $categories[$field['master']] = $field['label'] . ' (Chapter ' . str_pad($number, 2, '0', STR_PAD_LEFT) . ')';
                }
            }
        }

        return $categories;
    }


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
        return self::categories()[$this->category] ?? ucwords(str_replace('_', ' ', $this->category));
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
