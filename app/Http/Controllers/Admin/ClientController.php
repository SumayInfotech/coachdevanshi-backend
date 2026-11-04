<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ClientDataTable;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\CoachNote;
use App\Models\PlanRenewalRequest;
use App\Models\User;
use App\Models\UserPlan;
use App\Support\FormSchema;
use App\Support\Journey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Coach view of app clients: onboarding answers, pathway protocol, trackers, plan, notes and chat.
 */
class ClientController extends Controller
{
    protected $viewPrefix  = 'admin.clients';
    protected $routePrefix = 'admin.clients';

    private $viewPermission;
    private $editPermission;
    private $chatPermission;

    public function __construct()
    {
        $this->viewPermission = hasPermission('view_client');
        $this->editPermission = hasPermission('edit_client');
        $this->chatPermission = hasPermission('chat_client');

        view()->share([
            'viewPrefix'     => $this->viewPrefix,
            'routePrefix'    => $this->routePrefix,
            'editPermission' => $this->editPermission,
            'chatPermission' => $this->chatPermission,
        ]);
    }

    public function index(ClientDataTable $dataTable)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }

        return $dataTable->render($this->viewPrefix . '.index', [
            'pathways' => Pathway()->orderBy('sort_order')->pluck('title', 'uuid'),
        ]);
    }

    public function show($uuid)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }

        $client = User::customer()->with(['pathway', 'consent'])->where('uuid', $uuid)->firstOrFail();

        $saved = UserOnboardingChapter()->where('user_uuid', $client->uuid)->get()->keyBy('chapter');
        $chapters = [];
        foreach (config('onboarding.chapters') as $number => $definition) {
            $record = $saved[$number] ?? null;
            $chapters[] = [
                'number'       => $number,
                'title'        => $definition['title'],
                'status'       => $record?->status ?? 'pending',
                'completed_at' => $record?->completed_at,
                'rows'         => $record ? FormSchema::present($definition['fields'], $record->answers) : [],
            ];
        }

        $protocols = UserPathwayProtocol()->with('pathway')->where('user_uuid', $client->uuid)->latest('updated_at')->get()
            ->map(function ($protocol) {
                $definition = $protocol->pathway?->protocol_key ? config('protocols.' . $protocol->pathway->protocol_key) : null;

                return [
                    'pathway'      => $protocol->pathway?->title ?? 'Removed pathway',
                    'title'        => $definition['title'] ?? 'Protocol',
                    'status'       => $protocol->status,
                    'submitted_at' => $protocol->submitted_at,
                    'rows'         => $definition ? FormSchema::present($definition['fields'], $protocol->answers) : [],
                ];
            });

        activity_log(log: 'Client', event: 'viewed', subject: $client);

        return view($this->viewPrefix . '.show', [
            'client'        => $client,
            'chapters'      => $chapters,
            'protocols'     => $protocols,
            'plans'         => UserPlan::with('pathway')->where('user_uuid', $client->uuid)->latest('end_date')->get(),
            'renewals'      => PlanRenewalRequest::where('user_uuid', $client->uuid)->latest()->get(),
            'notes'         => CoachNote::with('coach')->where('user_uuid', $client->uuid)->latest()->get(),
            'dailyCheckins' => DailyCheckin()->where('user_uuid', $client->uuid)->latest('record_date')->limit(60)->get(),
            'weeklyCheckins'=> WeeklyCheckin()->where('user_uuid', $client->uuid)->latest('record_date')->limit(52)->get(),
            'photos'        => ProgressPhoto()->where('user_uuid', $client->uuid)->latest('record_date')->get(),
            'foodDiary'     => FoodDiaryEntry()->where('user_uuid', $client->uuid)->orderBy('day_number')->get(),
            'bloodReports'  => BloodReport()->with('files')->where('user_uuid', $client->uuid)->latest('report_date')->get(),
            'dailyNotes'    => DailyNote()->where('user_uuid', $client->uuid)->latest('note_date')->limit(30)->get(),
            'activity'      => ActivitySync()->where('user_uuid', $client->uuid)->latest('activity_date')->limit(14)->get(),
            'weekly'        => Journey::weeklyStatus($client),
            'baseline'      => Journey::baselineStatus($client),
            'weight'        => Journey::weightOverview($client),
            'pathways'      => Pathway()->active()->orderBy('sort_order')->get(),
            'unreadChat'    => ChatMessage::unread()->where('user_uuid', $client->uuid)->where('sender_type', 'client')->count(),
        ]);
    }

    /**
     * Assign a plan (new or renewal). Pending renewal requests are closed as Renewed.
     */
    public function storePlan(Request $request, $uuid)
    {
        $this->authorizeEdit();
        $client = User::customer()->where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'pathway_uuid'    => ['nullable', Rule::exists('pathways', 'uuid')],
            'duration_months' => ['required_without:end_date', 'nullable', 'integer', 'between:1,24'],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['nullable', 'date', 'after:start_date'],
            'notes'           => ['nullable', 'string', 'max:2000'],
        ]);

        $start = Carbon::parse($request->start_date);
        $end = $request->end_date ? Carbon::parse($request->end_date) : $start->copy()->addMonths((int) $request->duration_months)->subDay();

        $plan = UserPlan::create([
            'user_uuid'       => $client->uuid,
            'pathway_uuid'    => $request->pathway_uuid ?: $client->pathway_uuid,
            'title'           => $request->title,
            'duration_months' => $request->duration_months ?: null,
            'start_date'      => $start->toDateString(),
            'end_date'        => $end->toDateString(),
            'status'          => 'Active',
            'notes'           => $request->notes,
            'created_by_uuid' => auth()->user()->uuid,
        ]);

        PlanRenewalRequest::where('user_uuid', $client->uuid)->whereIn('status', ['Pending', 'Contacted'])->update(['status' => 'Renewed']);

        $lang = $client->language ?: 'en';
        notifyUser($client, 'plan_assigned',
            __('api.notification_plan_assigned_title', [], $lang),
            __('api.notification_plan_assigned_body', ['plan' => $plan->title, 'date' => $end->format('d M Y')], $lang),
            ['action' => 'plan', 'data' => ['plan_uuid' => $plan->uuid]]
        );

        activity_log(log: 'Client', event: 'plan_assigned', subject: $plan, properties: ['client' => $client->uuid, 'end_date' => $plan->end_date->toDateString()]);

        return redirect()->to(route($this->routePrefix . '.show', $client->uuid) . '#tab-plan')->with('success', 'Plan assigned successfully.');
    }

    public function updatePlan(Request $request, $planUuid)
    {
        $this->authorizeEdit();
        $plan = UserPlan::where('uuid', $planUuid)->firstOrFail();

        $request->validate([
            'end_date' => ['required', 'date', 'after_or_equal:' . $plan->start_date->toDateString()],
            'status'   => ['required', 'in:Active,Expired,Cancelled'],
        ]);

        $old = ['end_date' => $plan->end_date->toDateString(), 'status' => $plan->status];
        $plan->update(['end_date' => $request->end_date, 'status' => $request->status]);

        activity_log(log: 'Client', event: 'plan_updated', subject: $plan, properties: ['old' => $old, 'new' => $request->only('end_date', 'status')]);

        return redirect()->to(route($this->routePrefix . '.show', $plan->user_uuid) . '#tab-plan')->with('success', 'Plan updated successfully.');
    }

    public function updateRenewal(Request $request, $renewalUuid)
    {
        $this->authorizeEdit();
        $renewal = PlanRenewalRequest::where('uuid', $renewalUuid)->firstOrFail();

        $request->validate([
            'status'     => ['required', 'in:Pending,Contacted,Renewed,Closed'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $renewal->update($request->only('status', 'admin_note'));

        activity_log(log: 'Client', event: 'renewal_updated', subject: $renewal, properties: $request->only('status'));

        return redirect()->to(route($this->routePrefix . '.show', $renewal->user_uuid) . '#tab-plan')->with('success', 'Renewal request updated.');
    }

    /**
     * Coach note shown on the client's home screen.
     */
    public function storeNote(Request $request, $uuid)
    {
        $this->authorizeEdit();
        $client = User::customer()->where('uuid', $uuid)->firstOrFail();

        $request->validate(['note' => ['required', 'string', 'max:3000']]);

        $note = CoachNote::create([
            'user_uuid'  => $client->uuid,
            'coach_uuid' => auth()->user()->uuid,
            'note'       => $request->note,
        ]);

        notifyUser($client, 'coach_note',
            __('api.notification_coach_note_title', [], $client->language ?: 'en'),
            \Illuminate\Support\Str::limit($note->note, 120),
            ['action' => 'home', 'data' => ['note_uuid' => $note->uuid]]
        );

        activity_log(log: 'Client', event: 'coach_note_added', subject: $note, properties: ['client' => $client->uuid]);

        return redirect()->to(route($this->routePrefix . '.show', $client->uuid) . '#tab-plan')->with('success', 'Note added successfully.');
    }

    public function destroyNote($noteUuid)
    {
        if (!$this->editPermission) {
            return response()->json(['success' => false, 'message' => 'Permission denied.']);
        }

        $note = CoachNote::where('uuid', $noteUuid)->first();
        if (!$note) {
            return response()->json(['success' => false, 'message' => 'Note not found.']);
        }

        activity_log(log: 'Client', event: 'coach_note_deleted', subject: $note);
        $note->delete();

        return response()->json(['success' => true, 'message' => 'Note deleted successfully.']);
    }

    /**
     * Chat thread as JSON (oldest first). ?after_id= returns only newer messages. Marks client messages as read.
     */
    public function chatMessages(Request $request, $uuid)
    {
        if (!$this->chatPermission) {
            return response()->json(['success' => false, 'message' => 'Permission denied.']);
        }
        $client = User::customer()->where('uuid', $uuid)->firstOrFail();

        $messages = ChatMessage::with('sender')->where('user_uuid', $client->uuid)
            ->when($request->filled('after_id'), fn ($q) => $q->where('id', '>', (int) $request->after_id))
            ->orderByDesc('id')->limit(200)->get()->reverse()->values();

        ChatMessage::unread()->where('user_uuid', $client->uuid)->where('sender_type', 'client')->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'data'    => $messages->map(fn ($message) => [
                'id'          => $message->id,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender_type === 'client' ? $client->name : ($message->sender?->name ?? 'System'),
                'message'     => e($message->message),
                'attachments' => fileUrls($message->attachments),
                'created_at'  => $message->created_at->format('d M Y, h:i A'),
            ]),
        ]);
    }

    public function sendChat(Request $request, $uuid)
    {
        if (!$this->chatPermission) {
            return response()->json(['success' => false, 'message' => 'Permission denied.']);
        }
        $client = User::customer()->where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'message'       => ['nullable', 'string', 'max:5000'],
            'attachments'   => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'mimes:' . config('tracking.photo_mimes'), 'max:' . config('tracking.photo_max_kb')],
        ]);
        if (!$request->filled('message') && !$request->hasFile('attachments')) {
            return response()->json(['success' => false, 'message' => 'Please enter a message or attach a photo.']);
        }

        $message = ChatMessage::create([
            'user_uuid'   => $client->uuid,
            'sender_type' => 'coach',
            'sender_uuid' => auth()->user()->uuid,
            'message'     => $request->message,
            'attachments' => $request->hasFile('attachments') ? uploadFiles($request->file('attachments'), 'chat') : null,
        ]);

        notifyUser($client, 'chat_message',
            __('api.notification_coach_message_title', [], $client->language ?: 'en'),
            $message->message ? \Illuminate\Support\Str::limit($message->message, 120) : 'Photo',
            ['action' => 'chat', 'store' => false, 'data' => ['message_uuid' => $message->uuid]]
        );

        return response()->json(['success' => true, 'message' => 'Message sent.']);
    }

    protected function authorizeEdit(): void
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }
    }
}
