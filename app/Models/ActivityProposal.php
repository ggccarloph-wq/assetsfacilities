<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_no', 'user_id', 'organization_name', 'requester_position', 'department_id',
        'adviser_id', 'department_approver_id', 'sdao_id', 'facilities_mgmt_id', 'academic_director_id', 'executive_director_id',
        'title', 'activity_days', 'program_flow', 'speaker_name', 'participants_count', 'equipment_needed',
        'equipment_details', 'equipment_other_note',
        'facility_id', 'venue_other_note', 'start_at', 'end_at', 'facility_reservation_id',
        'status',
        'adviser_signed_by', 'adviser_signed_at',
        'department_signed_by', 'department_signed_at',
        'sdao_signed_by', 'sdao_signed_at',
        'fmo_signed_by', 'fmo_signed_at',
        'academic_director_signed_by', 'academic_director_signed_at',
        'executive_signed_by', 'executive_signed_at',
        'rejected_stage', 'rejected_by', 'rejected_at', 'rejection_reason',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'adviser_signed_at' => 'datetime',
        'department_signed_at' => 'datetime',
        'sdao_signed_at' => 'datetime',
        'fmo_signed_at' => 'datetime',
        'academic_director_signed_at' => 'datetime',
        'executive_signed_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function adviser() { return $this->belongsTo(User::class, 'adviser_id'); }
    public function departmentApprover() { return $this->belongsTo(User::class, 'department_approver_id'); }
    public function sdao() { return $this->belongsTo(User::class, 'sdao_id'); }
    public function facilitiesMgmt() { return $this->belongsTo(User::class, 'facilities_mgmt_id'); }
    public function academicDirector() { return $this->belongsTo(User::class, 'academic_director_id'); }
    public function executiveDirector() { return $this->belongsTo(User::class, 'executive_director_id'); }
    public function facility() { return $this->belongsTo(Facility::class); }
    public function reservation() { return $this->belongsTo(FacilityReservation::class, 'facility_reservation_id'); }

    public function adviserSigner() { return $this->belongsTo(User::class, 'adviser_signed_by'); }
    public function departmentSigner() { return $this->belongsTo(User::class, 'department_signed_by'); }
    public function sdaoSigner() { return $this->belongsTo(User::class, 'sdao_signed_by'); }
    public function fmoSigner() { return $this->belongsTo(User::class, 'fmo_signed_by'); }
    public function academicDirectorSigner() { return $this->belongsTo(User::class, 'academic_director_signed_by'); }
    public function executiveSigner() { return $this->belongsTo(User::class, 'executive_signed_by'); }
    public function rejecter() { return $this->belongsTo(User::class, 'rejected_by'); }

    public function equipmentList(): array
    {
        return $this->equipment_needed ? array_filter(array_map('trim', explode(',', $this->equipment_needed))) : [];
    }

    /**
     * Structured item/service breakdown with quantities. Older proposals have
     * no JSON payload, so the legacy comma-separated string is split instead
     * and rendered without quantities -- nothing is lost.
     */
    public function requirementLines(): array
    {
        return \App\Support\FacilityRequirements::decode($this->equipment_details, $this->equipment_needed);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending_adviser' => 'Pending - Adviser / Program Chair Signature',
            'pending_noted' => 'Pending - Noted By (Dean/Principal & SDAO)',
            'pending_review' => 'Pending - Reviewed By (Facilities Mgmt. & Academic Director)',
            'pending_executive' => 'Pending - Approved By (Executive Director)',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function isAwaitingAdviser(): bool { return $this->status === 'pending_adviser'; }
    public function isAwaitingNoted(): bool { return $this->status === 'pending_noted'; }
    public function isAwaitingReview(): bool { return $this->status === 'pending_review'; }
    public function isAwaitingExecutive(): bool { return $this->status === 'pending_executive'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
