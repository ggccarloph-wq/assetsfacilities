<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityItem;
use App\Models\FacilityReservation;
use App\Notifications\FacilityReservationStatusNotification;
use App\Support\FacilityRequirements;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Reservation submission side of Facilities.
 *
 * Venue / item / service maintenance and the FMO reservation queue now live in
 * App\Http\Controllers\Web\Fmo\* so they can be locked behind the FMO
 * middleware. What is left here is the part every signed-in requestor uses:
 * filling in and submitting a reservation, plus the approve/reject actions
 * (still routed, still FMO-gated, so any existing bookmark keeps working).
 */
class FacilityController extends Controller
{
    /**
     * Notifications are a side effect, not the reservation record itself -- if
     * mail fails the reservation must still be considered saved, so every
     * notify() call is isolated in its own try/catch.
     */
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

    /**
     * Legacy /facilities entry point. Facilities staff land on the new FMO
     * Reservation Requests screen; everybody else goes to their own home page
     * rather than hitting a 403 from an old notification link.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->canManageFacilities()) {
            return redirect()->route('fmo.reservations.index');
        }

        return redirect()->route($user->homeRouteName());
    }

    public function createReservation()
    {
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $catalogItems = FacilityItem::active()->items()->ordered()->get();
        $catalogServices = FacilityItem::active()->services()->ordered()->get();

        return view('facilities.reserve', compact('facilities', 'catalogItems', 'catalogServices'));
    }

    public function storeReservation(Request $request)
    {
        $data = $request->validate([
            'facility_id' => ['required','exists:facilities,id'],
            'title' => ['required','string','max:150'],
            'purpose' => ['nullable','string'],
            'start_at' => ['required','date'],
            'end_at' => ['required','date','after:start_at'],
            'requirements' => ['nullable','array'],
            'requirements_other_note' => ['nullable','string','max:1000'],
        ]);

        $requirements = FacilityRequirements::fromRequest($request);

        $conflict = FacilityReservation::where('facility_id', $data['facility_id'])
            ->whereIn('status', ['pending','approved'])
            ->where('start_at', '<', $data['end_at'])
            ->where('end_at', '>', $data['start_at'])
            ->exists();

        if ($conflict) {
            return back()->withErrors(['start_at' => 'Schedule conflict detected. This facility already has a pending or approved reservation in that time range.'])->withInput();
        }

        unset($data['requirements'], $data['requirements_other_note']);

        $data['resources_needed'] = $requirements['summary'];
        $data['resources_details'] = json_encode($requirements['lines']);
        $data['resources_other_note'] = $requirements['other_note'];
        $data['reservation_no'] = 'FR-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        $data['user_id'] = auth()->id();
        $data['status'] = auth()->user()->canManageFacilities() ? 'approved' : 'pending';
        if ($data['status'] === 'approved') {
            $data['reviewed_by'] = auth()->id();
            $data['reviewed_at'] = now();
        }
        $reservation = FacilityReservation::create($data);

        // If it still needs review, notify FMO Super Admin / FMO staff (in-system +
        // registered email) that a new reservation is awaiting their approval.
        if ($reservation->status === 'pending') {
            foreach (\App\Models\User::query()->whereIn('role', \App\Models\User::FMO_ROLES)->get() as $manager) {
                $this->safeNotify($manager, new FacilityReservationStatusNotification(
                    $reservation->fresh(['facility', 'user']),
                    'New Facility Reservation Awaiting Approval',
                    'A new reservation for "' . $reservation->title . '" was submitted and is now waiting for your review.'
                ));
            }
        }

        $target = auth()->user()->canManageFacilities()
            ? 'fmo.reservations.index'
            : auth()->user()->homeRouteName();

        return redirect()->route($target)->with('success', 'Reservation submitted successfully.');
    }

    public function approve(FacilityReservation $reservation)
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        $conflict = FacilityReservation::where('facility_id', $reservation->facility_id)
            ->where('id', '!=', $reservation->id)
            ->where('status', 'approved')
            ->where('start_at', '<', $reservation->end_at)
            ->where('end_at', '>', $reservation->start_at)
            ->exists();
        if ($conflict) {
            return back()->withErrors(['reservation' => 'This reservation conflicts with an already approved schedule.']);
        }
        $reservation->update(['status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'rejection_reason' => null]);

        $this->safeNotify($reservation->user, new FacilityReservationStatusNotification(
            $reservation->fresh(['facility', 'user']),
            'Facility Reservation Approved',
            'Your reservation "' . $reservation->title . '" has been approved.'
        ));

        return back()->with('success', 'Reservation approved.');
    }

    public function reject(Request $request, FacilityReservation $reservation)
    {
        abort_unless(auth()->user()->canManageFacilities(), 403);
        $data = $request->validate(['rejection_reason' => ['nullable','string','max:500']]);
        $reservation->update(['status' => 'rejected', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'rejection_reason' => $data['rejection_reason'] ?? 'Rejected by FMO']);

        $this->safeNotify($reservation->user, new FacilityReservationStatusNotification(
            $reservation->fresh(['facility', 'user']),
            'Facility Reservation Rejected',
            'Your reservation "' . $reservation->title . '" was rejected: ' . $reservation->rejection_reason
        ));

        return back()->with('success', 'Reservation rejected.');
    }

    /**
     * Only the FMO Super Admin may delete a facility reservation entirely --
     * this used to be the Asset Management Super Admin, which no longer has
     * any Facilities access at all.
     */
    public function destroyReservation(FacilityReservation $reservation)
    {
        abort_unless(auth()->user()->canDeleteFacilityRecords(), 403);
        $reservation->delete();
        return redirect()->route('fmo.reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
