@extends('layouts.admin', ['title' => 'Issuance Receipt'])
@section('content')
<div class="module-head">
    <div>
        <h2 class="module-title">Issuance Receipt</h2>
        <div class="module-note">E-receipt for requisition {{ $requisition->requisition_no }}</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" onclick="window.print()" class="btn-primaryx"><i class="bi bi-printer"></i> Print Receipt</button>
        <a href="{{ route('requisitions.show', $requisition) }}" class="btn-soft"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="surface p-4 receipt-print-card" id="print-area">
    <div class="text-center mb-3">
        <div class="tiny-2">NU CLARK · ASSET MANAGEMENT</div>
        <h3 class="module-title mb-1" style="font-size:20px;padding-left:0">Issuance E-Receipt</h3>
        <div class="tiny">Reference No.: <strong>{{ $requisition->requisition_no }}</strong></div>
    </div>

    <table class="kv-table">
        <tr><th><i class="bi bi-building me-1"></i>Department</th><td>{{ $requisition->department->name ?? 'N/A' }}</td></tr>
        <tr><th><i class="bi bi-person me-1"></i>Requested By</th><td>{{ $requisition->requested_by_name ?: ($requisition->user->name ?? 'N/A') }}</td></tr>
        <tr><th><i class="bi bi-calendar-event me-1"></i>Date Requested</th><td>{{ optional($requisition->requested_at)->format('Y-m-d H:i') ?: 'N/A' }}</td></tr>
        <tr><th><i class="bi bi-box-arrow-up me-1"></i>Date Issued</th><td>{{ optional($requisition->issuance->issued_at)->format('Y-m-d H:i') ?: 'N/A' }}</td></tr>
        <tr><th><i class="bi bi-person-check me-1"></i>Issued By</th><td>{{ $requisition->issuance->issuer->name ?? 'N/A' }}</td></tr>
        <tr><th><i class="bi bi-person-badge me-1"></i>Received By</th><td>{{ $requisition->issuance->receiver->name ?? 'N/A' }}</td></tr>
        @if($requisition->issuance->remarks)
        <tr><th><i class="bi bi-chat-left-text me-1"></i>Remarks</th><td class="kv-wide">{{ $requisition->issuance->remarks }}</td></tr>
        @endif
    </table>

    <div class="subhead"><h3 class="subhead-title"><i class="bi bi-list-ul"></i> Issued Items</h3></div>
    <div class="table-responsive mb-3">
        <table class="data-table">
            <thead><tr><th>Item</th><th>Requested Qty</th><th>Issued Qty</th></tr></thead>
            <tbody>
                @foreach($requisition->items as $line)
                <tr>
                    <td data-label="Item">{{ $line->item->name ?? 'N/A' }}</td>
                    <td data-label="Requested">{{ $line->quantity_requested }}</td>
                    <td data-label="Issued">{{ $line->quantity_approved ?? $line->quantity_requested }} {{ $line->item->unit ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="tiny text-center mt-4">This is a system-generated e-receipt from the NU Clark Asset and Inventory System.</div>
</div>

<style>
@media print {
    body * { visibility: hidden !important; }
    #print-area, #print-area * { visibility: visible !important; }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: 1px solid #000;
    }
}
.receipt-print-card { max-width: 680px; margin: 0 auto; }
</style>
@endsection
