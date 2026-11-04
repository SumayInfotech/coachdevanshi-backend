<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PathwayResource;
use App\Support\FormSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends Controller
{
    public function __construct(Request $request)
    {
        setApiLocale($request->lang);
    }

    /**
     * Onboarding journey overview: chapter statuses, consent and selected pathway.
     */
    public function status()
    {
        $user = auth()->user();

        return jsonResponse(true, __('api.onboarding_status_fetched'), $this->statusPayload($user));
    }

    /**
     * Chapter form definition + the user's saved answers (chapter 1 is prefilled from the profile).
     */
    public function chapter(Request $request, $chapter)
    {
        $definition = config('onboarding.chapters.' . (int) $chapter);
        if (!$definition) {
            return jsonResponse(false, __('api.invalid_chapter'), null, 404);
        }

        $user = auth()->user();
        $saved = UserOnboardingChapter()->where('user_uuid', $user->uuid)->where('chapter', (int) $chapter)->first();

        $answers = $saved?->answers;
        if (!$answers && (int) $chapter === 1) {
            $answers = $this->profilePrefill($user);
        }

        return jsonResponse(true, __('api.onboarding_chapter_fetched'), [
            'chapter'      => (int) $chapter,
            'title'        => $definition['title'],
            'subtitle'     => $definition['subtitle'] ?? null,
            'total'        => count(config('onboarding.chapters')),
            'status'       => $saved?->status ?? 'pending',
            'completed_at' => $saved?->completed_at ? dateFormat($saved->completed_at) : null,
            'fields'       => FormSchema::describe($definition['fields']),
            'answers'      => $answers ?? (object) [],
        ]);
    }

    /**
     * Save a chapter. Send answers as a JSON object in "answers" (or as top-level keys).
     * save_as_draft=true skips required checks and keeps the chapter "in_progress".
     */
    public function saveChapter(Request $request, $chapter)
    {
        $chapter = (int) $chapter;
        $definition = config('onboarding.chapters.' . $chapter);
        if (!$definition) {
            return jsonResponse(false, __('api.invalid_chapter'), null, 404);
        }

        $user = auth()->user();
        $draft = filter_var($request->input('save_as_draft', false), FILTER_VALIDATE_BOOLEAN);
        $input = is_array($request->input('answers')) ? $request->input('answers') : $request->except(['lang', 'save_as_draft']);

        [$answers, $error] = FormSchema::validate($definition['fields'], $input, $draft);
        if ($error) {
            return jsonResponse(false, $error);
        }

        $record = UserOnboardingChapter()->firstOrNew(['user_uuid' => $user->uuid, 'chapter' => $chapter]);
        $record->answers = $answers;
        $record->status = $draft ? 'in_progress' : 'completed';
        $record->completed_at = $draft ? null : now();
        $record->save();

        if ($chapter === 1) {
            $this->syncProfile($user, $answers);
        }

        activity_log(
            log: 'API Onboarding',
            event: $draft ? 'onboarding_chapter_draft_saved' : 'onboarding_chapter_completed',
            subject: $user,
            properties: ['chapter' => $chapter],
        );

        $payload = $this->statusPayload($user);
        $payload['saved_chapter'] = $chapter;
        $payload['next_chapter'] = $this->nextPendingChapter($user);

        return jsonResponse(true, $draft ? __('api.onboarding_chapter_draft_saved') : __('api.onboarding_chapter_saved'), $payload);
    }

    /**
     * Review & submit: every chapter with raw answers and a readable summary.
     */
    public function answers()
    {
        $user = auth()->user();
        $saved = UserOnboardingChapter()->where('user_uuid', $user->uuid)->get()->keyBy('chapter');

        $chapters = [];
        foreach (config('onboarding.chapters') as $number => $definition) {
            $record = $saved[$number] ?? null;
            $chapters[] = [
                'chapter' => $number,
                'title'   => $definition['title'],
                'status'  => $record?->status ?? 'pending',
                'answers' => $record?->answers ?? (object) [],
                'summary' => FormSchema::present($definition['fields'], $record?->answers),
            ];
        }

        return jsonResponse(true, __('api.onboarding_answers_fetched'), ['chapters' => $chapters]);
    }

    /**
     * Final consent & agreement. Completes onboarding when all chapters are done.
     */
    public function consent(Request $request)
    {
        $consents = config('onboarding.consents');

        $rules = [];
        foreach ($consents as $key => $consent) {
            $rules[$key] = $consent['required'] ? 'required|accepted' : 'nullable|boolean';
        }
        $validator = Validator::make($request->all(), $rules, [
            'accepted' => __('api.consent_required'),
            'required' => __('api.consent_required'),
        ]);
        if ($validator->fails()) {
            return jsonResponse(false, $validator->errors()->first());
        }

        $user = auth()->user();

        $completed = UserOnboardingChapter()->where('user_uuid', $user->uuid)->where('status', 'completed')->count();
        if ($completed < count(config('onboarding.chapters'))) {
            return jsonResponse(false, __('api.onboarding_incomplete'), [
                'next_chapter' => $this->nextPendingChapter($user),
            ]);
        }

        $data = ['ip_address' => $request->ip(), 'accepted_at' => now()];
        foreach (array_keys($consents) as $key) {
            $data[$key] = filter_var($request->input($key, false), FILTER_VALIDATE_BOOLEAN);
        }
        UserConsent()->updateOrCreate(['user_uuid' => $user->uuid], $data);

        $user->update([
            'is_onboarding_completed' => true,
            'onboarding_completed_at' => $user->onboarding_completed_at ?? now(),
            'is_profile_completed'    => true,
        ]);

        activity_log(log: 'API Onboarding', event: 'onboarding_completed', subject: $user);

        return jsonResponse(true, __('api.onboarding_completed'), $this->statusPayload($user->fresh()));
    }

    protected function statusPayload($user): array
    {
        $saved = UserOnboardingChapter()->where('user_uuid', $user->uuid)->get()->keyBy('chapter');
        $consent = UserConsent()->where('user_uuid', $user->uuid)->first();

        $chapters = [];
        foreach (config('onboarding.chapters') as $number => $definition) {
            $record = $saved[$number] ?? null;
            $chapters[] = [
                'chapter'      => $number,
                'title'        => $definition['title'],
                'subtitle'     => $definition['subtitle'] ?? null,
                'status'       => $record?->status ?? 'pending',
                'completed_at' => $record?->completed_at ? dateFormat($record->completed_at) : null,
            ];
        }

        $consentItems = [];
        foreach (config('onboarding.consents') as $key => $item) {
            $consentItems[] = [
                'key'      => $key,
                'label'    => $item['label'],
                'required' => $item['required'],
                'accepted' => (bool) ($consent?->{$key} ?? false),
            ];
        }

        $pathway = $user->pathway_uuid ? Pathway()->where('uuid', $user->pathway_uuid)->first() : null;
        $protocol = $pathway
            ? UserPathwayProtocol()->where('user_uuid', $user->uuid)->where('pathway_uuid', $pathway->uuid)->first()
            : null;

        return [
            'is_onboarding_completed' => (bool) $user->is_onboarding_completed,
            'completed_chapters'      => $saved->where('status', 'completed')->count(),
            'total_chapters'          => count($chapters),
            'chapters'                => $chapters,
            'consent'                 => [
                'is_accepted' => (bool) $consent?->accepted_at,
                'accepted_at' => $consent?->accepted_at ? dateFormat($consent->accepted_at) : null,
                'items'       => $consentItems,
            ],
            'pathway'                 => $pathway ? new PathwayResource($pathway) : null,
            'protocol_status'         => $pathway?->protocol_key ? ($protocol?->status ?? 'pending') : null,
        ];
    }

    protected function nextPendingChapter($user): ?int
    {
        $completed = UserOnboardingChapter()->where('user_uuid', $user->uuid)->where('status', 'completed')->pluck('chapter')->all();
        foreach (array_keys(config('onboarding.chapters')) as $number) {
            if (!in_array($number, $completed)) {
                return $number;
            }
        }

        return null;
    }

    protected function profilePrefill($user): array
    {
        return array_filter([
            'email'      => $user->email,
            'full_name'  => trim($user->name . ' ' . $user->last_name) ?: null,
            'nickname'   => $user->nickname,
            'phone_code' => $user->phone_code,
            'mobile'     => $user->mobile,
            'dob'        => $user->dob?->format('Y-m-d'),
            'gender'     => $user->gender,
            'city'       => $user->city_name,
            'country'    => $user->country_name,
            'timezone'   => $user->timezone,
        ], fn ($value) => !is_null($value));
    }

    /**
     * Chapter 1 personal details are mirrored on the user row (login identifiers are left untouched).
     */
    protected function syncProfile($user, array $answers): void
    {
        $map = [
            'nickname' => 'nickname',
            'dob'      => 'dob',
            'gender'   => 'gender',
            'city'     => 'city_name',
            'country'  => 'country_name',
            'timezone' => 'timezone',
        ];

        $data = [];
        foreach ($map as $answerKey => $column) {
            if (array_key_exists($answerKey, $answers)) {
                $data[$column] = $answers[$answerKey];
            }
        }
        if (isset($data['timezone']) && !in_array($data['timezone'], timezone_identifiers_list())) {
            unset($data['timezone']);
        }
        if (!$user->email && !empty($answers['email'])) {
            $data['email'] = $answers['email'];
        }

        if ($data) {
            $user->update($data);
        }
    }
}
