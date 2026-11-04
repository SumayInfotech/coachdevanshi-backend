<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PathwayResource;
use App\Support\FormSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PathwayController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Pathway list grouped as in the "Select Your Pathway" screen.
     */
    public function index()
    {
        $pathways = Pathway()->active()->orderBy('sort_order')->get();

        $groups = [];
        foreach (['core' => 'Core Coaching Tracks', 'hormonal' => 'Hormonal & Life-Stage Journeys', 'general' => 'General'] as $key => $title) {
            $groups[] = [
                'group'    => $key,
                'title'    => $title,
                'pathways' => PathwayResource::collection($pathways->where('group', $key)->values()),
            ];
        }

        return jsonResponse(true, __('api.pathways_fetched'), [
            'selected_pathway_uuid' => auth()->user()->pathway_uuid,
            'groups'                => $groups,
        ]);
    }

    public function select(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pathway_uuid' => 'required|string',
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $pathway = Pathway()->active()->where('uuid', $request->pathway_uuid)->first();
        if (!$pathway) {
            return jsonResponse(false, __('api.pathway_not_found'), null, 404);
        }

        $user = auth()->user();
        $oldPathway = $user->pathway_uuid;
        $user->update(['pathway_uuid' => $pathway->uuid]);

        activity_log(
            log: 'API Pathway',
            event: 'pathway_selected',
            subject: $user,
            properties: ['old' => ['pathway_uuid' => $oldPathway], 'new' => ['pathway_uuid' => $pathway->uuid, 'title' => $pathway->title]],
        );

        return jsonResponse(true, __('api.pathway_selected'), new PathwayResource($pathway));
    }

    /**
     * Protocol questionnaire for a pathway + saved answers.
     */
    public function protocol($uuid)
    {
        $pathway = Pathway()->active()->where('uuid', $uuid)->first();
        if (!$pathway) {
            return jsonResponse(false, __('api.pathway_not_found'), null, 404);
        }

        $definition = $pathway->protocol_key ? config('protocols.' . $pathway->protocol_key) : null;
        if (!$definition) {
            return jsonResponse(false, __('api.protocol_not_available'));
        }

        $saved = UserPathwayProtocol()->where('user_uuid', auth()->user()->uuid)->where('pathway_uuid', $pathway->uuid)->first();

        return jsonResponse(true, __('api.protocol_fetched'), [
            'pathway'      => new PathwayResource($pathway),
            'title'        => $definition['title'],
            'status'       => $saved?->status ?? 'pending',
            'submitted_at' => $saved?->submitted_at ? dateFormat($saved->submitted_at) : null,
            'fields'       => FormSchema::describe($definition['fields']),
            'answers'      => $saved?->answers ?? (object) [],
        ]);
    }

    public function saveProtocol(Request $request, $uuid)
    {
        $pathway = Pathway()->active()->where('uuid', $uuid)->first();
        if (!$pathway) {
            return jsonResponse(false, __('api.pathway_not_found'), null, 404);
        }

        $definition = $pathway->protocol_key ? config('protocols.' . $pathway->protocol_key) : null;
        if (!$definition) {
            return jsonResponse(false, __('api.protocol_not_available'));
        }

        $user = auth()->user();
        $draft = filter_var($request->input('save_as_draft', false), FILTER_VALIDATE_BOOLEAN);
        $input = is_array($request->input('answers')) ? $request->input('answers') : $request->except(['lang', 'save_as_draft']);

        [$answers, $error] = FormSchema::validate($definition['fields'], $input, $draft);
        if ($error) {
            return jsonResponse(false, $error);
        }

        $record = UserPathwayProtocol()->firstOrNew(['user_uuid' => $user->uuid, 'pathway_uuid' => $pathway->uuid]);
        $record->answers = $answers;
        if (!$draft) {
            $record->status = 'submitted';
            $record->submitted_at = now();
        } elseif (!$record->exists) {
            $record->status = 'in_progress';
        }
        $record->save();

        if (!$user->pathway_uuid) {
            $user->update(['pathway_uuid' => $pathway->uuid]);
        }

        activity_log(
            log: 'API Pathway',
            event: $draft ? 'protocol_draft_saved' : 'protocol_submitted',
            subject: $user,
            properties: ['pathway' => $pathway->title],
        );

        return jsonResponse(true, $draft ? __('api.protocol_draft_saved') : __('api.protocol_saved'), [
            'pathway'      => new PathwayResource($pathway),
            'status'       => $record->status,
            'submitted_at' => $record->submitted_at ? dateFormat($record->submitted_at) : null,
        ]);
    }
}
