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
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reservation No.</th>
                    <th>Requestor</th>
                    <th>Activity Title</th>
                    <th>Venue</th>
                    <th>Schedule</th>
                    <th>Approval Trail</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($reservations as $reservation)
                @php $progress = $reservation->approvalProgress(); @endphp
                <tr>
                    <td data-label="Reservation No."><span class="code-badge">{{ $reservation->reservation_no }}</span></td>
                    <td data-label="Requestor">
                        <div style="font-weight:700">{{ $reservation->user->name ?? 'N/A' }}</div>
                        <div class="tiny">{{ $reservation->user->department->name ?? 'No department' }}</div>
                    </td>
                    <td data-label="Activity Title">
                        {{ $reservation->title }}
                        @if($reservation->activityProposal)
                            <div class="tiny-2"><i class="bi bi-file-earmark-check"></i> {{ $reservation->activityProposal->proposal_no }}</div>
                        @endif
                    </td>
                    <td data-label="Venue">{{ $reservation->facility->name ?? 'N/A' }}</td>
                    <td data-label="Schedule">
                        {{ optional($reservation->start_at)->format('M d, Y h:i A') }}
                        <div class="tiny">to {{ optional($reservation->end_at)->format('M d, Y h:i A') }}</div>
                    </td>
                    <td data-label="Approval Trail">
                        <span class="tag">{{ $progress['done'] }} of {{ $progress['total'] }} approved</span>
                        <div class="stock-bar" style="width:100%">
                            <div class="stock-fill {{ $progress['done'] < $progress['total'] ? 'low' : '' }}" style="width: {{ $progress['total'] ? round($progress['done'] / $progress['total'] * 100) : 0 }}%"></div>
                        </div>
                    </td>
                    <td data-label="Status">
                        <span class="status {{ $reservation->status === 'approved' ? 'approved' : ($reservation->status === 'rejected' ? 'low' : 'pending') }}">{{ ucfirst($reservation->status) }}</span>
                    </td>
                    <td data-label="Actions">
                        <a class="btn-primaryx small-btn" href="{{ route('fmo.reservations.show', $reservation) }}"><i class="bi bi-eye"></i> View All Details</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty-state">No reservation requests match this filter.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $reservations->links('vendor.pagination.custom') }}</div>
</div>
@endsection
