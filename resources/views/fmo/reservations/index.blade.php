@extends('layouts.admin', ['title' => 'Reservation Requests', 'subtitle' => 'Filter by status, search by requestor or title, and open the full approval trail.'])

@section('content')

<div class="surface p-3 mb-3">
    {{-- Status filter. Filtering happens in the SQL query, not in Blade. --}}
    <div class="chip-row">
        <a class="chip {{ $status === '' ? 'active' : '' }}" href="{{ route('fmo.reservations.index', array_filter(['search' => $search])) }}">
            <i class="bi bi-collection"></i> All <span class="chip-count">{{ $counts['all'] }}</span>
        </a>
        <a class="chip {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('fmo.reservations.index', array_filter(['status' => 'pending', 'search' => $search])) }}">
            <i class="bi bi-hourglass-split"></i> Pending <span class="chip-count">{{ $counts['pending'] }}</span>
        </a>
        <a class="chip {{ $status === 'pre_plotted' ? 'active' : '' }}" href="{{ route('fmo.reservations.index', array_filter(['status' => 'pre_plotted', 'search' => $search])) }}">
            <i class="bi bi-pin-map"></i> Pre-Plotted <span class="chip-count">{{ $counts['pre_plotted'] }}</span>
        </a>
        <a class="chip {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('fmo.reservations.index', array_filter(['status' => 'approved', 'search' => $search])) }}">
            <i class="bi bi-check2-circle"></i> Approved <span class="chip-count">{{ $counts['approved'] }}</span>
        </a>
        <a class="chip {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('fmo.reservations.index', array_filter(['status' => 'rejected', 'search' => $search])) }}">
            <i class="bi bi-x-circle"></i> Rejected <span class="chip-count">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    {{-- Search bar: requestor name, activity title, reservation no., venue. --}}
    <form method="GET" class="search-strip mb-0">
        <input type="hidden" name="status" value="{{ $status }}">
        <i class="bi bi-search"></i>
        <input class="search-input" name="search" value="{{ $search }}" placeholder="Search requestor name, activity title, reservation no., or venue...">
        <button class="btn-primaryx" type="submit"><i class="bi bi-funnel"></i> Search</button>
        @if($search !== '')
            <a class="btn-soft small-btn" href="{{ route('fmo.reservations.index', array_filter(['status' => $status])) }}">Clear</a>
        @endif
    </form>
</div>

<div class="surface p-3">
    <div class="table-responsive">
        <div class="rec-list">
            @forelse($reservations as $reservation)
                @php $progress = $reservation->approvalProgress(); @endphp
                <article class="rec rec--plain">
                    <div class="rec-body">
                        <div class="rec-kicker">{{ $reservation->reservation_no }} &middot; {{ $reservation->facility->name ?? 'No venue' }}</div>
                        <h3 class="rec-heading"><a href="{{ route('fmo.reservations.show', $reservation) }}">{{ $reservation->title }}</a></h3>
                        @if($reservation->activityProposal)
                            <p class="rec-desc"><i class="bi bi-file-earmark-check"></i> Linked proposal {{ $reservation->activityProposal->proposal_no }}</p>
                        @endif
                        <dl class="rec-facts">
                            <div><dt>Requestor</dt><dd>{{ $reservation->user->name ?? 'N/A' }}<span>{{ $reservation->user->department->name ?? 'No department' }}</span></dd></div>
                            <div><dt>Schedule</dt><dd>{{ optional($reservation->start_at)->format('M d, Y h:i A') }}<span>to {{ optional($reservation->end_at)->format('M d, Y h:i A') }}</span></dd></div>
                            <div><dt>Approval trail</dt><dd>{{ $progress['done'] }} of {{ $progress['total'] }} approved
                                <div class="stock-bar" style="width:100%">
                                    <div class="stock-fill {{ $progress['done'] < $progress['total'] ? 'low' : '' }}" style="width: {{ $progress['total'] ? round($progress['done'] / $progress['total'] * 100) : 0 }}%"></div>
                                </div>
                            </dd></div>
                        </dl>
                    </div>
                    <div class="rec-side">
                        <span class="status {{ $reservation->status === 'approved' ? 'approved' : ($reservation->status === 'rejected' ? 'low' : 'pending') }}">{{ $reservation->displayStatus() }}</span>
                        <div class="rec-actions">
                            <a class="btn-primaryx small-btn" href="{{ route('fmo.reservations.show', $reservation) }}"><i class="bi bi-eye"></i> View all details</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-state">No reservation requests match this filter.</div>
            @endforelse
        </div>
    </div>
    <div class="mt-3">{{ $reservations->links('vendor.pagination.custom') }}</div>
</div>
@endsection
