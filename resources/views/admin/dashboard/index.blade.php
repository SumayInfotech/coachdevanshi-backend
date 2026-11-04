@extends('admin.layouts.master')

@section('title', 'Dashboard')

@php
    $palette = ['#8b4a4f', '#6f8a5b', '#d9959b', '#c19a6b', '#6d5a72', '#5f8b8a', '#b9a9ab'];
    $initials = fn ($u) => collect(preg_split('/\s+/', trim(($u->name ?? '') . ' ' . ($u->last_name ?? ''))))->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'C';
    $fullName = fn ($u) => trim(($u->name ?? '') . ' ' . ($u->last_name ?? '')) ?: ($u->email ?? $u->mobile ?? 'Client');
    $clientUrl = fn ($u) => $canViewClients && $u ? route('admin.clients.show', $u->uuid) : null;
    $pathwayTotal = array_sum($charts['pathways']['values']);
    $planTotal = array_sum($charts['plans']['values']);
    $funnelTop = max($charts['funnel']['values'][0] ?? 0, 1);
@endphp

@section('content')
<div class="main-container container-fluid">

    {{-- HERO --}}
    <div class="dd-hero">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 position-relative" style="z-index:1">
            <div>
                <div class="dd-eyebrow">Coach dashboard</div>
                <h1>Today's overview</h1>
                <p>Here is how your clients are doing today: onboarding, plans, daily rituals and conversations, all in one place.</p>
            </div>
            @if($canViewClients)
                <div class="dd-hero-actions">
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-primary"><i class="fe fe-users me-1"></i> View clients</a>
                    @if(hasPermission('view_pathway'))
                        <a href="{{ route('admin.pathways.index') }}" class="btn btn-light"><i class="fe fe-compass me-1"></i> Pathways</a>
                    @endif
                </div>
            @endif
        </div>
        <div class="dd-hero-chips">
            <span class="dd-chip"><span class="dd-dot"></span>{{ $kpis['checkins_today'] }} micro {{ Str::plural('check-in', $kpis['checkins_today']) }} today</span>
            <span class="dd-chip"><span class="dd-dot" style="background:var(--dd-sage)"></span>{{ $kpis['weekly_this_week'] }} weekly {{ Str::plural('check-in', $kpis['weekly_this_week']) }} this week</span>
            @if($kpis['unread_messages'])
                <span class="dd-chip"><span class="dd-dot" style="background:var(--dd-blush)"></span>{{ $kpis['unread_messages'] }} unread {{ Str::plural('message', $kpis['unread_messages']) }}</span>
            @endif
            @if($kpis['pending_renewals'])
                <span class="dd-chip"><span class="dd-dot" style="background:var(--dd-sand)"></span>{{ $kpis['pending_renewals'] }} renewal {{ Str::plural('request', $kpis['pending_renewals']) }} waiting</span>
            @endif
            @if($kpis['expiring_soon'])
                <span class="dd-chip"><span class="dd-dot" style="background:var(--dd-warning)"></span>{{ $kpis['expiring_soon'] }} {{ Str::plural('plan', $kpis['expiring_soon']) }} ending in 14 days</span>
            @endif
        </div>
    </div>

    {{-- KPI ROW --}}
    <div class="row dd-eq">
        <div class="col-sm-6 col-xl-3">
            <div class="card dd-kpi">
                <div class="dd-kpi-top">
                    <div>
                        <div class="dd-kpi-label">Total clients</div>
                        <div class="dd-kpi-value">{{ number_format($kpis['total_clients']) }}</div>
                    </div>
                    <span class="dd-kpi-icon dd-tone-primary"><i class="fe fe-users"></i></span>
                </div>
                <div class="dd-kpi-foot">
                    @if($kpis['growth'] === null)
                        <span class="dd-trend up"><i class="fe fe-arrow-up-right"></i>New</span>
                    @elseif($kpis['growth'] > 0)
                        <span class="dd-trend up"><i class="fe fe-arrow-up-right"></i>{{ $kpis['growth'] }}%</span>
                    @elseif($kpis['growth'] < 0)
                        <span class="dd-trend down"><i class="fe fe-arrow-down-right"></i>{{ abs($kpis['growth']) }}%</span>
                    @else
                        <span class="dd-trend flat">0%</span>
                    @endif
                    {{ $kpis['new_this_month'] }} joined this month
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card dd-kpi">
                <div class="dd-kpi-top">
                    <div>
                        <div class="dd-kpi-label">Active plans</div>
                        <div class="dd-kpi-value">{{ number_format($kpis['active_plans']) }}</div>
                    </div>
                    <span class="dd-kpi-icon dd-tone-sage"><i class="fe fe-calendar"></i></span>
                </div>
                <div class="dd-kpi-foot">
                    @if($kpis['expiring_soon'])
                        <span class="dd-trend down"><i class="fe fe-clock"></i>{{ $kpis['expiring_soon'] }}</span> ending within 14 days
                    @else
                        <span class="dd-trend up"><i class="fe fe-check"></i>0</span> none ending within 14 days
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card dd-kpi">
                <div class="dd-kpi-top">
                    <div>
                        <div class="dd-kpi-label">Onboarding completed</div>
                        <div class="dd-kpi-value">{{ $kpis['onboarded_rate'] }}<small>%</small></div>
                    </div>
                    <span class="dd-kpi-icon dd-tone-blush"><i class="fe fe-clipboard"></i></span>
                </div>
                <div class="dd-kpi-foot w-100 flex-column align-items-stretch gap-2">
                    <div class="dd-meter"><span style="width: {{ $kpis['onboarded_rate'] }}%"></span></div>
                    <span>{{ $kpis['onboarded'] }} of {{ $kpis['total_clients'] }} clients finished all 6 chapters</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card dd-kpi">
                <div class="dd-kpi-top">
                    <div>
                        <div class="dd-kpi-label">7-day engagement</div>
                        <div class="dd-kpi-value">{{ $kpis['engagement_rate'] }}<small>%</small></div>
                    </div>
                    <span class="dd-kpi-icon dd-tone-sand"><i class="fe fe-activity"></i></span>
                </div>
                <div class="dd-kpi-foot w-100 flex-column align-items-stretch gap-2">
                    <div class="dd-meter sand"><span style="width: {{ $kpis['engagement_rate'] }}%"></span></div>
                    <span>{{ $kpis['engaged_clients'] }} of {{ $kpis['active_plan_clients'] }} plan clients logged a check-in</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MINI STATS --}}
    <div class="row dd-eq">
        <div class="col-6 col-xl-3">
            <div class="card dd-mini">
                <span class="dd-kpi-icon dd-tone-sand"><i class="fe fe-refresh-cw"></i></span>
                <div>
                    <div class="dd-mini-value">{{ $kpis['pending_renewals'] }}</div>
                    <div class="dd-mini-label">Pending renewals</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card dd-mini">
                <span class="dd-kpi-icon dd-tone-primary"><i class="fe fe-message-circle"></i></span>
                <div>
                    <div class="dd-mini-value">{{ $kpis['unread_messages'] }}</div>
                    <div class="dd-mini-label">Unread messages · {{ $kpis['unread_threads'] }} {{ Str::plural('client', $kpis['unread_threads']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card dd-mini">
                <span class="dd-kpi-icon dd-tone-sage"><i class="fe fe-heart"></i></span>
                <div>
                    <div class="dd-mini-value">{{ $kpis['health_connected'] }} <small class="text-muted fs-13 fw-normal">({{ $kpis['health_rate'] }}%)</small></div>
                    <div class="dd-mini-label">Health app connected</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card dd-mini">
                <span class="dd-kpi-icon dd-tone-plum"><i class="fe fe-image"></i></span>
                <div>
                    <div class="dd-mini-value">{{ $kpis['photos_total'] }} <small class="text-muted fs-13 fw-normal">/ {{ $kpis['reports_total'] }}</small></div>
                    <div class="dd-mini-label">Progress photos / blood reports</div>
                </div>
            </div>
        </div>
    </div>

    {{-- GROWTH + PATHWAYS --}}
    <div class="row dd-eq">
        <div class="col-xl-8">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Client growth</h5>
                        <div class="dd-sub">New sign-ups and plans started, last 12 months</div>
                    </div>
                    <div class="dd-legend">
                        <span><i style="background:{{ $palette[0] }}"></i>New clients</span>
                        <span><i style="background:{{ $palette[1] }}"></i>Plans started</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dd-chart grow"><canvas id="chartSignups"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Clients by pathway</h5>
                        <div class="dd-sub">Programme each client selected</div>
                    </div>
                </div>
                <div class="card-body">
                    @if($pathwayTotal)
                        <div class="dd-donut-wrap dd-chart sm">
                            <canvas id="chartPathways"></canvas>
                            <div class="dd-donut-center"><b>{{ $pathwayTotal }}</b><span>clients</span></div>
                        </div>
                        <ul class="dd-split-list">
                            @foreach($charts['pathways']['labels'] as $i => $label)
                                <li>
                                    <i style="background:{{ $palette[$i % count($palette)] }}"></i>{{ $label }}
                                    <b>{{ $charts['pathways']['values'][$i] }}</b>
                                    <em>{{ round($charts['pathways']['values'][$i] * 100 / $pathwayTotal) }}%</em>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="dd-empty"><i class="fe fe-compass"></i>No clients yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ACTIVITY + FUNNEL --}}
    <div class="row dd-eq">
        <div class="col-xl-8">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Tracking activity</h5>
                        <div class="dd-sub">Entries logged by clients per day, last 30 days</div>
                    </div>
                    <div class="dd-legend">
                        <span><i style="background:{{ $palette[0] }}"></i>Micro check-ins</span>
                        <span><i style="background:{{ $palette[1] }}"></i>Weekly check-ins</span>
                        <span><i style="background:{{ $palette[3] }}"></i>Progress photos</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dd-chart grow"><canvas id="chartActivity"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Client journey</h5>
                        <div class="dd-sub">From sign-up to an active plan</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dd-funnel">
                        @foreach($charts['funnel']['labels'] as $i => $label)
                            @php $v = $charts['funnel']['values'][$i]; $pct = round($v * 100 / $funnelTop); @endphp
                            <div class="dd-funnel-row">
                                <div class="dd-funnel-top">
                                    <span>{{ $label }}</span>
                                    <span><b>{{ $v }}</b><em>{{ $pct }}%</em></span>
                                </div>
                                <div class="dd-funnel-bar"><span style="width: {{ $pct }}%"></span></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- WELLNESS + PLANS + ATTENTION --}}
    <div class="row dd-eq">
        <div class="col-xl-5">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Wellness snapshot</h5>
                        <div class="dd-sub">Averages from {{ $wellness['entries'] }} micro check-ins, last 30 days</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dd-well-grid mb-4">
                        <div class="dd-well"><div class="dd-well-label"><i class="fe fe-smile"></i> Mood</div><div class="dd-well-value">{{ $wellness['mood'] ?? '–' }}<small> / 5</small></div></div>
                        <div class="dd-well"><div class="dd-well-label"><i class="fe fe-zap"></i> Energy</div><div class="dd-well-value">{{ $wellness['energy'] ?? '–' }}<small> / 5</small></div></div>
                        <div class="dd-well"><div class="dd-well-label"><i class="fe fe-moon"></i> Sleep</div><div class="dd-well-value">{{ $wellness['sleep'] ?? '–' }}<small> hrs</small></div></div>
                        <div class="dd-well"><div class="dd-well-label"><i class="fe fe-droplet"></i> Water goal</div><div class="dd-well-value">{{ $wellness['water'] ?? '–' }}<small>{{ $wellness['water'] !== null ? '%' : '' }}</small></div></div>
                        <div class="dd-well"><div class="dd-well-label"><i class="fe fe-trending-up"></i> Movement</div><div class="dd-well-value">{{ $wellness['movement'] ?? '–' }}<small>{{ $wellness['movement'] !== null ? '%' : '' }}</small></div></div>
                        <div class="dd-well"><div class="dd-well-label"><i class="fe fe-sun"></i> Meditation</div><div class="dd-well-value">{{ $wellness['meditation'] ?? '–' }}<small>{{ $wellness['meditation'] !== null ? '%' : '' }}</small></div></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold" style="color:var(--dd-ink);font-size:13.5px">Mood &amp; energy trend</span>
                        <div class="dd-legend">
                            <span><i style="background:{{ $palette[2] }}"></i>Mood</span>
                            <span><i style="background:{{ $palette[1] }}"></i>Energy</span>
                        </div>
                    </div>
                    <div class="dd-chart sm"><canvas id="chartWellness"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Plan status</h5>
                        <div class="dd-sub">All plans created by coaches</div>
                    </div>
                </div>
                <div class="card-body">
                    @if($planTotal)
                        <div class="dd-donut-wrap dd-chart sm">
                            <canvas id="chartPlans"></canvas>
                            <div class="dd-donut-center"><b>{{ $planTotal }}</b><span>plans</span></div>
                        </div>
                        <ul class="dd-split-list">
                            @php $planColors = [$palette[1], $palette[3], $palette[0], $palette[6]]; @endphp
                            @foreach($charts['plans']['labels'] as $i => $label)
                                <li><i style="background:{{ $planColors[$i] }}"></i>{{ $label }}<b>{{ $charts['plans']['values'][$i] }}</b></li>
                            @endforeach
                        </ul>
                    @else
                        <div class="dd-empty"><i class="fe fe-calendar"></i>No plans created yet.</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Needs attention</h5>
                        <div class="dd-sub">On a plan, no micro check-in for 3+ days</div>
                    </div>
                    @if($attention->count())<span class="dd-count">{{ $attention->count() }}</span>@endif
                </div>
                @if($attention->isEmpty())
                    <div class="dd-empty"><i class="fe fe-check-circle"></i>Every client on a plan has checked in recently.</div>
                @else
                    <ul class="dd-list mt-2">
                        @foreach($attention as $row)
                            @php $url = $clientUrl($row['user']); @endphp
                            <li>
                                <a @if($url) href="{{ $url }}" @endif class="dd-list-item">
                                    <span class="dd-list-avatar">{{ $initials($row['user']) }}</span>
                                    <div class="dd-list-main">
                                        <div class="dd-list-title">{{ $fullName($row['user']) }}</div>
                                        <div class="dd-list-sub">{{ $row['last_date'] ? 'Last check-in ' . $row['last_date']->format('d M') : 'No check-in logged yet' }}</div>
                                    </div>
                                    <div class="dd-list-end">
                                        <span class="badge bg-danger">{{ $row['days'] !== null ? $row['days'] . ' days' : 'Never' }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- PLANS EXPIRING + RENEWALS --}}
    <div class="row dd-eq">
        <div class="col-xl-6">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Plans ending soon</h5>
                        <div class="dd-sub">Active plans that end within 14 days</div>
                    </div>
                    @if($canViewClients)<a href="{{ route('admin.clients.index') }}" class="dd-link">All clients</a>@endif
                </div>
                @if($expiringPlans->isEmpty())
                    <div class="dd-empty"><i class="fe fe-calendar"></i>No plans are ending in the next 14 days.</div>
                @else
                    <ul class="dd-list mt-2">
                        @foreach($expiringPlans as $plan)
                            @php $url = $clientUrl($plan->user); $left = (int) now()->startOfDay()->diffInDays($plan->end_date); @endphp
                            <li>
                                <a @if($url) href="{{ $url }}" @endif class="dd-list-item">
                                    <span class="dd-list-avatar" style="background:var(--dd-sage-soft);color:var(--dd-sage)">{{ $plan->user ? $initials($plan->user) : 'C' }}</span>
                                    <div class="dd-list-main">
                                        <div class="dd-list-title">{{ $plan->user ? $fullName($plan->user) : 'Client' }}</div>
                                        <div class="dd-list-sub">{{ $plan->title }}</div>
                                    </div>
                                    <div class="dd-list-end">
                                        <span class="badge {{ $left <= 7 ? 'bg-danger' : 'bg-warning' }}">{{ $left === 0 ? 'Ends today' : $left . ' ' . Str::plural('day', $left) . ' left' }}</span>
                                        <div class="dd-list-sub">{{ $plan->end_date->format('d M Y') }}</div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Renewal requests</h5>
                        <div class="dd-sub">Clients who asked to renew from the app</div>
                    </div>
                    @if($kpis['pending_renewals'])<span class="dd-count">{{ $kpis['pending_renewals'] }}</span>@endif
                </div>
                @if($renewals->isEmpty())
                    <div class="dd-empty"><i class="fe fe-refresh-cw"></i>No pending renewal requests.</div>
                @else
                    <ul class="dd-list mt-2">
                        @foreach($renewals as $renewal)
                            @php $url = $clientUrl($renewal->user); @endphp
                            <li>
                                <a @if($url) href="{{ $url }}" @endif class="dd-list-item">
                                    <span class="dd-list-avatar" style="background:var(--dd-sand-soft);color:var(--dd-sand)">{{ $renewal->user ? $initials($renewal->user) : 'C' }}</span>
                                    <div class="dd-list-main">
                                        <div class="dd-list-title">{{ $renewal->user ? $fullName($renewal->user) : 'Client' }}</div>
                                        <div class="dd-list-sub">{{ $renewal->message ? Str::limit($renewal->message, 70) : ($renewal->plan->title ?? 'Plan renewal') }}</div>
                                    </div>
                                    <div class="dd-list-end">
                                        <span class="badge bg-warning">Pending</span>
                                        <div class="dd-list-sub">{{ $renewal->created_at?->diffForHumans() }}</div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- MESSAGES + RECENT CLIENTS --}}
    <div class="row dd-eq">
        <div class="col-xl-6">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Unread messages</h5>
                        <div class="dd-sub">Latest message from each client waiting for a reply</div>
                    </div>
                    @if($kpis['unread_messages'])<span class="dd-count">{{ $kpis['unread_messages'] }}</span>@endif
                </div>
                @if($messages->isEmpty())
                    <div class="dd-empty"><i class="fe fe-message-circle"></i>You're all caught up.</div>
                @else
                    <ul class="dd-list mt-2">
                        @foreach($messages as $row)
                            @php $url = $clientUrl($row['user']); @endphp
                            <li>
                                <a @if($url) href="{{ $url }}" @endif class="dd-list-item">
                                    <span class="dd-list-avatar">{{ $initials($row['user']) }}</span>
                                    <div class="dd-list-main">
                                        <div class="dd-list-title">{{ $fullName($row['user']) }}</div>
                                        <div class="dd-list-sub">{{ $row['message']->message ? Str::limit($row['message']->message, 80) : 'Sent an attachment' }}</div>
                                    </div>
                                    <div class="dd-list-end">
                                        @if($row['count'] > 1)<span class="dd-count">{{ $row['count'] }}</span>@endif
                                        <div class="dd-list-sub">{{ $row['message']->created_at?->diffForHumans(null, true) }}</div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="dd-card-head">
                    <div>
                        <h5>Recently joined</h5>
                        <div class="dd-sub">Newest app sign-ups and where they are</div>
                    </div>
                    @if($canViewClients)<a href="{{ route('admin.clients.index') }}" class="dd-link">View all</a>@endif
                </div>
                @if($recentClients->isEmpty())
                    <div class="dd-empty"><i class="fe fe-user-plus"></i>No clients have signed up yet.</div>
                @else
                    <ul class="dd-list mt-2">
                        @foreach($recentClients as $client)
                            @php
                                $url = $clientUrl($client);
                                [$stage, $tone] = $client->is_onboarding_completed
                                    ? ($client->pathway_uuid ? ['Pathway selected', 'bg-success'] : ['Onboarded', 'bg-info'])
                                    : ['Onboarding', 'bg-secondary'];
                            @endphp
                            <li>
                                <a @if($url) href="{{ $url }}" @endif class="dd-list-item">
                                    @if($client->profile_image)
                                        <img src="{{ getFileUrl($client->profile_image) }}" class="dd-list-avatar" alt="">
                                    @else
                                        <span class="dd-list-avatar">{{ $initials($client) }}</span>
                                    @endif
                                    <div class="dd-list-main">
                                        <div class="dd-list-title">{{ $fullName($client) }}</div>
                                        <div class="dd-list-sub">{{ $client->email ?: trim(($client->phone_code ? '+' . ltrim($client->phone_code, '+') . ' ' : '') . $client->mobile) }}</div>
                                    </div>
                                    <div class="dd-list-end">
                                        <span class="badge {{ $tone }}">{{ $stage }}</span>
                                        <div class="dd-list-sub">{{ $client->created_at?->format('d M Y') }}</div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/sash/plugins/chart/Chart.bundle.js') }}"></script>
<script>
(function () {
    const data = @json($charts);
    const palette = @json($palette);
    let charts = [];

    function css(name) { return getComputedStyle(document.body).getPropertyValue(name).trim(); }
    function alpha(hex, a) {
        const n = parseInt(hex.replace('#', ''), 16);
        return `rgba(${(n >> 16) & 255}, ${(n >> 8) & 255}, ${n & 255}, ${a})`;
    }
    function gradient(ctx, color) {
        const g = ctx.createLinearGradient(0, 0, 0, ctx.canvas.clientHeight || 280);
        g.addColorStop(0, alpha(color, .22));
        g.addColorStop(1, alpha(color, 0));
        return g;
    }

    function render() {
        charts.forEach(c => c.destroy());
        charts = [];

        const ink = css('--dd-muted') || '#7d6b6d';
        const grid = css('--dd-line') || '#eee3df';
        const panel = css('--dd-panel') || '#ffffff';
        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        Chart.defaults.font.size = 12;
        Chart.defaults.color = ink;

        const tooltip = {
            backgroundColor: '#2b2224', titleColor: '#fff', bodyColor: '#f3e9e6',
            padding: 10, cornerRadius: 10, displayColors: true, boxPadding: 4, usePointStyle: true,
        };
        const axes = (extraY = {}) => ({
            x: { grid: { display: false, drawBorder: false }, ticks: { maxRotation: 0, autoSkipPadding: 14 } },
            y: Object.assign({ beginAtZero: true, grid: { color: grid, drawBorder: false }, ticks: { precision: 0 } }, extraY),
        });
        const base = { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { display: false }, tooltip } };

        const el = id => document.getElementById(id);

        if (el('chartSignups')) {
            charts.push(new Chart(el('chartSignups'), {
                data: {
                    labels: data.signups.labels,
                    datasets: [
                        { type: 'bar', label: 'New clients', data: data.signups.clients, backgroundColor: palette[0], borderRadius: 8, maxBarThickness: 26, order: 2 },
                        { type: 'line', label: 'Plans started', data: data.signups.plans, borderColor: palette[1], backgroundColor: panel, borderWidth: 2.5, tension: .35, cubicInterpolationMode: 'monotone', pointRadius: 3.5, pointBorderWidth: 2, pointHoverRadius: 5, order: 1 },
                    ],
                },
                options: Object.assign({}, base, { scales: axes() }),
            }));
        }

        if (el('chartActivity')) {
            const ctx = el('chartActivity').getContext('2d');
            charts.push(new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.activity.labels,
                    datasets: [
                        { label: 'Micro check-ins', data: data.activity.daily, borderColor: palette[0], backgroundColor: gradient(ctx, palette[0]), fill: true, borderWidth: 2.5, tension: .35, cubicInterpolationMode: 'monotone', pointRadius: 0, pointHoverRadius: 4 },
                        { label: 'Weekly check-ins', data: data.activity.weekly, borderColor: palette[1], backgroundColor: 'transparent', borderWidth: 2, tension: .35, cubicInterpolationMode: 'monotone', pointRadius: 0, pointHoverRadius: 4 },
                        { label: 'Progress photos', data: data.activity.photos, borderColor: palette[3], backgroundColor: 'transparent', borderWidth: 2, borderDash: [5, 4], tension: .35, cubicInterpolationMode: 'monotone', pointRadius: 0, pointHoverRadius: 4 },
                    ],
                },
                options: Object.assign({}, base, { scales: axes() }),
            }));
        }

        if (el('chartWellness')) {
            charts.push(new Chart(el('chartWellness'), {
                type: 'line',
                data: {
                    labels: data.wellness.labels,
                    datasets: [
                        { label: 'Mood', data: data.wellness.mood, borderColor: palette[2], backgroundColor: palette[2], borderWidth: 2.5, tension: .35, cubicInterpolationMode: 'monotone', pointRadius: 0, pointHoverRadius: 4, spanGaps: true },
                        { label: 'Energy', data: data.wellness.energy, borderColor: palette[1], backgroundColor: palette[1], borderWidth: 2.5, tension: .35, cubicInterpolationMode: 'monotone', pointRadius: 0, pointHoverRadius: 4, spanGaps: true },
                    ],
                },
                options: Object.assign({}, base, { scales: axes({ beginAtZero: false, min: 1, max: 5, ticks: { stepSize: 1 } }) }),
            }));
        }

        const donut = (id, labels, values, colors) => {
            if (!el(id)) return;
            charts.push(new Chart(el(id), {
                type: 'doughnut',
                data: { labels, datasets: [{ data: values, backgroundColor: colors, borderColor: panel, borderWidth: 3, hoverOffset: 6 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false }, tooltip } },
            }));
        };
        donut('chartPathways', data.pathways.labels, data.pathways.values, data.pathways.labels.map((_, i) => palette[i % palette.length]));
        donut('chartPlans', data.plans.labels, data.plans.values, [palette[1], palette[3], palette[0], palette[6]]);
    }

    document.addEventListener('DOMContentLoaded', render);
    // Re-draw with the dark/light palette after the theme toggle
    $(document).on('click', '.layout-setting', () => setTimeout(render, 60));
})();
</script>
@endsection
