@extends('layouts.admin', ['title' => 'Facilities Dashboard', 'subtitle' => 'Venue reservations, approval routing, and facility resources.'])

@section('page-actions')
<a class="btn-primaryx" href="{{ route('fmo.reservations.index', ['status' => 'pending']) }}"><i class="bi bi-inbox"></i> Pending Requests</a>
<a class="btn-soft" href="{{ route('fmo.venues.create') }}"><i class="bi bi-building-add"></i> Add Venue</a>
@endsection

@section('content')

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-label">Pending Reservations</div>
        <div class="stat-value">{{ $stats['pending'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-label">Approved Reservations</div>
        <div class="stat-value">{{ $stats['approved'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-cyan"><i class="bi bi-building"></i></div>
        <div class="stat-label">Venues (Active / Total)</div>
        <div class="stat-value">{{ $stats['venues_active'] }}<span style="font-size:18px;color:var(--ink-400)"> / {{ $stats['venues_total'] }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-red"><i class="bi bi-x-circle"></i></div>
        <div class="stat-label">Rejected Reservations</div>
        <div class="stat-value">{{ $stats['rejected'] }}</div>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon icon-cyan"><i class="bi bi-calendar-event"></i></div>
        <div class="stat-label">Upcoming Confirmed</div>
        <div class="stat-value">{{ $stats['upcoming'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="bi bi-box-seam"></i></div>
        <div class="stat-label">Facility Items</div>
        <div class="stat-value">{{ $stats['items'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="bi bi-tools"></i></div>
        <div class="stat-label">Facility Services</div>
        <div class="stat-value">{{ $stats['services'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-cyan"><i class="bi bi-people"></i></div>
        <div class="stat-label">Facilities Users</div>
        <div class="stat-value">{{ $stats['fmo_users'] }}</div>
    </div>
</div>

<div class="panel-grid-2">
    <div class="surface p-3">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title" style="font-size:16px">Waiting for Your Review</h2>
                <div class="module-note">Reservations that have not been approved or rejected yet.</div>
            </div>
        </div>
        @forelse($pendingQueue as $reservation)
            <div class="request-card">
                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                    <div>
                        <div style="font-weight:700;font-size:13px">{{ $reservation->title }}</div>
                        <div class="tiny">{{ $reservation->user->name ?? 'N/A' }} · {{ $reservation->facility->name ?? 'No venue' }}</div>
                        <div class="tiny-2">{{ optional($reservation->start_at)->format('M d, Y h:i A') }}</div>
                    </div>
                    <a class="btn-soft small-btn" href="{{ route('fmo.reservations.show', $reservation) }}"><i class="bi bi-eye"></i> View All Details</a>
                </div>
            </div>
        @empty
            <div class="empty-state">Nothing is waiting for review right now.</div>
        @endforelse
    </div>

    <div class="surface p-3">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title" style="font-size:16px">Upcoming Confirmed Schedules</h2>
                <div class="module-note">Approved bookings starting from today onwards.</div>
            </div>
        </div>
        @forelse($upcoming as $reservation)
            <div class="request-card">
                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                    <div>
                        <div style="font-weight:700;font-size:13px">{{ $reservation->title }}</div>
                        <div class="tiny">{{ $reservation->facility->name ?? 'No venue' }} · {{ $reservation->user->name ?? 'N/A' }}</div>
                        <div class="tiny-2">{{ optional($reservation->start_at)->format('M d, Y h:i A') }} — {{ optional($reservation->end_at)->format('h:i A') }}</div>
                    </div>
                    <a class="btn-soft small-btn" href="{{ route('fmo.reservations.show', $reservation) }}"><i class="bi bi-eye"></i> Details</a>
                </div>
            </div>
        @empty
            <div class="empty-state">No upcoming confirmed schedules.</div>
        @endforelse
    </div>
</div>
@endsection
