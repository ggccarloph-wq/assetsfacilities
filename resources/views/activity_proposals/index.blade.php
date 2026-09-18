@extends('layouts.admin', ['title' => 'Activity Proposals', 'subtitle' => 'Digital routing with Facilities Management as the first approval step.'])
@section('page-actions')
@if(auth()->user()->isRequestor())<a href="{{ route('activity-proposals.create') }}" class="btn-primaryx"><i class="bi bi-calendar-plus"></i> Reserve Facility</a>@endif
@endsection
@section('content')
<div class="page-tabs">
    <span class="active">
        @if(auth()->user()->isAdviserApprover()) Adviser Queue
        @elseif(auth()->user()->isDeanApprover()) Department Queue
        @elseif(auth()->user()->isFmoSuperAdmin()) All Proposals
        @elseif(auth()->user()->isFmoSide()) FMO Queue
        @else My Proposals
        @endif
    </span>
</div>
<div class="surface p-3">
    @forelse($proposals as $proposal)
    <article class="rec rec--plain">
        <div class="rec-body">
            <div class="rec-kicker">{{ $proposal->proposal_no }} &middot; {{ optional($proposal->start_at)->format('M d, Y H:i') }}</div>
            <h3 class="rec-heading"><a href="{{ route('activity-proposals.show', $proposal) }}">{{ $proposal->title }}</a></h3>
            <dl class="rec-facts">
                <div><dt>Requested by</dt><dd>{{ $proposal->user->name ?? 'Unknown' }}<span>Venue: {{ $proposal->facility->name ?? 'N/A' }}</span></dd></div>
                <div><dt>Adviser</dt><dd>{{ $proposal->adviser->name ?? 'N/A' }}<span>Dept. approver: {{ $proposal->departmentApprover->name ?? 'N/A' }}</span></dd></div>
            </dl>
            {{-- Panel feature: an emergency cancellation needs an answer from the
                 requestor, so it is surfaced on the list rather than living only
                 in the notification email. --}}
            @if($proposal->reservation && $proposal->reservation->awaitingRequestorChoice() && (int) $proposal->user_id === (int) auth()->id())
            <p class="rec-flag">
                <strong>Venue cancelled for an emergency.</strong>
                {{ $proposal->reservation->emergency_reason }}
                <a href="{{ route('reservations.rebook.edit', $proposal->reservation) }}">Choose a new schedule</a>
            </p>
            @elseif($proposal->reservation && $proposal->reservation->awaitingFmoRebooking() && (int) $proposal->user_id === (int) auth()->id())
            <p class="rec-flag"><strong>Replacement schedule sent.</strong> Waiting for the Facilities Office to confirm it.</p>
            @endif

            @if($proposal->status === 'rejected' && $proposal->rejection_reason)
            <p class="rec-flag"><strong>Reason:</strong> {{ $proposal->rejection_reason }}</p>
            @endif
        </div>
        <div class="rec-side">
            <span class="status {{ $proposal->status === 'approved' ? 'approved' : ($proposal->status === 'rejected' ? 'low' : 'pending') }}">{{ $proposal->statusLabel() }}</span>
            @if($proposal->reservation && $proposal->reservation->isPrePlotted() && !in_array($proposal->status, ['approved','rejected']))
                <span class="status pending"><i class="bi bi-pin-map"></i> {{ $proposal->reservation->isVenueApproved() ? 'Pre-Plotted — Venue Approved' : 'Pre-Plotted' }}</span>
            @endif
            <div class="rec-actions">
                <a class="btn-approve small-btn" href="{{ route('activity-proposals.show', $proposal) }}"><i class="bi bi-eye"></i> View</a>@if($proposal->status === 'approved' && (int)$proposal->user_id === (int)auth()->id()) <a class="btn-soft small-btn" href="{{ route('activity-proposals.print', $proposal) }}" target="_blank"><i class="bi bi-printer"></i> Print</a>@endif
            </div>
        </div>
    </article>
    @empty
    <div class="empty-state">No activity proposals found.</div>
    @endforelse
    {{ $proposals->links('vendor.pagination.custom') }}
</div>
@endsection
