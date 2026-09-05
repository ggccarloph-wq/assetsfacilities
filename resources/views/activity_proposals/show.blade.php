@extends('layouts.admin', ['title' => 'Activity Proposal Details'])
@section('content')
<div class="panel-grid-2">
    <div class="surface p-3">
        <div class="module-head mb-2">
            <div>
                <h2 class="module-title" style="font-size:18px">{{ $proposal->proposal_no }}</h2>
                <div class="module-note">{{ $proposal->title }}</div>
            </div>
            <span class="status {{ $proposal->status === 'approved' ? 'approved' : ($proposal->status === 'rejected' ? 'low' : 'pending') }}">{{ $proposal->statusLabel() }}</span>
        </div>

        @if(auth()->user()->canDeleteFacilityRecords())
        <form method="POST" action="{{ route('activity-proposals.destroy', $proposal) }}" class="mb-2" onsubmit="return confirm('Delete this activity proposal permanently? This cannot be undone.');">
            @csrf @method('DELETE')
            <button class="btn-soft small-btn text-danger"><i class="bi bi-trash"></i> Delete Proposal (Super Admin)</button>
        </form>
        @endif

        <table class="kv-table">
            <tr><th><i class="bi bi-building me-1"></i>Organization</th><td>{{ $proposal->organization_name }}</td></tr>
            <tr><th><i class="bi bi-person me-1"></i>Requested By</th><td>{{ $proposal->user->name ?? 'N/A' }} @if($proposal->requester_position)({{ $proposal->requester_position }})@endif</td></tr>
            <tr><th><i class="bi bi-geo-alt me-1"></i>Venue</th><td>{{ $proposal->facility->name ?? 'N/A' }} — {{ $proposal->facility->location ?? '' }} @if($proposal->venue_other_note)<span class="tiny-2">({{ $proposal->venue_other_note }})</span>@endif</td></tr>
            <tr><th><i class="bi bi-clock me-1"></i>Schedule</th><td>{{ optional($proposal->start_at)->format('Y-m-d H:i') }} – {{ optional($proposal->end_at)->format('Y-m-d H:i') }}</td></tr>
            <tr><th><i class="bi bi-calendar3 me-1"></i>Day(s) of Activity</th><td>{{ $proposal->activity_days ?: 'N/A' }}</td></tr>
            <tr><th><i class="bi bi-people me-1"></i>Expected Attendees</th><td>{{ $proposal->participants_count }}</td></tr>
            @if($proposal->speaker_name)
            <tr><th><i class="bi bi-mic me-1"></i>Speaker</th><td>{{ $proposal->speaker_name }}</td></tr>
            @endif
            <tr>
                <th><i class="bi bi-door-open me-1"></i>Venue Slot</th>
                <td>
                    <span class="status {{ $proposal->reservation && $proposal->reservation->status === 'confirmed' ? 'approved' : 'pending' }}">{{ ucfirst($proposal->reservation->status ?? 'N/A') }}</span>
                    @if($proposal->reservation && $proposal->reservation->isPrePlotted())<span class="tiny text-muted ms-1">(pre-plotted — not yet confirmed)</span>@endif
                </td>
            </tr>
            <tr>
                <th><i class="bi bi-box-seam me-1"></i>Items &amp; Services Needed</th>
                <td class="kv-wide">
                    @php $reqLines = $proposal->requirementLines(); @endphp
                    @forelse($reqLines as $line)
                        <span class="tag">{{ $line['name'] ?? '' }}@if(!empty($line['quantity'])) &times; {{ $line['quantity'] }}@endif</span>
                    @empty
                        None specified
                    @endforelse
                    @if($proposal->equipment_other_note)
                        <div class="tiny mt-2"><strong>Others:</strong> {{ $proposal->equipment_other_note }}</div>
                    @endif
                </td>
            </tr>
            <tr><th><i class="bi bi-list-check me-1"></i>Program Flow</th><td class="kv-wide">{!! nl2br(e($proposal->program_flow)) !!}</td></tr>
        </table>

        <div class="module-note mb-2" style="font-weight:700;color:var(--ink-900);font-size:12.5px">Approval Trail</div>
        <ul class="approval-timeline">
            <li class="{{ $proposal->adviser_signed_at ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $proposal->adviser_signed_at ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">Prepared By — Adviser / Program Chair</div>
                        <div class="step-name">{{ $proposal->adviserSigner->name ?? ($proposal->adviser->name ?? 'Assigned') }}</div>
                    </div>
                    <span class="step-meta {{ $proposal->adviser_signed_at ? 'signed' : 'pending' }}">{{ $proposal->adviser_signed_at ? 'Signed '.$proposal->adviser_signed_at->format('Y-m-d H:i') : 'Pending' }}</span>
                </div>
            </li>
            <li class="{{ $proposal->department_signed_at ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $proposal->department_signed_at ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">Noted By — Dean / Principal</div>
                        <div class="step-name">{{ $proposal->departmentSigner->name ?? ($proposal->departmentApprover->name ?? 'Assigned') }}</div>
                    </div>
                    <span class="step-meta {{ $proposal->department_signed_at ? 'signed' : 'pending' }}">{{ $proposal->department_signed_at ? 'Signed '.$proposal->department_signed_at->format('Y-m-d H:i') : 'Pending' }}</span>
                </div>
            </li>
            <li class="{{ $proposal->sdao_signed_at ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $proposal->sdao_signed_at ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">Noted By — SDAO</div>
                        <div class="step-name">{{ $proposal->sdaoSigner->name ?? ($proposal->sdao->name ?? 'Assigned') }}</div>
                    </div>
                    <span class="step-meta {{ $proposal->sdao_signed_at ? 'signed' : 'pending' }}">{{ $proposal->sdao_signed_at ? 'Signed '.$proposal->sdao_signed_at->format('Y-m-d H:i') : 'Pending' }}</span>
                </div>
            </li>
            <li class="{{ $proposal->fmo_signed_at ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $proposal->fmo_signed_at ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">Reviewed By — Facilities Management</div>
                        <div class="step-name">{{ $proposal->fmoSigner->name ?? ($proposal->facilitiesMgmt->name ?? 'Assigned') }}</div>
                    </div>
                    <span class="step-meta {{ $proposal->fmo_signed_at ? 'signed' : 'pending' }}">{{ $proposal->fmo_signed_at ? 'Signed '.$proposal->fmo_signed_at->format('Y-m-d H:i') : 'Pending' }}</span>
                </div>
            </li>
            <li class="{{ $proposal->academic_director_signed_at ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $proposal->academic_director_signed_at ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">Reviewed By — Academic Director</div>
                        <div class="step-name">{{ $proposal->academicDirectorSigner->name ?? ($proposal->academicDirector->name ?? 'Assigned') }}</div>
                    </div>
                    <span class="step-meta {{ $proposal->academic_director_signed_at ? 'signed' : 'pending' }}">{{ $proposal->academic_director_signed_at ? 'Signed '.$proposal->academic_director_signed_at->format('Y-m-d H:i') : 'Pending' }}</span>
                </div>
            </li>
            <li class="{{ $proposal->executive_signed_at ? 'signed' : 'pending' }}">
                <div class="step-dot"><i class="bi {{ $proposal->executive_signed_at ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
                <div class="step-row">
                    <div>
                        <div class="step-role">Approved By — Executive Director</div>
                        <div class="step-name">{{ $proposal->executiveSigner->name ?? ($proposal->executiveDirector->name ?? 'Assigned') }}</div>
                    </div>
                    <span class="step-meta {{ $proposal->executive_signed_at ? 'signed' : 'pending' }}">{{ $proposal->executive_signed_at ? 'Signed '.$proposal->executive_signed_at->format('Y-m-d H:i') : 'Pending' }}</span>
                </div>
            </li>
        </ul>
        <div class="note-callout"><i class="bi bi-shield-check"></i><div>Digital signature = the approver's authenticated account confirming approval; no wet ink or physical routing required.</div></div>
    </div>

    <div class="surface p-3">
        <h3 class="module-title" style="font-size:16px">Actions</h3>

        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

        @if($proposal->status === 'rejected')
            <div class="alert alert-danger">Rejected at "{{ $proposal->statusLabel() }}" stage by {{ $proposal->rejecter->name ?? 'N/A' }}: {{ $proposal->rejection_reason }}</div>
        @endif

        @php $user = auth()->user(); @endphp

        @if($proposal->isAwaitingAdviser() && ($user->isFmoSuperAdmin() || $user->id === $proposal->adviser_id))
            <form method="POST" action="{{ route('activity-proposals.approve-adviser', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Adviser/Program Chair</button>
            </form>
        @endif

        @if($proposal->isAwaitingNoted())
            @if(!$proposal->department_signed_at && ($user->isFmoSuperAdmin() || $user->id === $proposal->department_approver_id))
            <form method="POST" action="{{ route('activity-proposals.sign-dean', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Dean/Principal</button>
            </form>
            @endif
            @if(!$proposal->sdao_signed_at && ($user->isFmoSuperAdmin() || $user->id === $proposal->sdao_id))
            <form method="POST" action="{{ route('activity-proposals.sign-sdao', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as SDAO</button>
            </form>
            @endif
            <div class="tiny text-muted mb-2">Both Dean/Principal and SDAO must sign before this moves to the Reviewed By stage.</div>
        @endif

        @if($proposal->isAwaitingReview())
            @if(!$proposal->fmo_signed_at && $user->isFmoSide())
            <form method="POST" action="{{ route('activity-proposals.sign-facilities', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Facilities Management</button>
            </form>
            @endif
            @if(!$proposal->academic_director_signed_at && ($user->isFmoSuperAdmin() || $user->id === $proposal->academic_director_id))
            <form method="POST" action="{{ route('activity-proposals.sign-academic-director', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-pen"></i> Sign as Academic Director</button>
            </form>
            @endif
            <div class="tiny text-muted mb-2">Both Facilities Management and Academic Director must sign before this moves to Executive Director for final approval.</div>
        @endif

        @if($proposal->isAwaitingExecutive() && ($user->isFmoSuperAdmin() || $user->id === $proposal->executive_director_id))
            <form method="POST" action="{{ route('activity-proposals.approve-executive', $proposal) }}" class="mb-3">@csrf
                <button class="btn-approve w-100 justify-content-center"><i class="bi bi-check-lg"></i> Final Approve & Confirm Venue</button>
            </form>
        @endif

        @if(!in_array($proposal->status, ['approved','rejected']))
            @php
                $canReject = $user->isFmoSuperAdmin()
                    || ($proposal->isAwaitingAdviser() && $user->id === $proposal->adviser_id)
                    || ($proposal->isAwaitingNoted() && in_array($user->id, [$proposal->department_approver_id, $proposal->sdao_id]))
                    || ($proposal->isAwaitingReview() && ($user->isFmoSide() || $user->id === $proposal->academic_director_id))
                    || ($proposal->isAwaitingExecutive() && $user->id === $proposal->executive_director_id);
            @endphp
            @if($canReject)
            <details class="reject-disclosure">
                <summary><i class="bi bi-x-circle"></i> Reject this proposal instead</summary>
                <div class="reject-disclosure-body">
                    <form method="POST" action="{{ route('activity-proposals.reject', $proposal) }}">@csrf
                        <label class="form-label">Rejection Reason</label>
                        <textarea class="form-control mb-2" name="rejection_reason" required></textarea>
                        <div class="field-hint mb-2">This will be shown to the requestor, so be specific about what needs to change.</div>
                        <button class="btn-reject w-100 justify-content-center"><i class="bi bi-x-lg"></i> Confirm Rejection</button>
                    </form>
                </div>
            </details>
            @endif
        @endif

        @if($proposal->status === 'approved')
            <div class="alert alert-success">Fully approved. Venue is confirmed — no other reservation can be approved for this facility during this time.</div>
        @endif

        <a class="btn-soft w-100 justify-content-center mt-2" href="{{ route('activity-proposals.index') }}"><i class="bi bi-arrow-left"></i> Back to List</a>
    </div>
</div>
@endsection
