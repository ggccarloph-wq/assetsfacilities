@extends('layouts.admin', ['title' => 'Reserve Facility'])
@section('content')
@php
    /*
     | The schedule is now taken from the requestor directly instead of being
     | derived from the current week. The old version mapped a weekday name
     | onto startOfWeek(MONDAY), so a request made on a Saturday for "Monday"
     | resolved to the Monday that had already passed.
     |
     | $earliestStart is the floor for the date pickers. FMO_RESERVATION_LEAD_DAYS
     | lets the office require advance notice without touching this view.
     */
    $leadDays = (int) config('fmo.reservation_lead_days', 0);
    $earliestStart = now('Asia/Manila')->startOfDay()->addDays($leadDays);
    $earliestStartValue = $earliestStart->format('Y-m-d\TH:i');
@endphp

<div class="premium-form-page">
  <div class="premium-form-intro">
    <div class="premium-form-intro-icon"><i class="bi bi-buildings"></i></div>
    <div class="premium-form-intro-copy">
      <div class="premium-form-eyebrow">Facilities Management Office</div>
      <h2>School Facilities Reservation</h2>
      <p>Create the activity proposal, reserve the venue, attach requirements, and route the request to the proper signatories in one guided form.</p>
    </div>
    <div class="premium-form-intro-badge"><i class="bi bi-shield-check"></i><span>Digital Proposal</span></div>
  </div>

  <form method="POST" action="{{ route('activity-proposals.store') }}" id="activityProposalForm" enctype="multipart/form-data" class="premium-form">@csrf
    <section class="premium-section">
      <div class="premium-section-head">
        <div class="premium-step">01</div>
        <div>
          <h3>Activity & Venue</h3>
          <p>Tell Facilities Management who is requesting, what the activity is, and where it will be held.</p>
        </div>
      </div>
      <div class="premium-section-body">
        <div class="row g-4">
          <div class="col-lg-6">
            <label class="form-label">Organization / Department / College</label>
            <input name="organization_name" class="form-control" value="{{ old('organization_name') }}" placeholder="Enter organization or department name" required>
          </div>
          <div class="col-lg-3 col-md-6">
            <label class="form-label">Position</label>
            <input name="requester_position" class="form-control" value="{{ old('requester_position') }}" placeholder="e.g. President">
          </div>
          <div class="col-lg-3 col-md-6">
            <label class="form-label">Expected Attendees</label>
            <input type="number" min="1" name="participants_count" class="form-control" value="{{ old('participants_count') }}" placeholder="0" required>
          </div>

          <div class="col-lg-6">
            <label class="form-label">Title of Activity</label>
            <input name="title" class="form-control" value="{{ old('title') }}" placeholder="Enter the official activity title" required>
          </div>
          <div class="col-lg-3 col-md-6">
            <label class="form-label">Name of Speaker</label>
            <input name="speaker_name" class="form-control" value="{{ old('speaker_name') }}" placeholder="Type N/A if none" required>
            <div class="field-hint">Required. Enter <strong>N/A</strong> when there is no speaker.</div>
          </div>
          <div class="col-lg-3 col-md-6">
            <label class="form-label">Venue</label>
            <select name="facility_id" id="facility_select" class="form-select" required>
              <option value="">Select venue</option>
              @foreach($facilities as $facility)
                <option value="{{ $facility->id }}" @selected(old('facility_id') == $facility->id)>{{ $facility->name }} — {{ $facility->location }}</option>
              @endforeach
            </select>
            <input type="text" name="venue_other_note" id="venue_other_note" class="form-control mt-2 d-none" placeholder="Specify venue" value="{{ old('venue_other_note') }}">
          </div>
        </div>
      </div>
    </section>

    <section class="premium-section">
      <div class="premium-section-head">
        <div class="premium-step">02</div>
        <div>
          <h3>Schedule</h3>
          <p>Choose the activity day or date range.</p>
        </div>
      </div>
      <div class="premium-section-body">
        <div class="premium-schedule-grid">
          <div class="premium-schedule-fields">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label">Start Date &amp; Time</label>
                <input type="datetime-local" name="activity_start_at" id="activityStartAt" class="form-control"
                       value="{{ old('activity_start_at') }}" min="{{ $earliestStartValue }}" required>
                @error('activity_start_at')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="form-text">
                  @if($leadDays > 0)
                    Earliest bookable date is {{ $earliestStart->format('F j, Y') }} ({{ $leadDays }}-day advance notice).
                  @else
                    Past dates can't be selected.
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">End Date &amp; Time</label>
                <input type="datetime-local" name="activity_end_at" id="activityEndAt" class="form-control"
                       value="{{ old('activity_end_at') }}" min="{{ $earliestStartValue }}" required>
                @error('activity_end_at')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="form-text">Fills in automatically when you set the start. Adjust for multi-day activities.</div>
              </div>
            </div>
          </div>
          <div class="premium-date-summary">
            <div class="premium-date-summary-top"><i class="bi bi-calendar2-week"></i><span>Selected schedule</span></div>
            <strong id="activityDaysPreview">No schedule selected yet</strong>
            <div class="premium-date-pair">
              <div><span>Start date</span><input type="text" id="startDatePreview" class="form-control" readonly></div>
              <i class="bi bi-arrow-right"></i>
              <div><span>End date</span><input type="text" id="endDatePreview" class="form-control" readonly></div>
            </div>
            <p id="scheduleDurationNote">Pick a start date and time above. The summary and duration update as you type.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="premium-section">
      <div class="premium-section-head">
        <div class="premium-step">03</div>
        <div>
          <h3>Facility Requirements</h3>
          <p>Select the equipment, items, and services needed for the activity. Quantity fields appear only when an option is selected.</p>
        </div>
      </div>
      <div class="premium-section-body premium-requirements-wrap">
        @include('facilities.partials.requirements-picker')
      </div>
    </section>

    <section class="premium-section">
      <div class="premium-section-head">
        <div class="premium-step">04</div>
        <div>
          <h3>Program Flow</h3>
          <p>Type the program sequence manually or attach a PDF, Word document, or text file for extraction.</p>
        </div>
      </div>
      <div class="premium-section-body">
        <div class="premium-upload-zone">
          <div class="premium-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
          <div class="premium-upload-copy">
            <strong>Attach program flow</strong>
            <span>PDF, DOCX, or TXT · Maximum 5 MB</span>
          </div>
          <input type="file" id="programFlowFile" name="program_flow_file" accept=".pdf,.docx,.txt,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain" class="form-control">
        </div>
        <div class="field-hint mt-2">The original file remains attached so FMO can open it. Word files extract most reliably.</div>

        <div class="mt-4">
          <label class="form-label">Program Flow Details</label>
          <textarea name="program_flow" id="programFlowText" class="form-control premium-textarea" rows="7" placeholder="Example: 8:00 AM — Registration&#10;8:30 AM — Opening Program&#10;9:00 AM — Main Activity">{{ old('program_flow') }}</textarea>
          <div class="field-hint">You may leave this blank when uploading a file. The extracted text will be filled in after submission and can still be edited afterwards.</div>
        </div>
      </div>
    </section>

    <section class="premium-section">
      <div class="premium-section-head">
        <div class="premium-step">05</div>
        <div>
          <h3>Signature Routing</h3>
          <p>Facilities Management is always the first review. Select the proper approvers for each succeeding step.</p>
        </div>
      </div>
      <div class="premium-section-body">
        <div class="premium-routing-grid">
          <div class="premium-route-card is-automatic">
            <div class="premium-route-number">1</div>
            <div class="premium-route-content">
              <span class="premium-route-kicker">First Review</span>
              <strong>Facilities Management</strong>
              <div class="premium-auto-assigned"><i class="bi bi-check-circle-fill"></i> Auto Assigned</div>
              <p>The authorized FMO Staff or FMO Super Admin who completes the review becomes the assigned reviewer.</p>
            </div>
          </div>

          <div class="premium-route-card">
            <div class="premium-route-number">2</div>
            <div class="premium-route-content">
              <span class="premium-route-kicker">Prepared By</span>
              <strong>Adviser / Program Chair</strong>
              @include('partials.department-approver-tree', [
                'inputName' => 'adviser_id',
                'people' => $advisers,
                'placeholder' => 'Select department, then Adviser / Program Chair',
                'treeId' => 'adviser_tree',
              ])
            </div>
          </div>

          <div class="premium-route-card">
            <div class="premium-route-number">3</div>
            <div class="premium-route-content">
              <span class="premium-route-kicker">Noted By</span>
              <strong>Dean / Principal</strong>
              @include('partials.department-approver-tree', [
                'inputName' => 'department_approver_id',
                'people' => $deansPrincipals,
                'placeholder' => 'Select department, then Dean / Principal',
                'treeId' => 'dean_tree',
              ])
            </div>
          </div>

          <div class="premium-route-card">
            <div class="premium-route-number">4</div>
            <div class="premium-route-content">
              <span class="premium-route-kicker">Noted By</span>
              <strong>SDAO</strong>
              @include('partials.department-approver-tree', [
                'inputName' => 'sdao_id',
                'people' => $sdaoOfficers,
                'placeholder' => 'Select SDAO Officer',
                'treeId' => 'sdao_tree',
                'fixedGroupName' => 'SDAO',
              ])
            </div>
          </div>

          <div class="premium-route-card">
            <div class="premium-route-number">5</div>
            <div class="premium-route-content">
              <span class="premium-route-kicker">Reviewed By</span>
              <strong>Academic Director</strong>
              <div class="premium-auto-assigned"><i class="bi bi-check-circle-fill"></i> Auto Assigned</div>
              @if($academicDirector)
                <div class="premium-locked-field mt-2">
                  <i class="bi bi-person-badge-fill"></i>
                  <span>{{ $academicDirector->name }}</span>
                </div>
                <p>Campus-wide role, so there is nothing to choose. This step is routed by the system.</p>
              @else
                <div class="premium-warning mt-2">
                  <i class="bi bi-exclamation-triangle"></i>
                  <div><strong>No Academic Director configured.</strong><span>Ask the Super Admin to assign one before submitting.</span></div>
                </div>
              @endif
            </div>
          </div>

          <div class="premium-route-card">
            <div class="premium-route-number">6</div>
            <div class="premium-route-content">
              <span class="premium-route-kicker">Approved By</span>
              <strong>Executive Director</strong>
              <div class="premium-auto-assigned"><i class="bi bi-check-circle-fill"></i> Auto Assigned</div>
              @if($executiveDirector)
                <div class="premium-locked-field mt-2">
                  <i class="bi bi-person-badge-fill"></i>
                  <span>{{ $executiveDirector->name }}</span>
                </div>
                <p>Campus-wide role, so there is nothing to choose. This step is routed by the system.</p>
              @else
                <div class="premium-warning mt-2">
                  <i class="bi bi-exclamation-triangle"></i>
                  <div><strong>No Executive Director configured.</strong><span>Ask the Super Admin to assign one before submitting.</span></div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="premium-form-actionbar">
      <div class="premium-action-note"><i class="bi bi-info-circle"></i><span>Review the details before submitting. Existing approval rules and system processes remain unchanged.</span></div>
      <div class="premium-action-buttons">
        <a class="btn-soft" href="{{ route('activity-proposals.index') }}">Cancel</a>
        <button class="btn-primaryx"><i class="bi bi-send"></i> Submit Reservation Proposal</button>
      </div>
    </div>
  </form>
</div>

<script>
(function () {
  const facility = document.getElementById('facility_select');
  const other = document.getElementById('venue_other_note');
  function syncVenue() {
    const text = facility.options[facility.selectedIndex]?.text || '';
    if (text.toLowerCase().includes('other')) other.classList.remove('d-none');
    else { other.classList.add('d-none'); other.value = ''; }
  }
  facility.addEventListener('change', syncVenue); syncVenue();

  /*
   | Schedule summary. The requestor picks two real datetimes; everything shown
   | here is derived from those values, so the weekday label can never disagree
   | with the stored date the way the old week-mapped version could.
   */
  const startAt = document.getElementById('activityStartAt');
  const endAt = document.getElementById('activityEndAt');
  const startPreview = document.getElementById('startDatePreview');
  const endPreview = document.getElementById('endDatePreview');
  const daysPreview = document.getElementById('activityDaysPreview');
  const durationNote = document.getElementById('scheduleDurationNote');
  const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

  function parseLocal(value) {
    if (!value) return null;
    const d = new Date(value);
    return isNaN(d.getTime()) ? null : d;
  }
  function formatDate(d) {
    return String(d.getMonth()+1).padStart(2,'0') + '/' + String(d.getDate()).padStart(2,'0') + '/' + d.getFullYear();
  }
  function formatTime(d) {
    let h = d.getHours(); const m = String(d.getMinutes()).padStart(2,'0');
    const ampm = h >= 12 ? 'PM' : 'AM'; h = h % 12 || 12;
    return h + ':' + m + ' ' + ampm;
  }
  function describeDuration(s, e) {
    const mins = Math.round((e - s) / 60000);
    if (mins <= 0) return null;
    const days = Math.floor(mins / 1440);
    const hours = Math.floor((mins % 1440) / 60);
    const rem = mins % 60;
    const parts = [];
    if (days) parts.push(days + (days === 1 ? ' day' : ' days'));
    if (hours) parts.push(hours + (hours === 1 ? ' hour' : ' hours'));
    if (rem) parts.push(rem + ' min');
    return parts.join(' ');
  }

  function syncSchedule() {
    const s = parseLocal(startAt.value);
    const e = parseLocal(endAt.value);

    // The end can never precede the start, enforced in the picker itself.
    if (s) {
      endAt.min = startAt.value;
      if (!endAt.value || (e && e < s)) {
        const suggested = new Date(s.getTime() + 60 * 60000);
        const pad = (n) => String(n).padStart(2, '0');
        endAt.value = suggested.getFullYear() + '-' + pad(suggested.getMonth()+1) + '-' + pad(suggested.getDate())
                    + 'T' + pad(suggested.getHours()) + ':' + pad(suggested.getMinutes());
      }
    }

    const end = parseLocal(endAt.value);
    startPreview.value = s ? formatDate(s) : '';
    endPreview.value = end ? formatDate(end) : '';

    if (!s || !end) {
      daysPreview.textContent = 'No schedule selected yet';
      durationNote.textContent = 'Pick a start date and time above. The summary and duration update as you type.';
      return;
    }

    const sameDay = s.toDateString() === end.toDateString();
    daysPreview.textContent = sameDay
      ? dayNames[s.getDay()] + ', ' + formatDate(s)
      : dayNames[s.getDay()] + ' ' + formatDate(s) + ' to ' + dayNames[end.getDay()] + ' ' + formatDate(end);

    const span = describeDuration(s, end);
    durationNote.textContent = span
      ? formatTime(s) + ' to ' + formatTime(end) + ' \u00b7 ' + span + ' \u00b7 Philippine time'
      : 'The end must be later than the start.';
  }

  startAt.addEventListener('change', syncSchedule);
  startAt.addEventListener('input', syncSchedule);
  endAt.addEventListener('change', syncSchedule);
  endAt.addEventListener('input', syncSchedule);
  syncSchedule();
})();
</script>
@endsection
