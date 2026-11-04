<?php

namespace Database\Seeders;

use App\Models\Pathway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PathwaySeeder extends Seeder
{
    public function run(): void
    {
        $pathways = [
            [
                'slug'         => 'gut-reset',
                'title'        => 'Gut Reset',
                'group'        => 'core',
                'protocol_key' => 'gut_reset',
                'description'  => 'Microbiome rebalancing, gut motility, acid reflux relief & anti-inflammatory whole-food protocols designed to restore digestion from within.',
                'tags'         => ['8-12 Weeks', 'High Recommendation', 'Microbiome Tuning'],
            ],
            [
                'slug'         => 'fat-loss-inch-loss',
                'title'        => 'Fat Loss and Inch Loss',
                'group'        => 'core',
                'protocol_key' => 'fat_loss',
                'description'  => 'Sustainable metabolic tuning, inch-loss cadence & mindful body recomp with zero restriction or punitive calorie deficits.',
                'tags'         => ['12-16 Weeks', 'Metabolic Sync'],
            ],
            [
                'slug'         => 'healthy-weight-gain',
                'title'        => 'Healthy Weight Gain',
                'group'        => 'core',
                'protocol_key' => 'weight_gain',
                'description'  => 'Nourishing caloric density, gut absorption optimization & lean muscle recovery crafted for delicate appetites.',
                'tags'         => ['12 Weeks', 'Vitality'],
            ],
            [
                'slug'         => 'rooted-recovery-new-moms',
                'title'        => 'Rooted Recovery for New Moms',
                'group'        => 'core',
                'protocol_key' => 'new_moms',
                'description'  => 'Postpartum restorative nourishment, tissue healing, warm digestive broths & gentle hormonal rebalancing.',
                'tags'         => ['Specialized', 'Postpartum Safe'],
            ],
            [
                'slug'         => 'maintenance-lifestyle',
                'title'        => 'Maintenance & Lifestyle',
                'group'        => 'core',
                'protocol_key' => 'maintenance',
                'description'  => 'Long-term intuitive rhythm, dining out flexibility, metabolic resilience & joyful habit sustaining.',
                'tags'         => ['Ongoing', 'Lifestyle Integration'],
            ],
            [
                'slug'         => 'reproductive-life-stage',
                'title'        => 'Reproductive Life-Stage Modules',
                'group'        => 'hormonal',
                'protocol_key' => 'reproductive',
                'description'  => 'Specialized clinical evaluation calibrated to your hormonal journey, menstrual rhythm, preconception, pregnancy, postpartum recovery, or menopausal transition.',
                'tags'         => ['Cycle Syncing', 'Maternal Care', 'Perimenopause', 'Post-Op Recovery'],
            ],
            [
                'slug'         => 'not-applicable',
                'title'        => 'Not applicable or Prefer Not to Answer',
                'group'        => 'general',
                'protocol_key' => null,
                'description'  => 'Bypass hormonal staging; your clinical team will personalize foundational metabolic guidance.',
                'tags'         => ['General Foundation', 'Flexible Focus'],
            ],
        ];

        foreach ($pathways as $index => $pathway) {
            // DatabaseSeeder runs WithoutModelEvents, so HasUuid does not fire: set the uuid here
            $model = Pathway::firstOrNew(['slug' => $pathway['slug']]);
            $model->uuid = $model->uuid ?: (string) Str::uuid();
            $model->fill($pathway + ['sort_order' => $index + 1]);
            $model->status = $model->exists ? $model->status : 'Active';
            $model->save();
        }
    }
}
