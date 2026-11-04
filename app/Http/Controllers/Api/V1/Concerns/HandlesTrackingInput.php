<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Http\Request;

/**
 * Shared input handling for tracking endpoints (form-data friendly).
 */
trait HandlesTrackingInput
{
    /**
     * Cast "true"/"false"/"1"/"0" strings to booleans so the `boolean` rule accepts form-data values.
     */
    protected function normalizeBooleans(Request $request, array $keys): void
    {
        $values = [];
        foreach ($keys as $key) {
            if ($request->has($key) && !is_bool($request->input($key)) && !is_null($request->input($key))) {
                $bool = filter_var($request->input($key), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if (!is_null($bool)) {
                    $values[$key] = $bool;
                }
            }
        }
        if ($values) {
            $request->merge($values);
        }
    }

    /**
     * Decode JSON strings sent through form-data (e.g. meals={"breakfast":{...}}).
     */
    protected function decodeJsonInputs(Request $request, array $keys): void
    {
        $values = [];
        foreach ($keys as $key) {
            $value = $request->input($key);
            if (is_string($value) && $value !== '') {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    $values[$key] = $decoded;
                }
            }
        }
        if ($values) {
            $request->merge($values);
        }
    }

    protected function photoRules(string $key = 'photos'): array
    {
        return [
            $key          => 'nullable|array|max:' . config('tracking.max_photos_per_entry'),
            $key . '.*'   => 'file|mimes:' . config('tracking.photo_mimes') . '|max:' . config('tracking.photo_max_kb'),
            'remove_photos'   => 'nullable|array',
            'remove_photos.*' => 'string',
        ];
    }

    /**
     * Apply uploads + removals to a stored photo path list.
     */
    protected function syncPhotos(Request $request, ?array $existing, string $folder, string $key = 'photos'): array
    {
        $existing = $existing ?? [];

        $remove = (array) $request->input('remove_photos', []);
        if ($remove) {
            $keep = [];
            foreach ($existing as $path) {
                if (in_array($path, $remove, true) || in_array(getFileUrl($path), $remove, true)) {
                    deleteFiles($path);
                } else {
                    $keep[] = $path;
                }
            }
            $existing = $keep;
        }

        if ($request->hasFile($key)) {
            $existing = array_merge($existing, uploadFiles($request->file($key), $folder));
        }

        return array_values($existing);
    }

    /**
     * Only the keys present in the request (partial updates must not wipe existing values).
     */
    protected function presentInput(Request $request, array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            if ($request->exists($key)) {
                $data[$key] = $request->input($key);
            }
        }

        return $data;
    }
}
