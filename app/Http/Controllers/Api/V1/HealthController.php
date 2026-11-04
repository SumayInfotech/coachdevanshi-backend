<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Apple Health / Google Fit: the app reads the data on device and pushes daily aggregates here.
 */
class HealthController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    public function connect(Request $request)
    {
        // JSON booleans fail the `in:` rule (false is not "false"), so map them to "1" / "0" first
        if (is_bool($request->input('connected'))) {
            $request->merge(['connected' => $request->input('connected') ? '1' : '0']);
        }

        $validator = Validator::make($request->all(), [
            'source'    => 'required_unless:connected,false,0|nullable|in:apple_health,google_fit',
            'connected' => 'required|in:true,false,1,0',
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        $connected = filter_var($request->connected, FILTER_VALIDATE_BOOLEAN);
        $user->update([
            'is_health_connected' => $connected,
            'health_source'       => $connected ? $request->source : null,
        ]);

        activity_log(log: 'API Health', event: $connected ? 'health_connected' : 'health_disconnected', subject: $user, properties: ['source' => $request->source]);

        return jsonResponse(true, __('api.health_connection_updated'), [
            'is_health_connected' => (bool) $user->is_health_connected,
            'health_source'       => $user->health_source,
        ]);
    }

    /**
     * records: [{date: Y-m-d, steps, distance_km?, calories?, active_minutes?}, ...] (max 90 per call)
     */
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source'                   => 'nullable|in:apple_health,google_fit',
            'records'                  => 'required|array|min:1|max:90',
            'records.*.date'           => 'required|date_format:Y-m-d',
            'records.*.steps'          => 'required|integer|between:0,200000',
            'records.*.distance_km'    => 'nullable|numeric|between:0,500',
            'records.*.calories'       => 'nullable|integer|between:0,50000',
            'records.*.active_minutes' => 'nullable|integer|between:0,1440',
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();
        $source = $request->source ?? $user->health_source;

        foreach ($request->records as $record) {
            ActivitySync()->updateOrCreate(
                ['user_uuid' => $user->uuid, 'activity_date' => $record['date']],
                [
                    'steps'          => $record['steps'],
                    'distance_km'    => $record['distance_km'] ?? null,
                    'calories'       => $record['calories'] ?? null,
                    'active_minutes' => $record['active_minutes'] ?? null,
                    'source'         => $source,
                    'synced_at'      => now(),
                ]
            );
        }

        if (!$user->is_health_connected && $source) {
            $user->update(['is_health_connected' => true, 'health_source' => $source]);
        }

        return jsonResponse(true, __('api.health_synced'), ['synced' => count($request->records)]);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'nullable|date_format:Y-m-d',
            'to'   => 'nullable|date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $records = ActivitySync()->where('user_uuid', auth()->user()->uuid)
            ->when($request->from, fn ($q) => $q->whereDate('activity_date', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('activity_date', '<=', $request->to))
            ->orderByDesc('activity_date')
            ->limit(366)
            ->get();

        return jsonResponse(true, __('api.health_steps_fetched'), $records->map(fn ($row) => [
            'date'           => $row->activity_date->toDateString(),
            'steps'          => $row->steps,
            'distance_km'    => $row->distance_km,
            'calories'       => $row->calories,
            'active_minutes' => $row->active_minutes,
            'source'         => $row->source,
            'synced_at'      => $row->synced_at ? dateFormat($row->synced_at) : null,
        ]));
    }
}
