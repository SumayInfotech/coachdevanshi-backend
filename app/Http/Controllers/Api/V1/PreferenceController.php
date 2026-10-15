<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PreferenceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PreferenceController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if(!$user){
            return jsonResponse(false,__('api.unauthorized'),[],401);
        }
        $query = Preference()->active();
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $perPage = $request->input('per_page', 10);
        $preferences = $query->latest()->paginate($perPage);

        activity_log(
            log: 'API Preference',
            event: 'preference_list_viewed',
            subject: $user,
        );

        // return jsonResponse(true,__('api.preferences_fetched'),paginatedResponse($preferences, PreferenceResource::class),);
        return jsonResponse(true,__('api.preferences_fetched'),paginatedResponse($preferences,PreferenceResource::class,'preferences'));
    }
    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        if(!$user){
            return jsonResponse(false,__('api.unauthorized'),[],401);
        }
        $preferenceUuids = collect(explode(',', $request->uuid))->map(fn($uuid) => trim($uuid))->filter()->unique()->values();

        // Check valid active preferences
        $validPreferenceUuids = Preference()->active()->whereIn('uuid', $preferenceUuids)->pluck('uuid')->toArray();

        // Invalid UUID check
        if (count($preferenceUuids) !== count($validPreferenceUuids)) {
            return jsonResponse(false,__('api.invalid_preference'));
        }        

        // Remove old preferences
        UserPreference()->where('user_uuid',$user->uuid)->delete();

        // Insert new preferences
        foreach ($preferenceUuids as $preferenceUuid) {
            UserPreference()->create([
                'user_uuid' => $user->uuid,
                'preference_uuid' => $preferenceUuid,
            ]);
        }
        if (!$user->is_profile_completed) {
            $user->is_profile_completed = true;
            $user->save();
        }
        activity_log(log: 'API Preference',event: 'preferences_updated',subject: $user,);
        return jsonResponse(true,__('api.preferences_updated'));
    }
}