<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OnboardingMaster;
use Illuminate\Http\Request;

class OnboardingMasterController extends Controller
{
    /**
     * Get all active onboarding masters.
     * Supports optional ?category=filter.
     */
    public function index(Request $request)
    {
        $query = OnboardingMaster::active()->orderBy('category')->orderBy('sort_order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('chapter')) {
            // echo "test";die;
            if($request->chapter == 1){
                $query->where('category','coaching_journey');
            }
            
        }

        $masters = $query->get()->map(function ($item) {
            return [
                'uuid'           => $item->uuid,
                'category'       => $item->category,
                'category_label' => $item->category_label,
                'title'          => $item->title,
                'description'    => $item->description,
                'image'          => $item->image ? getFileUrl($item->image) : null,
                'sort_order'     => $item->sort_order,
            ];
        });

        // Group masters by category for convenience in mobile screens
        $grouped = $masters->groupBy('category');

        return jsonResponse(true, 'Onboarding masters fetched successfully.', [
            'total'      => $masters->count(),
            'masters'    => $masters,
            'grouped'    => $grouped,
        ]);
    }
}
