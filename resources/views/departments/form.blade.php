<div class="row g-3">
<div class="col-md-6"><label class="form-label">Department Name</label><input name="name" class="form-control" value="{{ old('name',$department->name ?? '') }}" required></div>
<div class="col-md-6"><label class="form-label">Code</label><input name="code" class="form-control" value="{{ old('code',$department->code ?? '') }}" required></div>
<div class="col-md-6"><label class="form-label">CAPEX Limit (₱)</label><input type="number" step="0.01" min="0" name="capex_limit" class="form-control" value="{{ old('capex_limit',$department->capex_limit ?? 0) }}" required></div>
<div class="col-md-6"><label class="form-label">OPEX Limit (₱)</label><input type="number" step="0.01" min="0" name="opex_limit" class="form-control" value="{{ old('opex_limit',$department->opex_limit ?? 0) }}" required>
    @isset($department)
    <div class="tiny mt-1">Consumed so far: ₱{{ number_format($department->opexConsumed(), 2) }} &middot; Remaining: ₱{{ number_format($department->opexRemaining(), 2) }}</div>
    @endisset
</div>
<div class="col-12">
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="restrict_supply_requests" name="restrict_supply_requests" value="1" @checked(old('restrict_supply_requests', $department->restrict_supply_requests ?? false))>
        <label class="form-check-label" for="restrict_supply_requests">This department does not use OPEX Inventory / Requisitions</label>
    </div>
    <div class="tiny mt-1">Turn this on for departments like Facilities Management Office, whose requestors only ever submit Activity Proposals / Facility Reservations. Their OPEX Inventory and Requisitions tabs will be hidden entirely.</div>
</div>
</div>
