@extends('admin.layouts.master')

@section('title', 'Client - ' . $client->name)

@php
    $activePlan = $plans->firstWhere('status', 'Active');
    $completedChapters = collect($chapters)->where('status', 'completed')->count();
    $statusBadge = fn ($status) => match ($status) {
        'completed', 'submitted', 'Active', 'Renewed' => 'bg-success',
        'in_progress', 'Contacted' => 'bg-warning',
        'Pending' => 'bg-danger',
        default => 'bg-secondary',
    };
@endphp

@section('content')
<div class="main-container container-fluid">

    <div class="page-header">
        <h1 class="page-title">{{ trim($client->name . ' ' . $client->last_name) }}</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item1 active"><a href="{{ route($routePrefix . '.index') }}">Clients</a></li>
                <li class="breadcrumb-item1" aria-current="page">View</li>
            </ol>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="row">
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $client->profile_image ? getFileUrl($client->profile_image) : asset('assets/sash/images/users/21.jpg') }}"
                             alt="Client photo" class="rounded-circle border" width="64" height="64" style="object-fit:cover;">
                        <div>
                            <h4 class="mb-1">{{ trim($client->name . ' ' . $client->last_name) }}</h4>
                            <div class="text-muted small">{{ $client->nickname ? 'Prefers "' . $client->nickname . '"' : '' }}</div>
                            <span class="badge {{ $client->status === 'Active' ? 'bg-success' : 'bg-danger' }}">{{ $client->status }}</span>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0"><span class="fw-semibold">Email</span><span>{{ $client->email ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span class="fw-semibold">Mobile</span><span>{{ trim($client->phone_code . ' ' . $client->mobile) ?: '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span class="fw-semibold">Date of birth</span><span>{{ $client->dob?->format('d M Y') ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span class="fw-semibold">Location</span><span>{{ collect([$client->city_name, $client->country_name])->filter()->implode(', ') ?: '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span class="fw-semibold">Time zone</span><span>{{ $client->timezone ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span class="fw-semibold">Weekly check-in day</span><span>{{ ucfirst($client->weekly_checkin_day) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span class="fw-semibold">Activity sync</span><span>{{ $client->is_health_connected ? formatName($client->health_source) : 'Not connected' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span class="fw-semibold">Joined</span><span>{{ $client->created_at->format('d M Y') }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-6">
            <div class="row">
                <div class="col-md-6 col-xl-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small text-uppercase">Plan</div>
                        @if($activePlan)
                            <h5 class="mb-1 mt-1">{{ $activePlan->title }}</h5>
                            <div class="{{ $activePlan->days_left <= 14 ? 'text-danger' : 'text-success' }}">{{ $activePlan->days_left }} days left</div>
                            <div class="progress progress-sm mt-2"><div class="progress-bar bg-primary" style="width: {{ $activePlan->progress_percent }}%"></div></div>
                            <div class="small text-muted mt-1">Ends {{ $activePlan->end_date->format('d M Y') }}</div>
                        @else
                            <h5 class="mb-0 mt-1 text-muted">No active plan</h5>
                        @endif
                    </div></div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small text-uppercase">Onboarding</div>
                        <h5 class="mb-1 mt-1">{{ $completedChapters }} / {{ count($chapters) }} chapters</h5>
                        <span class="badge {{ $client->is_onboarding_completed ? 'bg-success' : 'bg-warning' }}">
                            {{ $client->is_onboarding_completed ? 'Completed ' . $client->onboarding_completed_at?->format('d M Y') : 'In progress' }}
                        </span>
                        <div class="small text-muted mt-2">Pathway: {{ $client->pathway->title ?? 'Not selected' }}</div>
                    </div></div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small text-uppercase">Weight</div>
                        @if($weight)
                            <h5 class="mb-1 mt-1">{{ $weight['current'] }} {{ $weight['unit'] }}</h5>
                            <div class="small {{ ($weight['change'] ?? 0) <= 0 ? 'text-success' : 'text-danger' }}">
                                {{ is_null($weight['change']) ? 'First entry' : (($weight['change'] > 0 ? '+' : '') . $weight['change'] . ' ' . $weight['unit'] . ' since last entry') }}
                            </div>
                            <div class="small text-muted mt-1">Recorded {{ \Carbon\Carbon::parse($weight['recorded_on'])->format('d M Y') }}</div>
                        @else
                            <h5 class="mb-0 mt-1 text-muted">No entries yet</h5>
                        @endif
                    </div></div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small text-uppercase">7-day baseline</div>
                        <h5 class="mb-0 mt-1">Day {{ $baseline['completed'] }} of 7</h5>
                    </div></div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small text-uppercase">This week's check-in</div>
                        <h5 class="mb-0 mt-1">{{ ucfirst($weekly['status']) }}</h5>
                        <div class="small text-muted">Due {{ ucfirst($weekly['due_day']) }}, {{ \Carbon\Carbon::parse($weekly['due_date'])->format('d M') }}</div>
                    </div></div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small text-uppercase">Steps (last sync)</div>
                        @php $lastActivity = $activity->first(); @endphp
                        <h5 class="mb-0 mt-1">{{ $lastActivity ? number_format($lastActivity->steps) : '-' }}</h5>
                        <div class="small text-muted">{{ $lastActivity ? $lastActivity->activity_date->format('d M Y') : 'No synced data' }}</div>
                    </div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="card">
        <div class="card-header border-bottom p-0">
            <ul class="nav nav-tabs border-0 px-3 pt-2" role="tablist" id="clientTabs">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-plan" role="tab">Plan &amp; Notes</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-onboarding" role="tab">Onboarding</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-protocol" role="tab">Pathway Protocol</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-daily" role="tab">Daily Check-ins</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-weekly" role="tab">Weekly Check-ins</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-media" role="tab">Photos &amp; Reports</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-diary" role="tab">Food Diary &amp; Notes</a></li>
                @if($chatPermission)
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-chat" role="tab">Chat @if($unreadChat)<span class="badge bg-danger ms-1">{{ $unreadChat }}</span>@endif</a></li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">

                {{-- PLAN & NOTES --}}
                <div class="tab-pane fade show active" id="tab-plan" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-7">
                            <h5 class="mb-3">Plans</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="bg-light"><tr><th>Plan</th><th>Start</th><th>End</th><th>Days left</th><th>Status</th>@if($editPermission)<th></th>@endif</tr></thead>
                                    <tbody>
                                    @forelse($plans as $plan)
                                        <tr>
                                            <td>{{ $plan->title }}<div class="small text-muted">{{ $plan->pathway->title ?? '' }}{{ $plan->duration_months ? ' · ' . $plan->duration_months . 'M' : '' }}</div></td>
                                            <td>{{ $plan->start_date->format('d M Y') }}</td>
                                            <td>{{ $plan->end_date->format('d M Y') }}</td>
                                            <td>{{ $plan->status === 'Active' ? $plan->days_left : '-' }}</td>
                                            <td><span class="badge {{ $statusBadge($plan->status) }}">{{ $plan->status }}</span></td>
                                            @if($editPermission)
                                                <td>
                                                    <form method="POST" action="{{ route($routePrefix . '.plans.update', $plan->uuid) }}" class="d-flex gap-1">
                                                        @csrf @method('PUT')
                                                        <input type="date" name="end_date" value="{{ $plan->end_date->toDateString() }}" class="form-control form-control-sm" style="max-width:150px">
                                                        <select name="status" class="form-control form-control-sm" style="max-width:110px">
                                                            @foreach(['Active', 'Expired', 'Cancelled'] as $status)
                                                                <option value="{{ $status }}" {{ $plan->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button class="btn btn-sm btn-primary" title="Save"><i class="fe fe-check"></i></button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted">No plans assigned yet.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($editPermission)
                                <div class="card border mt-3">
                                    <div class="card-header bg-light"><h6 class="mb-0">Assign / renew plan</h6></div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route($routePrefix . '.plans.store', $client->uuid) }}" class="row g-3">
                                            @csrf
                                            <div class="col-md-6">
                                                <label class="form-label">Plan title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ old('title', $client->pathway->title ?? '') }}" placeholder="e.g. Fat Loss & Inch Loss" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Pathway</label>
                                                <select name="pathway_uuid" class="form-control">
                                                    <option value="">Client's selected pathway</option>
                                                    @foreach($pathways as $pathway)
                                                        <option value="{{ $pathway->uuid }}" {{ $client->pathway_uuid === $pathway->uuid ? 'selected' : '' }}>{{ $pathway->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Duration</label>
                                                <select name="duration_months" class="form-control">
                                                    <option value="">Custom end date</option>
                                                    @foreach([1, 3, 6, 12] as $months)
                                                        <option value="{{ $months }}" {{ $months === 3 ? 'selected' : '' }}>{{ $months }} month{{ $months > 1 ? 's' : '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Start date <span class="text-danger">*</span></label>
                                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', ($activePlan ? $activePlan->end_date->copy()->addDay() : now())->toDateString()) }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">End date</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                                                <small class="text-muted">Leave empty to use the duration.</small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Internal notes</label>
                                                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn btn-success"><i class="fa fa-check me-1"></i> Assign Plan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            <h5 class="mb-3 mt-4">Renewal requests</h5>
                            @forelse($renewals as $renewal)
                                <div class="border rounded p-3 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge {{ $statusBadge($renewal->status) }}">{{ $renewal->status }}</span>
                                            <span class="small text-muted ms-2">{{ $renewal->created_at->format('d M Y, h:i A') }}</span>
                                        </div>
                                    </div>
                                    <p class="mb-2 mt-2">{{ $renewal->message ?: 'Renewal requested from the app.' }}</p>
                                    @if($editPermission)
                                        <form method="POST" action="{{ route($routePrefix . '.renewals.update', $renewal->uuid) }}" class="d-flex gap-2">
                                            @csrf @method('PUT')
                                            <select name="status" class="form-control form-control-sm" style="max-width:140px">
                                                @foreach(['Pending', 'Contacted', 'Renewed', 'Closed'] as $status)
                                                    <option value="{{ $status }}" {{ $renewal->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="admin_note" value="{{ $renewal->admin_note }}" class="form-control form-control-sm" placeholder="Internal note">
                                            <button class="btn btn-sm btn-primary">Update</button>
                                        </form>
                                    @elseif($renewal->admin_note)
                                        <div class="small text-muted">{{ $renewal->admin_note }}</div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted">No renewal requests.</p>
                            @endforelse
                        </div>

                        <div class="col-lg-5">
                            <h5 class="mb-3">Coach notes <small class="text-muted">(latest is shown on the client's home screen)</small></h5>
                            @if($editPermission)
                                <form method="POST" action="{{ route($routePrefix . '.notes.store', $client->uuid) }}" class="mb-3">
                                    @csrf
                                    <textarea name="note" rows="3" class="form-control mb-2" placeholder="Write a note for {{ $client->nickname ?: $client->name }}..." required></textarea>
                                    <div class="text-end"><button class="btn btn-success btn-sm"><i class="fa fa-paper-plane me-1"></i> Add Note</button></div>
                                </form>
                            @endif
                            @forelse($notes as $note)
                                <div class="border rounded p-3 mb-2 bg-light">
                                    <div class="d-flex justify-content-between">
                                        <span class="small text-muted">{{ $note->coach->name ?? 'Coach' }} &middot; {{ $note->created_at->format('d M Y, h:i A') }}</span>
                                        @if($editPermission)
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-btn" data-uuid="{{ $note->uuid }}" title="Delete note"><i class="fe fe-trash-2"></i></button>
                                        @endif
                                    </div>
                                    <p class="mb-0 mt-1">{!! nl2br(e($note->note)) !!}</p>
                                </div>
                            @empty
                                <p class="text-muted">No notes yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ONBOARDING --}}
                <div class="tab-pane fade" id="tab-onboarding" role="tabpanel">
                    <div class="accordion" id="chaptersAccordion">
                        @foreach($chapters as $chapter)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#chapter{{ $chapter['number'] }}">
                                        Chapter {{ str_pad($chapter['number'], 2, '0', STR_PAD_LEFT) }} &middot; {{ $chapter['title'] }}
                                        <span class="badge {{ $statusBadge($chapter['status']) }} ms-2">{{ formatName($chapter['status']) }}</span>
                                    </button>
                                </h2>
                                <div id="chapter{{ $chapter['number'] }}" class="accordion-collapse collapse" data-bs-parent="#chaptersAccordion">
                                    <div class="accordion-body">
                                        @if($chapter['rows'])
                                            @include('admin.clients.partials.answers', ['rows' => $chapter['rows']])
                                        @else
                                            <p class="text-muted mb-0">Not started.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <h5 class="mt-4">Consent &amp; agreement</h5>
                    @if($client->consent)
                        <ul class="list-group">
                            @foreach(config('onboarding.consents') as $key => $consent)
                                <li class="list-group-item d-flex gap-2">
                                    <i class="fe {{ $client->consent->{$key} ? 'fe-check-circle text-success' : 'fe-x-circle text-muted' }} mt-1"></i>
                                    <span>{{ $consent['label'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="small text-muted mt-1">Accepted {{ $client->consent->accepted_at?->format('d M Y, h:i A') }} from {{ $client->consent->ip_address }}</div>
                    @else
                        <p class="text-muted">Not submitted yet.</p>
                    @endif
                </div>

                {{-- PROTOCOL --}}
                <div class="tab-pane fade" id="tab-protocol" role="tabpanel">
                    @forelse($protocols as $protocol)
                        <div class="mb-4">
                            <h5>{{ $protocol['title'] }} <span class="badge {{ $statusBadge($protocol['status']) }}">{{ formatName($protocol['status']) }}</span></h5>
                            <div class="small text-muted mb-2">Pathway: {{ $protocol['pathway'] }}{{ $protocol['submitted_at'] ? ' · Submitted ' . $protocol['submitted_at']->format('d M Y') : '' }}</div>
                            @include('admin.clients.partials.answers', ['rows' => $protocol['rows']])
                        </div>
                    @empty
                        <p class="text-muted">The client has not filled a pathway protocol yet.</p>
                    @endforelse
                </div>

                {{-- DAILY --}}
                <div class="tab-pane fade" id="tab-daily" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="bg-light">
                                <tr><th>Date</th><th>Day</th><th>Weight</th><th>Sleep</th><th>Steps</th><th>Water</th><th>Mood / Energy</th><th>Nutrition</th><th>Mindset</th><th>Meals</th><th>Notes</th><th>Photos</th></tr>
                            </thead>
                            <tbody>
                            @forelse($dailyCheckins as $checkin)
                                <tr>
                                    <td class="text-nowrap">{{ $checkin->record_date->format('d M Y') }}</td>
                                    <td>{{ $checkin->baseline_day ? 'D' . $checkin->baseline_day : '-' }}</td>
                                    <td>{{ $checkin->weight ? $checkin->weight . ' ' . $checkin->weight_unit : '-' }}</td>
                                    <td class="small">
                                        {{ $checkin->sleep_hours ? $checkin->sleep_hours . 'h' : '' }}
                                        {{ $checkin->bed_time || $checkin->wake_time ? '(' . $checkin->bed_time . ' - ' . $checkin->wake_time . ')' : '' }}
                                        <div class="text-muted">{{ config('tracking.sleep_quality.' . $checkin->sleep_quality) }}</div>
                                    </td>
                                    <td>{{ $checkin->steps ? number_format($checkin->steps) : '-' }}</td>
                                    <td>{{ $checkin->water_intake ? $checkin->water_intake . ' ' . $checkin->water_unit : (is_null($checkin->water_goal_met) ? '-' : ($checkin->water_goal_met ? 'Goal met' : 'Not yet')) }}</td>
                                    <td>{{ $checkin->mood ?? '-' }} / {{ $checkin->energy ?? '-' }}</td>
                                    <td class="small">
                                        {{ config('tracking.nutrition_adherence.' . $checkin->nutrition_adherence, '-') }}
                                        @if(!is_null($checkin->movement_done))<div class="text-muted">Movement: {{ $checkin->movement_done ? 'Yes' : 'No' }} {{ $checkin->movement_type }}</div>@endif
                                    </td>
                                    <td class="small">
                                        @if(!is_null($checkin->meditation_done))<div>Meditation: {{ $checkin->meditation_done ? 'Yes' : 'No' }}</div>@endif
                                        @if(!is_null($checkin->journaling_done))<div>Journaling: {{ $checkin->journaling_done ? 'Yes' : 'No' }}</div>@endif
                                        <div class="text-muted">{{ \Illuminate\Support\Str::limit(trim($checkin->meditation_notes . ' ' . $checkin->journaling_notes), 80) }}</div>
                                    </td>
                                    <td class="small">
                                        @foreach($checkin->meals ?? [] as $slot => $meal)
                                            <div><strong>{{ config('tracking.meal_slots.' . $slot, formatName($slot)) }}</strong> {{ $meal['time'] ?? '' }}: {{ $meal['items'] ?? '' }}</div>
                                        @endforeach
                                    </td>
                                    <td class="small">{{ $checkin->notes }}</td>
                                    <td>
                                        @foreach(fileUrls($checkin->photos) as $url)
                                            <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" alt="Check-in photo" width="40" height="40" class="rounded border mb-1" style="object-fit:cover;"></a>
                                        @endforeach
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="text-center text-muted">No daily check-ins yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- WEEKLY --}}
                <div class="tab-pane fade" id="tab-weekly" role="tabpanel">
                    @forelse($weeklyCheckins as $checkin)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between flex-wrap">
                                <h6 class="mb-2">{{ $checkin->record_date->format('d M Y') }} @if($checkin->is_baseline)<span class="badge bg-primary">Baseline</span>@endif</h6>
                                <div class="small">
                                    <strong>Weight:</strong> {{ $checkin->weight }} {{ $checkin->weight_unit }}
                                    @foreach(config('tracking.measurements') as $key => $label)
                                        @if($checkin->{$key}) &middot; <strong>{{ $label }}:</strong> {{ $checkin->{$key} }} {{ $checkin->measurement_unit }} @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="row small">
                                <div class="col-md-6">
                                    <div><strong>Self confidence:</strong> {{ $checkin->self_confidence ?: '-' }}</div>
                                    <div><strong>Cycle:</strong> {{ $checkin->last_period_start?->format('d M Y') ?? '-' }} {{ $checkin->cycle_day }} {{ $checkin->cycle_notes }}</div>
                                    <div><strong>Cravings:</strong> {{ config('tracking.cravings_levels.' . $checkin->cravings_level, '-') }} {{ $checkin->cravings_notes }}</div>
                                    <div><strong>Travel:</strong> {{ is_null($checkin->travelled) ? '-' : ($checkin->travelled ? 'Yes' : 'No') }} {{ $checkin->travel_notes }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div><strong>Wins:</strong> {{ $checkin->wins ?: '-' }}</div>
                                    <div><strong>Proud moments:</strong> {{ $checkin->proud_moments ?: '-' }}</div>
                                    <div><strong>Struggles:</strong> {{ $checkin->struggles ?: '-' }}</div>
                                    <div><strong>Notes:</strong> {{ $checkin->notes ?: '-' }}</div>
                                </div>
                            </div>
                            @if($checkin->photos)
                                <div class="mt-2">
                                    @foreach(fileUrls($checkin->photos) as $url)
                                        <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" alt="Weekly photo" width="60" height="60" class="rounded border me-1" style="object-fit:cover;"></a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">No weekly check-ins yet.</p>
                    @endforelse
                </div>

                {{-- PHOTOS & REPORTS --}}
                <div class="tab-pane fade" id="tab-media" role="tabpanel">
                    <h5>Progress photos</h5>
                    @forelse($photos as $entry)
                        <div class="mb-3">
                            <div class="fw-semibold mb-1">{{ $entry->record_date->format('d M Y') }}</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['front_photo' => 'Front', 'side_photo' => 'Side', 'back_photo' => 'Back'] as $view => $label)
                                    @if($entry->{$view})
                                        <a href="{{ getFileUrl($entry->{$view}) }}" target="_blank" class="text-center">
                                            <img src="{{ getFileUrl($entry->{$view}) }}" alt="{{ $label }} view" width="120" height="160" class="rounded border" style="object-fit:cover;">
                                            <div class="small text-muted">{{ $label }}</div>
                                        </a>
                                    @endif
                                @endforeach
                                @foreach(fileUrls($entry->extra_photos) as $url)
                                    <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" alt="Extra photo" width="120" height="160" class="rounded border" style="object-fit:cover;"></a>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No progress photos yet.</p>
                    @endforelse

                    <h5 class="mt-4">Blood reports</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="bg-light"><tr><th>Report date</th><th>Files</th><th>Notes</th><th>Uploaded</th></tr></thead>
                            <tbody>
                            @forelse($bloodReports as $report)
                                <tr>
                                    <td>{{ $report->report_date->format('d M Y') }}</td>
                                    <td>
                                        @foreach($report->files as $file)
                                            <div><a href="{{ getFileUrl($file->file_path) }}" target="_blank"><i class="fe fe-file me-1"></i>{{ $file->original_name ?? basename($file->file_path) }}</a></div>
                                        @endforeach
                                    </td>
                                    <td class="small">{{ $report->notes }}</td>
                                    <td class="small">{{ $report->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No reports uploaded.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- FOOD DIARY & NOTES --}}
                <div class="tab-pane fade" id="tab-diary" role="tabpanel">
                    <h5>Three-day food diary</h5>
                    <div class="row">
                        @foreach(config('tracking.food_diary_days') as $number => $day)
                            @php $entry = $foodDiary->firstWhere('day_number', $number); @endphp
                            <div class="col-lg-4 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <h6>{{ $day['label'] }} <small class="text-muted">({{ ucfirst($day['day_type']) }})</small></h6>
                                    @if($entry)
                                        <div class="small text-muted mb-2">{{ $entry->record_date->format('d M Y') }}</div>
                                        @foreach($entry->meals ?? [] as $slot => $meal)
                                            <div class="small mb-1"><strong>{{ config('tracking.meal_slots.' . $slot, formatName($slot)) }}</strong> {{ $meal['time'] ?? '' }}<br>{{ $meal['items'] ?? '-' }}</div>
                                        @endforeach
                                        @if($entry->sensations)
                                            <div class="mt-2">
                                                @foreach($entry->sensations as $sensation)
                                                    <span class="badge bg-light text-dark border">{{ config('tracking.food_sensations.' . $sensation, $sensation) }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($entry->context_notes)<div class="small mt-2">{{ $entry->context_notes }}</div>@endif
                                    @else
                                        <p class="text-muted small mb-0">Not logged yet.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <h5 class="mt-3">Calendar notes &amp; photos</h5>
                    @forelse($dailyNotes as $note)
                        <div class="border rounded p-2 mb-2">
                            <div class="fw-semibold small">{{ $note->note_date->format('d M Y') }}</div>
                            <div class="small">{{ $note->note }}</div>
                            @foreach(fileUrls($note->photos) as $url)
                                <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" alt="Note photo" width="50" height="50" class="rounded border me-1 mt-1" style="object-fit:cover;"></a>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-muted">No notes yet.</p>
                    @endforelse
                </div>

                {{-- CHAT --}}
                @if($chatPermission)
                    <div class="tab-pane fade" id="tab-chat" role="tabpanel">
                        <div class="dd-chat">
                            <div class="dd-chat-head">
                                <span class="dd-list-avatar">{{ mb_strtoupper(mb_substr($client->name ?? 'C', 0, 1)) }}</span>
                                <div>
                                    <div class="dd-list-title">{{ trim(($client->name ?? '') . ' ' . ($client->last_name ?? '')) ?: 'Client' }}</div>
                                    <div class="dd-list-sub">Coach chat · new messages appear automatically</div>
                                </div>
                            </div>
                            <div id="chatMessages" class="dd-chat-body">
                                <div class="dd-chat-empty"><i class="fe fe-loader"></i>Loading messages...</div>
                            </div>
                            <form id="chatForm" class="dd-chat-composer" enctype="multipart/form-data">
                                <label class="dd-chat-btn" title="Attach photos">
                                    <i class="fe fe-image"></i>
                                    <input type="file" name="attachments[]" id="chatAttachments" accept="image/*" multiple hidden>
                                </label>
                                <div class="flex-grow-1">
                                    <textarea name="message" id="chatMessage" rows="1" class="form-control" placeholder="Reply to {{ $client->nickname ?: $client->name }}..."></textarea>
                                    <small class="text-muted d-block mt-1" id="chatAttachmentInfo"></small>
                                </div>
                                <button type="submit" class="dd-chat-btn dd-chat-send" id="chatSend" title="Send"><i class="fe fe-send"></i></button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // Open the tab from the URL hash (redirects land on #tab-plan etc.)
    if (window.location.hash) {
        const link = document.querySelector('#clientTabs a[href="' + window.location.hash + '"]');
        if (link) bootstrap.Tab.getOrCreateInstance(link).show();
    }
    $('#clientTabs a').on('shown.bs.tab', function (e) {
        history.replaceState(null, '', e.target.getAttribute('href'));
    });

    // Coach note delete (stay on this page)
    $(document).on('click', '#tab-plan .delete-btn', function () {
        const url = "{{ route($routePrefix . '.notes.destroy', '') }}/" + $(this).data('uuid');
        Swal.fire({ title: 'Delete this note?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, Delete it!' })
            .then(result => {
                if (!result.isConfirmed) return;
                $.ajax({ url: url, type: 'DELETE', data: { _token: $('meta[name="csrf-token"]').attr('content') } })
                    .done(response => response.success ? window.location.reload() : showToast('error', response.message))
                    .fail(() => showToast('error', 'Delete request failed.'));
            });
    });

    @if($chatPermission)
    const messagesUrl = "{{ route($routePrefix . '.chat.messages', $client->uuid) }}";
    const sendUrl = "{{ route($routePrefix . '.chat.send', $client->uuid) }}";
    const $box = $('#chatMessages');
    let lastId = 0;

    function bubble(m) {
        // m.message is HTML-escaped by the server
        const type = m.sender_type === 'coach' ? 'mine' : (m.sender_type === 'system' ? 'system' : 'theirs');
        const photos = (m.attachments || []).map(url =>
            '<a href="' + url + '" target="_blank"><img src="' + url + '" alt="Attachment" class="dd-chat-photo"></a>'
        ).join('');
        return '<div class="dd-msg ' + type + '"><div class="dd-bubble">'
            + (type === 'mine' ? '' : '<div class="dd-msg-name">' + $('<div>').text(m.sender_name).html() + '</div>')
            + (m.message ? '<div class="dd-msg-text">' + m.message + '</div>' : '')
            + (photos ? '<div class="dd-msg-photos">' + photos + '</div>' : '')
            + '<div class="dd-msg-time">' + m.created_at + '</div>'
            + '</div></div>';
    }

    function loadMessages(initial) {
        $.get(messagesUrl, initial ? {} : { after_id: lastId }, function (response) {
            if (!response.success) return;
            if (initial) $box.empty();
            if (initial && !response.data.length) {
                $box.html('<div class="dd-chat-empty" id="chatEmpty"><i class="fe fe-message-circle"></i>No messages yet. Say hello to start the conversation.</div>');
            }
            if (response.data.length) {
                $('#chatEmpty').remove();
                response.data.forEach(m => { $box.append(bubble(m)); lastId = Math.max(lastId, m.id); });
                $box.scrollTop($box[0].scrollHeight);
            }
        });
    }

    let chatStarted = false;
    function startChat() {
        if (chatStarted) return;
        chatStarted = true;
        loadMessages(true);
        setInterval(function () { loadMessages(false); }, 15000);
    }
    $('#clientTabs a[href="#tab-chat"]').on('shown.bs.tab', startChat);
    if (window.location.hash === '#tab-chat') startChat();

    // Enter sends, Shift+Enter adds a new line
    $('#chatMessage').on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); $('#chatForm').trigger('submit'); }
    });

    $('#chatAttachments').on('change', function () {
        $('#chatAttachmentInfo').text(this.files.length ? this.files.length + ' photo(s) attached' : '');
    });

    $('#chatForm').on('submit', function (e) {
        e.preventDefault();
        const data = new FormData(this);
        data.append('_token', $('meta[name="csrf-token"]').attr('content'));
        $('#chatSend').prop('disabled', true);
        $.ajax({
            url: sendUrl, type: 'POST', data: data, processData: false, contentType: false,
            success: function (response) {
                if (!response.success) { showToast('error', response.message); return; }
                $('#chatForm')[0].reset();
                $('#chatAttachmentInfo').text('');
                loadMessages(lastId === 0);
            },
            error: function (xhr) {
                showToast('error', xhr.responseJSON?.message || 'Message could not be sent.');
            },
            complete: function () { $('#chatSend').prop('disabled', false); }
        });
    });
    @endif
});
</script>
@endsection
