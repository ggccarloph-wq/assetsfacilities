@extends('layouts.admin', ['title' => 'Reservation Details', 'subtitle' => 'Everything the requestor submitted, plus who has approved and who has not.'])

@section('page-actions')
<a class="btn-soft" href="{{ route('fmo.reservations.index') }}"><i class="bi bi-arrow-left"></i> Back to Requests</a>
@endsection

@section('content')
@php $user = auth()->user(); @endphp

<div class="surface p-3 mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title" style="font-size:18px">{{ $reservation->reservation_no }}</h2>
            <div class="module-note">{{ $reservation->title }}</div>
        </div>
        <span class="status {{ $reservation->status === 'approved' ? 'approved' : ($reservation->status === 'rejected' ? 'low' : 'pending') }}">{{ ucfirst($reservation->status) }}</span>
    </div>

    {{-- FMO actions --}}
    <div class="request-actions mb-3" style="justify-content:flex-start">
        @if($reservation->isPending())
            <form method="POST" action="{{ route('fmo.reservations.approve', $reservation) }}">@csrf
                <button class="btn-approve"><i class="bi bi-check-lg"></i> Approve Reservation</button>
            </form>
            <button class="btn-reject" type="button" data-bs-toggle="collapse" data-bs-target="#reject-box"><i class="bi bi-x-lg"></i> Reject</button>
        @endif

        @if($proposal && !$proposal->fmo_signed_at && $proposal->isAwaitingReview())
            <form method="POST" action="{{ route('activity-proposals.sign-facilities', $proposal) }}">@csrf
                <button class="btn-primaryx"><i class="bi bi-pen"></i> Sign as Facilities Management</button>
            </form>
        @endif

        @if($user->canDeleteFacilityRecords())
            <form method="POST" action="{{ route('fmo.reservations.destroy', $reservation) }}" onsubmit="return confirm('Delete this reservation permanently? This cannot be undone.');">
                @csrf @method('DELETE')
                <button class="btn-soft small-btn text-danger"><i class="bi bi-trash"></i> Delete</button>
            </form>
        @endif
    </div>

    @if($reservation->isPending())
    <div class="collapse mb-3" id="reject-box">
        <form method="POST" action="{{ route('fmo.reservations.reject', $reservation) }}" class="p-3" style="background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r-sm)">
            @csrf
            <label class="form-label">Reason for rejection</label>
            <textarea name="rejection_reason" class="form-control mb-2" rows="2" placeholder="e.g. Venue already blocked for maintenance on that date."></textarea>
            <button class="btn-reject"><i class="bi bi-x-lg"></i> Confirm Rejection</button>
        </form>
    </div>
    @endif

    <h3 class="module-title mb-2" style="font-size:14px">Request Details</h3>
    <table class="kv-table">
        <tr><th><i class="bi bi-person me-1"></i>Requestor</th><td>{{ $reservation->user->name ?? 'N/A' }} <span class="tiny">({{ $reservation->user->email ?? '—' }})</span></td></tr>
        <tr><th><i class="bi bi-building me-1"></i>Department</th><td>{{ $reservation->user->department->name ?? 'Not assigned' }}</td></tr>
        <tr><th><i class="bi bi-geo-alt me-1"></i>Venue</th><td>{{ $reservation->facility->name ?? 'N/A' }}{{ $reservation->facility && $reservation->facility->location ? ' — '.$reservation->facility->location : '' }}</td></tr>
        <tr><th><i class="bi bi-clock me-1"></i>Schedule</th><td>{{ optional($reservation->start_at)->format('M d, Y h:i A') }} — {{ optional($reservation->end_at)->format('M d, Y h:i A') }}</td></tr>
        <tr><th><i class="bi bi-card-text me-1"></i>Purpose</th><td class="kv-wide">{!! nl2br(e($reservation->purpose ?: 'Not specified')) !!}</td></tr>
        <tr><th><i class="bi bi-calendar-plus me-1"></i>Submitted</th><td>{{ optional($reservation->created_at)->format('M d, Y h:i A') }}</td></tr>
        @if($reservation->rejection_reason)
        <tr><th><i class="bi bi-exclamation-octagon me-1"></i>Rejection Reason</th><td class="kv-wide">{{ $reservation->rejection_reason }}</td></tr>
        @endif
    </table>

    @if($proposal)
    <h3 class="module-title mb-2" style="font-size:14px">Activity Proposal — {{ $proposal->proposal_no }}</h3>
    <table class="kv-table">
        <tr><th><i class="bi bi-people me-1"></i>Organization</th><td>{{ $proposal->organization_name }}</td></tr>
        <tr><th><i class="bi bi-person-badge me-1"></i>Position</th><td>{{ $proposal->requester_position ?: 'N/A' }}</td></tr>
        <tr><th><i class="bi bi-calendar3 me-1"></i>Day(s) of Activity</th><td>{{ $proposal->activity_days ?: 'N/A' }}</td></tr>
        <tr><th><i class="bi bi-person-lines-fill me-1"></i>Expected Attendees</th><td>{{ $proposal->participants_count }}</td></tr>
        <tr><th><i class="bi bi-mic me-1"></i>Speaker</th><td>{{ $proposal->speaker_name ?: 'None' }}</td></tr>
        @if($proposal->venue_other_note)
        <tr><th><i class="bi bi-pin-map me-1"></i>Venue Note</th><td>{{ $proposal->venue_other_note }}</td></tr>
        @endif
        <tr><th><i class="bi bi-list-check me-1"></i>Program Flow</th><td class="kv-wide">{!! nl2br(e($proposal->program_flow)) !!}</td></tr>
        <tr><th><i class="bi bi-flag me-1"></i>Routing Status</th><td>{{ $proposal->statusLabel() }}</td></tr>
    </table>
    @endif
</div>

{{-- ============================ ITEMS & SERVICES ============================ --}}
<div class="surface p-3 mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title" style="font-size:16px">Items &amp; Services Requested</h2>
            <div class="module-note">Exactly what the requestor ticked on the form, with the quantities they entered.</div>
        </div>
    </div>

    @if(count($requirements) === 0 && !$otherNote)
        <div class="empty-state">The requestor did not request any items or services.</div>
    @else
        @if(count($requirements))
        <div class="table-responsive">
            <table class="req-summary-table">
                <thead><tr><th>Item / Service</th><th>Type</th><th>Quantity</th></tr></thead>
                <tbody>
                @foreach($requirements as $line)
                    <tr>
                        <td>{{ $line['name'] ?? '—' }}</td>
                        <td>
                            @if(($line['type'] ?? null) === 'service')
                                <span class="status in-use">Service</span>
                            @elseif(($line['type'] ?? null) === 'item')
                                <span class="status available">Item</span>
                            @else
                                <span class="tag">Legacy entry</span>
                            @endif
                        </td>
                        <td class="qty">
                            @if(!empty($line['quantity']))
                                {{ $line['quantity'] }}{{ !empty($line['unit']) ? ' '.$line['unit'] : '' }}
                            @else
                                <span class="tiny text-muted">Not specified</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($otherNote)
        <div class="note-callout mt-3">
            <i class="bi bi-pencil-square"></i>
            <div><strong>Others (typed by the requestor):</strong><br>{!! nl2br(e($otherNote)) !!}</div>
        </div>
        @endif
    @endif
</div>

{{-- ============================ APPROVAL TRAIL ============================ --}}
<div class="surface p-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title" style="font-size:16px">Approval Trail</h2>
            <div class="module-note">Who has already approved, who is currently holding it, and who has not acted yet.</div>
        </div>
        @php $progress = $reservation->approvalProgress(); @endphp
        <span class="tag">{{ $progress['done'] }} of {{ $progress['total'] }} completed</span>
    </div>

    <ul class="approval-timeline">
        @foreach($trail as $step)
            @php
                $state = $step['state'];
                $icon = match ($state) {
                    'signed' => 'bi-check-lg',
                    'waiting' => 'bi-hourglass-split',
                    'rejected', 'blocked' => 'bi-x-lg',
                    default => 'bi-dash-lg',
                };
                $label = match ($state) {
                    'signed' => $step['at'] ? 'Approved ' . $step['at']->format('M d, Y h:i A') : 'Approved',
                    'waiting' => 'Waiting for action now',
                    'rejected' => $step['at'] ? 'Rejected ' . $step['at']->format('M d, Y h:i A') : 'Rejected',
                    'blocked' => 'Not reached — routing stopped',
                    default => 'Not yet approved',
                };
            @endphp
            <li class="{{ $state }}">
                <div class="step-dot"><i class="bi {{ $icon }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">{{ $step['role'] }}</div>
                        <div class="step-name">{{ $step['name'] }}</div>
                        @if(!empty($step['note']))
                            <div class="step-note">{{ $step['note'] }}</div>
                        @endif
                    </div>
                    <span class="step-meta {{ $state }}">{{ $label }}</span>
                </div>
            </li>
        @endforeach
    </ul>

    @php
        $stillPending = $reservation->pendingApproverNames();
    @endphp
    @if(count($stillPending))
    <div class="note-callout mt-2">
        <i class="bi bi-info-circle"></i>
        <div><strong>Still waiting on:</strong> {{ implode(' · ', $stillPending) }}</div>
    </div>
    @endif
</div>
@endsection
