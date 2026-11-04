<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\HandlesTrackingInput;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProgressPhotoResource;
use App\Support\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Body composition photos (front / side / back) per record date.
 */
class ProgressPhotoController extends Controller
{
    use HandlesTrackingInput;

    protected const VIEWS = ['front_photo', 'side_photo', 'back_photo'];

    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function index(Request $request)
    {
        $photos = ProgressPhoto()->where('user_uuid', auth()->user()->uuid)
            ->latest('record_date')
            ->paginate((int) $request->input('per_page', 10));

        return jsonResponse(true, __('api.progress_photos_fetched'), paginatedResponse($photos, ProgressPhotoResource::class, 'photos'));
    }

    /**
     * Upload any of front_photo / side_photo / back_photo / extra_photos[] for record_date.
     * Re-uploading a view replaces it; remove_views[] (front_photo, ...) clears a view.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $this->normalizeBooleans($request, ['consent']);

        $image = 'nullable|file|mimes:' . config('tracking.photo_mimes') . '|max:' . config('tracking.photo_max_kb');
        $validator = Validator::make($request->all(), [
            'record_date'    => 'required|date|before_or_equal:' . Journey::today($user)->addDay()->toDateString(),
            'front_photo'    => $image,
            'side_photo'     => $image,
            'back_photo'     => $image,
            'extra_photos'   => 'nullable|array|max:' . config('tracking.max_photos_per_entry'),
            'extra_photos.*' => 'file|mimes:' . config('tracking.photo_mimes') . '|max:' . config('tracking.photo_max_kb'),
            'remove_views'   => 'nullable|array',
            'remove_views.*' => 'in:' . implode(',', self::VIEWS),
            'remove_photos'  => 'nullable|array',
            'consent'        => 'required|accepted',
        ], [
            'consent.required' => __('api.photo_consent_required'),
            'consent.accepted' => __('api.photo_consent_required'),
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $date = date('Y-m-d', strtotime($request->record_date));
        $entry = ProgressPhoto()->where('user_uuid', $user->uuid)->whereDate('record_date', $date)->first()
            ?? ProgressPhoto()->newInstance(['user_uuid' => $user->uuid, 'record_date' => $date]);

        foreach ((array) $request->input('remove_views', []) as $view) {
            deleteFiles($entry->{$view});
            $entry->{$view} = null;
        }
        foreach (self::VIEWS as $view) {
            if ($request->hasFile($view)) {
                $entry->{$view} = uploadFile($request->file($view), 'progress-photos', $entry->{$view});
            }
        }
        $entry->extra_photos = $this->syncPhotos($request, $entry->extra_photos, 'progress-photos', 'extra_photos');
        $entry->consent = true;

        if (!$entry->front_photo && !$entry->side_photo && !$entry->back_photo && !$entry->extra_photos) {
            return jsonResponse(false, __('api.progress_photo_required'));
        }
        $entry->save();

        activity_log(log: 'API Progress Photos', event: 'progress_photos_saved', subject: $entry);

        return jsonResponse(true, __('api.progress_photos_saved'), new ProgressPhotoResource($entry));
    }
}
