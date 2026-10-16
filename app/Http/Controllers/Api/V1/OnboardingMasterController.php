<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OnboardingMasterResource;
use App\Models\OnboardingMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OnboardingMasterController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Get all active onboarding masters.
     * Supports optional ?category= and ?chapter= filters, with optional ?per_page pagination.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chapter' => 'nullable|integer|between:1,6',

        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        if ($request->filled('chapter')) {
            $categories = onboarding_chapter_categories((int) $request->chapter);
        } else {
            $categories = array_merge(...array_values(onboarding_chapter_categories()));            
        }

        $masters = OnboardingMaster::active()
            ->whereIn('category', $categories)
            ->orderBy('sort_order', 'asc')
            ->get();


        $grouped = [];
        foreach ($categories as $slug) {
            $grouped[$slug] = OnboardingMasterResource::collection(
                $masters->where('category', $slug)->values()
            )->resolve();
        }

        return jsonResponse(true, 'Onboarding masters fetched successfully.', $grouped);

        // activity_log(
        //     log: 'API OnboardingMaster',
        //     event: 'onboarding_master_list_viewed',
        //     subject: auth()->user(),
        // );

        //pagination response
        // if ($request->filled('per_page')) {
        //     $perPage = (int) $request->input('per_page', 10);
        //     $paginated = $query->paginate($perPage);

        //     return jsonResponse(
        //         true,
        //         'Onboarding masters fetched successfully.',
        //         paginatedResponse($paginated, OnboardingMasterResource::class, 'masters')
        //     );
        // }
    }
}
