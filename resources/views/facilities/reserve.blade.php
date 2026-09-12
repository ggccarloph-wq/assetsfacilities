@extends('layouts.admin', ['title' => 'Reserve Facility'])
@section('content')
<div class="form-shell">
  <div class="module-head"><div><h2 class="module-title">Facility Reservation Form</h2><div class="module-note">The system will block the request when it overlaps with an existing pending or approved schedule.</div></div></div>
  <form method="POST" action="{{ route('facilities.reservations.store') }}">@csrf
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Facility</label><select name="facility_id" class="form-select" required><option value="">Select facility</option>@foreach($facilities as $facility)<option value="{{ $facility->id }}" @selected(old('facility_id') == $facility->id)>{{ $facility->name }} — {{ $facility->location }}</option>@endforeach</select></div>
      <div class="col-md-6"><label class="form-label">Activity / Event Title</label><input name="title" class="form-control" value="{{ old('title') }}" required></div>
      <div class="col-md-6"><label class="form-label">Start Date and Time</label><input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at') }}" required></div>
      <div class="col-md-6"><label class="form-label">End Date and Time</label><input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at') }}" required></div>
      <div class="col-12"><label class="form-label">Purpose</label><textarea name="purpose" class="form-control" rows="3">{{ old('purpose') }}</textarea></div>

      @include('facilities.partials.requirements-picker')
    </div>
    <div class="mt-3 d-flex gap-2"><button class="btn-primaryx">Submit Reservation</button><a class="btn-soft" href="{{ route(auth()->user()->homeRouteName()) }}">Cancel</a></div>
  </form>
</div>
@endsection
