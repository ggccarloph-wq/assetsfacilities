<?php

namespace App\Http\Controllers\Web\Fmo;

use App\Http\Controllers\Controller;
use App\Models\ActivityProposal;
use App\Models\FacilityReservation;
use App\Notifications\ActivityProposalStatusNotification;
use App\Notifications\FacilityReservationStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Reservation Requests screen for the FMO account: status filter, search, and
 * a full "View All Details" page that shows everything the requestor filled up
 * together with the complete approver trail.
 */
class FmoReservationController extends Controller
{
    private const STATUSES = ['pending', 'pre_plotted', 'approved', 'rejected'];

    private function safeNotify($notifiable, $notification): void
    {
        if (!$notifiable) {
            return;
        }
        try {
            $notifiable->notify($notification);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');
        if (!in_array($status, self::STATUSES, true)) {
            $status = '';
        }

        $reservations = FacilityReservation::query()
            ->with(['facility', 'user.department', 'reviewer', 'venueReviewer', 'activityProposal'])
            ->when($status === 'pending', fn ($q) => $q->where('status', 'pending')->where('is_pre_plotted', false))
            ->when($status === 'pre_plotted', fn ($q) => $q->where('status', 'pending')->where('is_pre_plotted', true))
            ->when(in_array($status, ['approved', 'rejected'], true), fn ($q) => $q->where('status', $status))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('reservation_no', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('facility', fn ($f) => $f->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
                });
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('start_at')
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'all' => FacilityReservation::count(),
            'pending' => FacilityReservation::where('status', 'pending')->where('is_pre_plotted', false)->count(),
            'pre_plotted' => FacilityReservation::where('status', 'pending')->where('is_pre_plotted', true)->count(),
            'approved' => FacilityReservation::where('status', 'approved')->count(),
            'rejected' => FacilityReservation::where('status', 'rejected')->count(),
        ];

        return view('fmo.reservations.index', compact('reservations', 'search', 'status', 'counts'));
    }

    public function show(FacilityReservation $reservation): View
    {
        $reservation->load([
            'facility', 'user.department', 'reviewer', 'venueReviewer',
            'activityProposal.user', 'activityProposal.department', 'activityProposal.facility',
            'activityProposal.adviser', 'activityProposal.departmentApprover', 'activityProposal.sdao',
            'activityProposal.facilitiesMgmt', 'activityProposal.academicDirector', 'activityProposal.executiveDirector',
            'activityProposal.adviserSigner', 'activityProposal.departmentSigner', 'activityProposal.sdaoSigner',
            'activityProposal.fmoSigner', 'activityProposal.academicDirectorSigner', 'activityProposal.executiveSigner',
            'activityProposal.rejecter',
        ]);

        return view('fmo.reservations.show', [
            'reservation' => $reservation,
            'proposal' => $reservation->activityProposal,
            'trail' => $reservation->approvalTrail(),
            'requirements' => $reservation->requirementLines(),
            'otherNote' => $reservation->requirementOtherNote(),
        ]);
    }

    /**
     * Venue decision for a pre-plotted request. This is deliberately separate
     * from the proposal's FMO "Approve Request" signature.
     */
    public function approveVenue(FacilityReservation $reservation): RedirectResponse
    {
        abort_unless($reservation->isPrePlotted(), 422, 'Only a pre-plotted reservation needs a separate venue confirmation.');
        abort_if($reservation->isRejected(), 422, 'A rejected request cannot have its venue approved.');

        if ($reservation->isVenueApproved()) {
            return back()->with('success', 'This pre-plotted venue is already approved.');
        }

        if ($reservation->committedCompetingReservations()->exists()) {
            return back()->withErrors([
                'reservation' => 'A same-date request already has this venue confirmed or has already passed FMO request approval. Reject or resolve that committed request first before assigning this pre-plotted venue.'
            ]);
        }

        $reservation->update([
            'venue_status' => 'approved',
            'venue_reviewed_by' => auth()->id(),
            'venue_reviewed_at' => now(),
            'venue_rejection_reason' => null,
        ]);

        $proposal = $reservation->activityProposal;
        if ($proposal && $proposal->status === 'pending_fmo' && $proposal->fmo_signed_at) {
            $proposal->update(['status' => 'pending_adviser']);
            $this->safeNotify($proposal->adviser, new ActivityProposalStatusNotification(
                $proposal,
                'Activity Proposal Awaiting Adviser Signature',
                'Facilities Management completed both the request review and pre-plotted venue confirmation for "' . $proposal->title . '". It now needs your Adviser / Program Chair signature.'
            ));
        }

        $this->safeNotify($reservation->user, new FacilityReservationStatusNotification(
            $reservation->fresh(['facility', 'user']),
            'Pre-Plotted Venue Approved',
            'Facilities Management approved the pre-plotted venue slot for "' . $reservation->title . '". The request will continue once all required approval steps are complete.'
        ));

        $message = $proposal && $proposal->fmo_signed_at
            ? 'Venue approved. The FMO request approval was already complete, so the proposal is now routed to the Adviser / Program Chair.'
            : 'Venue approved. Approve Request is still required before the proposal can proceed to the next approver.';

        return back()->with('success', $message);
    }

    /** Standalone reservation approval. Activity Proposals use Approve Request. */
    public function approve(FacilityReservation $reservation): RedirectResponse
    {
        if ($reservation->activityProposal) {
            return back()->withErrors([
                'reservation' => 'This reservation belongs to an Activity Proposal. Use Approve Request for the FMO approval trail instead of approving the reservation directly.'
            ]);
        }

        if ($reservation->isPrePlotted() && !$reservation->isVenueApproved()) {
            return back()->withErrors(['reservation' => 'Approve Venue first for this pre-plotted standalone reservation.']);
        }

        if ($reservation->confirmedCompetingReservations()->exists()) {
            return back()->withErrors(['reservation' => 'This reservation conflicts with an already confirmed schedule for the same venue.']);
        }

        $reservation->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->safeNotify($reservation->user, new FacilityReservationStatusNotification(
            $reservation->fresh(['facility', 'user']),
            'Facility Reservation Approved',
            'Your reservation "' . $reservation->title . '" has been approved by the Facilities Management Office.'
        ));

        return back()->with('success', 'Reservation approved.');
    }

    public function reject(Request $request, FacilityReservation $reservation): RedirectResponse
    {
        $data = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:500']]);
        $reason = $data['rejection_reason'] ?? 'Rejected by the Facilities Management Office';
        $proposal = $reservation->activityProposal;

        if ($proposal && !in_array($proposal->status, ['approved', 'rejected'], true)) {
            $user = auth()->user();
            $canRejectProposal = $user->isFmoSuperAdmin()
                || ($proposal->isAwaitingFmo() && $user->isFmoSide() && ($proposal->facilities_mgmt_id === null || (int) $user->id === (int) $proposal->facilities_mgmt_id));
            abort_unless($canRejectProposal, 403, 'Only an available/assigned FMO reviewer or FMO Super Admin can reject this proposal at the current stage.');

            if ($proposal->isAwaitingFmo() && $proposal->facilities_mgmt_id === null && $user->isFmoSide()) {
                $proposal->update(['facilities_mgmt_id' => $user->id]);
            }

            $proposal->update([
                'status' => 'rejected',
                'rejected_stage' => $proposal->status,
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);
        }

        $reservation->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        if ($proposal) {
            $this->safeNotify($proposal->user, new ActivityProposalStatusNotification(
                $proposal,
                'Activity Proposal Rejected',
                'Your activity proposal "' . $proposal->title . '" was rejected: ' . $reason
            ));
        } else {
            $this->safeNotify($reservation->user, new FacilityReservationStatusNotification(
                $reservation->fresh(['facility', 'user']),
                'Facility Reservation Rejected',
                'Your reservation "' . $reservation->title . '" was rejected: ' . $reason
            ));
        }

        return back()->with('success', 'Request rejected and the requestor has been notified.');
    }

    /**
     * FMO Super Admin deletion is synchronized: the reservation and its linked
     * Activity Proposal are removed in one transaction, so no requestor account
     * can keep a ghost copy after deletion.
     */
    public function destroy(FacilityReservation $reservation): RedirectResponse
    {
        $proposal = $reservation->activityProposal
            ?: ActivityProposal::where('facility_reservation_id', $reservation->id)->first();
        $attachmentPath = $proposal?->program_flow_path;

        DB::transaction(function () use ($reservation, $proposal) {
            if ($proposal) {
                $proposal->delete();
            }
            $reservation->delete();
        });

        if ($attachmentPath) {
            Storage::disk('local')->delete($attachmentPath);
        }

        return redirect()->route('fmo.reservations.index')->with('success', 'Reservation and any linked activity proposal were deleted everywhere.');
    }
}
