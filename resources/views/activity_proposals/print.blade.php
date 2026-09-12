@extends('layouts.admin', ['title' => 'Print Approved Proposal'])
@section('content')
@php
$signatures = [
 ['Facilities Management', $proposal->fmoSigner ?? $proposal->facilitiesMgmt, $proposal->fmo_signed_at],
 ['Adviser / Program Chair', $proposal->adviserSigner ?? $proposal->adviser, $proposal->adviser_signed_at],
 ['Dean / Principal', $proposal->departmentSigner ?? $proposal->departmentApprover, $proposal->department_signed_at],
 ['SDAO', $proposal->sdaoSigner ?? $proposal->sdao, $proposal->sdao_signed_at],
 ['Academic Director', $proposal->academicDirectorSigner ?? $proposal->academicDirector, $proposal->academic_director_signed_at],
 ['Executive Director', $proposal->executiveSigner ?? $proposal->executiveDirector, $proposal->executive_signed_at],
];
@endphp
<div class="module-head no-print"><div><h2 class="module-title">Approved Activity Proposal</h2><div class="module-note">All required approvals are complete.</div></div><div class="d-flex gap-2"><button class="btn-primaryx" onclick="window.print()"><i class="bi bi-printer"></i> Print Proposal</button><a class="btn-soft" href="{{ route('activity-proposals.show',$proposal) }}">Back</a></div></div>
<div id="proposal-print" class="surface p-4 proposal-print">
    <div class="text-center mb-4"><div class="tiny-2">NATIONAL UNIVERSITY — CLARK</div><h2 style="font-size:22px;margin:4px 0">School Facilities Reservation — Approved Digital Proposal</h2><div>Proposal No. <strong>{{ $proposal->proposal_no }}</strong></div></div>
    <table class="print-kv">
      <tr><th>Organization / Department / College</th><td>{{ $proposal->organization_name }}</td><th>Requested By</th><td>{{ $proposal->user->name ?? 'N/A' }}</td></tr>
      <tr><th>Requestor Department</th><td>{{ $proposal->department->name ?? 'N/A' }}</td><th>Reservation No.</th><td>{{ $proposal->reservation->reservation_no ?? 'N/A' }}</td></tr>
      <tr><th>Position</th><td>{{ $proposal->requester_position ?: 'N/A' }}</td><th>Expected Attendees</th><td>{{ $proposal->participants_count }}</td></tr>
      <tr><th>Activity</th><td colspan="3">{{ $proposal->title }}</td></tr>
      <tr><th>Day(s)</th><td>{{ $proposal->activity_days }}</td><th>Speaker</th><td>{{ $proposal->speaker_name ?: 'N/A' }}</td></tr>
      <tr><th>Start</th><td>{{ optional($proposal->start_at)->format('m/d/Y h:i A') }}</td><th>End</th><td>{{ optional($proposal->end_at)->format('m/d/Y h:i A') }}</td></tr>
      <tr><th>Venue</th><td colspan="3">{{ $proposal->facility->name ?? 'N/A' }} — {{ $proposal->facility->location ?? '' }} @if($proposal->venue_other_note)({{ $proposal->venue_other_note }})@endif</td></tr>
      <tr><th>Items / Services</th><td colspan="3">@forelse($proposal->requirementLines() as $line){{ $line['name'] ?? '' }}@if(!empty($line['quantity'])) × {{ $line['quantity'] }}@endif{{ !$loop->last ? ', ' : '' }}@empty None specified @endforelse @if($proposal->equipment_other_note) · Others: {{ $proposal->equipment_other_note }}@endif</td></tr>
      <tr><th>Program Flow</th><td colspan="3" style="white-space:pre-line">{{ $proposal->program_flow }}</td></tr>
    </table>

    <h3 class="print-section-title">Digital Approval Signatures</h3>
    <div class="signature-grid">
      @foreach($signatures as [$label,$person,$signedAt])
      <div class="signature-card">
        <div class="signature-role">{{ $label }}</div>
        <div class="signature-image">@if($person?->signature_data)<img src="{{ $person->signature_data }}" alt="{{ $label }} signature">@else<span>Signature on file unavailable</span>@endif</div>
        <div class="signature-name">{{ $person->name ?? 'N/A' }}</div>
        <div class="signature-date">Digitally approved {{ optional($signedAt)->format('m/d/Y h:i A') }}</div>
      </div>
      @endforeach
    </div>
    <div class="print-footer">System-generated approved proposal. Printed names and e-signatures are taken from the authenticated approval trail.</div>
</div>
<style>
.proposal-print{max-width:1000px;margin:0 auto}.print-kv{width:100%;border-collapse:collapse;font-size:12px}.print-kv th,.print-kv td{border:1px solid #9ca3af;padding:8px;vertical-align:top}.print-kv th{width:18%;background:#f3f4f6}.print-section-title{font-size:14px;margin:20px 0 10px}.signature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.signature-card{border:1px solid #cbd5e1;padding:10px;text-align:center;min-height:145px}.signature-role{font-size:10px;font-weight:700;text-transform:uppercase}.signature-image{height:65px;display:flex;align-items:center;justify-content:center}.signature-image img{max-width:100%;max-height:62px;object-fit:contain}.signature-image span{font-size:9px;color:#6b7280}.signature-name{font-size:11px;font-weight:700;border-top:1px solid #111;padding-top:4px}.signature-date{font-size:9px;color:#4b5563}.print-footer{text-align:center;font-size:9px;margin-top:18px;color:#4b5563}
@media print{body *{visibility:hidden!important}.no-print{display:none!important}#proposal-print,#proposal-print *{visibility:visible!important}#proposal-print{position:absolute;left:0;top:0;width:100%;max-width:none;box-shadow:none!important;border:none!important;padding:0!important}.signature-grid{grid-template-columns:repeat(3,1fr)}@page{size:A4;margin:12mm}}
</style>
@endsection
