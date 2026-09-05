<?php

namespace App\Http\Controllers\Web\Fmo;

use App\Http\Controllers\Controller;
use App\Models\FacilityReservation;
use App\Notifications\FacilityReservationStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Reservation Requests screen for the FMO account: status filter, search, and
 * a full "View All Details" page that shows everything the requestor filled up
 * together with the complete approver trail.
 */
class FmoReservationController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected'];

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
            ->with(['facility', 'user.department', 'reviewer', 'activityProposal'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($search !== '', function ($q) use ($search) {
                // Searching by requestor name, activity title, reservation
                // number, purpose or venue -- all resolved in SQL.
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
            'pending' => FacilityReservation::where('status', 'pending')->count(),
            'approved' => FacilityReservation::where('status', 'approved')->count(),
            'rejected' => FacilityReservation::where('status', 'rejected')->count(),
        ];

        return view('fmo.reservations.index', compact('reservations', 'search', 'status', 'counts'));
    }

    public function show(FacilityReservation $reservation): View
    {
        $reservation->load([
            'facility', 'user.department', 'reviewer',
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

    public function approve(FacilityReservation $reservation): RedirectResponse
    {
        $conflict = FacilityReservation::where('facility_id', $reservation->facility_id)
            ->where('id', '!=', $reservation->id)
            ->where('status', 'approved')
            ->where('start_at', '<', $reservation->end_at)
            ->where('end_at', '>', $reservation->start_at)
            ->exists();

        if ($conflict) {
            return back()->withErrors(['reservation' => 'This reservation conflicts with an already approved schedule for the same venue.']);
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

        $reservation->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'] ?? 'Rejected by the Facilities Management Office',
        ]);

        $this->safeNotify($reservation->user, new FacilityReservationStatusNotification(
            $reservation->fresh(['facility', 'user']),
            'Facility Reservation Rejected',
            'Your reservation "' . $reservation->title . '" was rejected: ' . $reservation->rejection_reason
        ));

        return back()->with('success', 'Reservation rejected and the requestor has been notified.');
    }

    /**
     * Permanently removing a reservation is FMO Super Admin only (enforced
     * again by the route middleware) so historical records cannot be wiped by
     * ordinary FMO staff.
     */
    public function destroy(FacilityReservation $reservation): RedirectResponse
    {
        $reservation->delete();

        return redirect()->route('fmo.reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
