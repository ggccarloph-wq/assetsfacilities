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
    <div class="request-card">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span style="font-weight:800">{{ $proposal->proposal_no }}</span>
                    <span class="status {{ $proposal->status === 'approved' ? 'approved' : ($proposal->status === 'rejected' ? 'low' : 'pending') }}">{{ $proposal->statusLabel() }}</span>
                    @if($proposal->reservation && $proposal->reservation->isPrePlotted() && !in_array($proposal->status, ['approved','rejected']))
                        <span class="status pending"><i class="bi bi-pin-map"></i> {{ $proposal->reservation->isVenueApproved() ? 'Pre-Plotted — Venue Approved' : 'Pre-Plotted' }}</span>
                    @endif
                    <span class="tiny"><i class="bi bi-calendar-event"></i> {{ optional($proposal->start_at)->format('Y-m-d H:i') }}</span>
                </div>
                <div style="font-weight:700;margin-top:4px">{{ $proposal->title }}</div>
                <div class="tiny">Requested by: {{ $proposal->user->name ?? 'Unknown' }} · Venue: {{ $proposal->facility->name ?? 'N/A' }}</div>
                <div class="tiny-2 mt-1">Adviser: {{ $proposal->adviser->name ?? 'N/A' }} · Department Approver: {{ $proposal->departmentApprover->name ?? 'N/A' }}</div>
                @if($proposal->status === 'rejected' && $proposal->rejection_reason)
                <div class="tiny mt-2 text-danger"><strong>Reason:</strong> {{ $proposal->rejection_reason }}</div>
                @endif
            </div>
            <div class="request-actions">
                <a class="btn-approve" href="{{ route('activity-proposals.show', $proposal) }}"><i class="bi bi-eye"></i> View</a>@if($proposal->status === 'approved' && (int)$proposal->user_id === (int)auth()->id()) <a class="btn-soft" href="{{ route('activity-proposals.print', $proposal) }}"><i class="bi bi-printer"></i> Print</a>@endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">No activity proposals found.</div>
    @endforelse
    {{ $proposals->links('vendor.pagination.custom') }}
</div>
@endsection
