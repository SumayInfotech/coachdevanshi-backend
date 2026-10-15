<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\MealPlanning;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $user = auth()->user();
        if (!$user) {
            return jsonResponse(false, __('api.unauthorized'), [], 401);
        }

        $getRecipes = MealPlanning::with('recipe.category')->where('user_uuid', $user->uuid)->whereDate('date', today()->toDateString())->get();

        $categories = Category()::pluck('name');

        $groupedRecipes = $getRecipes->groupBy(function ($meal) {

            $categoryName = $meal->recipe->category->name ?? 'Uncategorized';

            return $categoryMapping[$categoryName] ?? $categoryName;
        });

        $result = collect();

        foreach ($categories as $category) {

            $meals = $groupedRecipes->get($category, collect());

            $totalMinutes = $meals->sum(function ($meal) {
                return (int) ($meal->recipe->time_duration ?? 0);
            });

            $result[$category] = [
                'total_minutes' => $totalMinutes,
                'extra_minutes' => $totalMinutes > 60
                    ? $totalMinutes - 60
                    : 0,

                'recipes' => $meals->map(function ($meal) {
                    return [
                        'meal_planning_uuid' => $meal->uuid,
                        'recipe_uuid'        => $meal->recipe->uuid,
                        'title'              => $meal->recipe->title,
                        'description'        => $meal->recipe->description,
                        'thumbnail_url'      => getFileUrl($meal->recipe->thumbnail_url),
                        'time_duration'      => $meal->recipe->time_duration,
                        'calory'             => $meal->recipe->calory,
                        'status'             => $meal->status,
                    ];
                })->values(),
            ];
        }

        $suggestedRecipes = Recipe()
            ->with(['category'])
            ->active()
            ->published()
            ->whereDoesntHave('likes', function ($query) use ($user) {
                $query->where('user_uuid', $user->uuid);
            })
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => __('api.meal_plan_fetched'),
            'data' => [
                'meal_plan' => $result,

                'suggested_recipes' => $suggestedRecipes->map(function ($recipe) {
                    return [
                        'recipe_uuid'   => $recipe->uuid,
                        'title'         => $recipe->title,
                        'description'   => $recipe->description,
                        'thumbnail_url' => $recipe->thumbnail_url
                            ? getFileUrl($recipe->thumbnail_url)
                            : null,
                        'category'      => $recipe->category?->name,
                        'time_duration' => $recipe->time_duration,
                        'calory'        => $recipe->calory,
                    ];
                })->values(),
            ],
        ]);
    }
}
