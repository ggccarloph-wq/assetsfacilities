<?php
namespace App\Models;

use App\Support\FacilityRequirements;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_no','user_id','facility_id','title','purpose','resources_needed',
        'resources_details','resources_other_note',
        'start_at','end_at','status','reviewed_by','reviewed_at','rejection_reason','activity_proposal_id'
    ];

    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime', 'reviewed_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function facility() { return $this->belongsTo(Facility::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function activityProposal() { return $this->belongsTo(ActivityProposal::class); }

    public function isPrePlotted(): bool
    {
        return $this->status === 'pending';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Structured "Other Items Needed and Services" breakdown with quantities.
     * Falls back to splitting the legacy comma-separated string for records
     * created before quantities existed.
     */
    public function requirementLines(): array
    {
        $proposal = $this->activityProposal;
        if ($proposal) {
            return FacilityRequirements::decode($proposal->equipment_details, $proposal->equipment_needed);
        }

        return FacilityRequirements::decode($this->resources_details, $this->resources_needed);
    }

    public function requirementOtherNote(): ?string
    {
        $proposal = $this->activityProposal;
        if ($proposal && $proposal->equipment_other_note) {
            return $proposal->equipment_other_note;
        }

        return $this->resources_other_note;
    }

    /**
     * Full approval trail for the FMO screens: every stage of the routing, who
     * is assigned to it, who actually signed, when, and which stages are still
     * waiting. Reservations attached to an Activity Proposal expose the whole
     * six-signature chain; standalone reservations expose the short FMO review
     * trail.
     */
    public function approvalTrail(): array
    {
        $steps = [];

        $steps[] = [
            'role' => 'Submitted By — Requestor',
            'name' => $this->user->name ?? 'N/A',
            'state' => 'signed',
            'at' => $this->created_at,
            'note' => $this->user->department->name ?? null,
        ];

        $proposal = $this->activityProposal;

        if ($proposal) {
            $chain = [
                ['Prepared By — Adviser / Program Chair', $proposal->adviserSigner ?? $proposal->adviser, $proposal->adviser_signed_at, 'pending_adviser'],
                ['Noted By — Dean / Principal', $proposal->departmentSigner ?? $proposal->departmentApprover, $proposal->department_signed_at, 'pending_noted'],
                ['Noted By — SDAO', $proposal->sdaoSigner ?? $proposal->sdao, $proposal->sdao_signed_at, 'pending_noted'],
                ['Reviewed By — Facilities Management (FMO)', $proposal->fmoSigner ?? $proposal->facilitiesMgmt, $proposal->fmo_signed_at, 'pending_review'],
                ['Reviewed By — Academic Director', $proposal->academicDirectorSigner ?? $proposal->academicDirector, $proposal->academic_director_signed_at, 'pending_review'],
                ['Approved By — Executive Director', $proposal->executiveSigner ?? $proposal->executiveDirector, $proposal->executive_signed_at, 'pending_executive'],
            ];

            foreach ($chain as [$label, $person, $signedAt, $stage]) {
                if ($signedAt) {
                    $state = 'signed';
                } elseif ($proposal->status === 'rejected') {
                    $state = 'blocked';
                } elseif ($proposal->status === $stage) {
                    $state = 'waiting';
                } else {
                    $state = 'pending';
                }

                $steps[] = [
                    'role' => $label,
                    'name' => $person->name ?? 'Not yet assigned',
                    'state' => $state,
                    'at' => $signedAt,
                    'note' => null,
                ];
            }

            if ($proposal->status === 'rejected') {
                $steps[] = [
                    'role' => 'Rejected',
                    'name' => $proposal->rejecter->name ?? 'N/A',
                    'state' => 'rejected',
                    'at' => $proposal->rejected_at,
                    'note' => $proposal->rejection_reason,
                ];
            }
        }

        $steps[] = [
            'role' => 'Venue Slot — FMO Confirmation',
            'name' => $this->reviewer->name ?? 'Awaiting FMO action',
            'state' => match ($this->status) {
                'approved' => 'signed',
                'rejected' => 'rejected',
                default => 'waiting',
            },
            'at' => $this->reviewed_at,
            'note' => $this->rejection_reason,
        ];

        return $steps;
    }

    /** "4 of 8 approved" style counter used on the reservation list. */
    public function approvalProgress(): array
    {
        $trail = $this->approvalTrail();
        $done = count(array_filter($trail, fn ($step) => $step['state'] === 'signed'));

        return ['done' => $done, 'total' => count($trail)];
    }

    /** Names of everyone who has already approved / signed. */
    public function approvedByNames(): array
    {
        return array_values(array_map(
            fn ($step) => $step['name'],
            array_filter($this->approvalTrail(), fn ($step) => $step['state'] === 'signed')
        ));
    }

    /** Stages still waiting for a signature. */
    public function pendingApproverNames(): array
    {
        return array_values(array_map(
            fn ($step) => $step['name'] . ' (' . $step['role'] . ')',
            array_filter($this->approvalTrail(), fn ($step) => in_array($step['state'], ['waiting', 'pending'], true))
        ));
    }
}
