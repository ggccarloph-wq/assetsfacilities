@extends('layouts.admin', ['title' => 'FMO User Management', 'subtitle' => 'Facilities-side accounts only. Asset Management users are not listed here.'])

@section('content')

@if($pendingCount)
<div class="note-callout mb-3">
    <i class="bi bi-person-exclamation"></i>
    <div>{{ $pendingCount }} facilities account(s) are waiting for your approval.</div>
</div>
@endif

<div class="surface p-3 mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title" style="font-size:16px">Add Facilities User</h2>
            <div class="module-note">You can only create FMO-side accounts. Asset Management roles are not assignable from here.</div>
        </div>
    </div>
    <form method="POST" action="{{ route('fmo.users.store') }}" class="row g-3">@csrf
        <div class="col-md-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name') }}" required></div>
        <div class="col-md-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
        <div class="col-md-2">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                @foreach($roles as $r)<option value="{{ $r }}" @selected(old('role') === $r)>{{ ucwords(str_replace('_', ' ', $r)) }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Department</label>
            <select name="department_id" class="form-select">
                <option value="">No department</option>
                @foreach($departments as $department)<option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-1"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="col-md-1"><label class="form-label">Confirm</label><input type="password" name="password_confirmation" class="form-control" required></div>
        <div class="col-12"><button class="btn-primaryx"><i class="bi bi-person-plus"></i> Create Account</button></div>
    </form>
</div>

<div class="surface p-3">
    <div class="chip-row">
        <a class="chip {{ $role === '' ? 'active' : '' }}" href="{{ route('fmo.users.index', array_filter(['search' => $search])) }}"><i class="bi bi-collection"></i> All</a>
        @foreach($roles as $r)
            <a class="chip {{ $role === $r ? 'active' : '' }}" href="{{ route('fmo.users.index', array_filter(['role' => $r, 'search' => $search])) }}">{{ ucwords(str_replace('_', ' ', $r)) }}</a>
        @endforeach
    </div>

    <form method="GET" class="search-strip">
        <input type="hidden" name="role" value="{{ $role }}">
        <i class="bi bi-search"></i>
        <input class="search-input" name="search" value="{{ $search }}" placeholder="Search name, email, or role...">
        <button class="btn-primaryx" type="submit"><i class="bi bi-funnel"></i> Search</button>
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>User</th><th>Department</th><th>Role</th><th>Account Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td data-label="User">
                        <div style="font-weight:700">{{ $u->name }}</div>
                        <div class="tiny">{{ $u->email }}</div>
                    </td>
                    <td data-label="Department">{{ $u->department->name ?? 'Not assigned' }}</td>
                    <td data-label="Role"><span class="status {{ $u->role === 'fmo_super_admin' ? 'approved' : ($u->role === 'fmo' ? 'available' : 'pending') }}">{{ ucwords(str_replace('_', ' ', $u->role)) }}</span></td>
                    <td data-label="Account Status">
                        <span class="status {{ $u->is_approved ? 'approved' : 'pending' }}">{{ $u->is_approved ? 'Active' : 'Pending Review' }}</span>
                        <div class="tiny-2 mt-1">{{ $u->email_verified_at ? 'Email verified' : 'Email not verified' }}</div>
                    </td>
                    <td data-label="Actions">
                        <button class="btn-soft small-btn" type="button" data-bs-toggle="collapse" data-bs-target="#edit-fmo-user-{{ $u->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('fmo.users.toggle', $u) }}" class="d-inline">@csrf
                            <button class="btn-soft small-btn">{{ $u->is_approved ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                        <form method="POST" action="{{ route('fmo.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Delete this account?')">@csrf @method('DELETE')
                            <button class="btn-reject small-btn"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                <tr class="collapse-row">
                    <td colspan="5" class="p-0 border-0">
                        <div class="collapse" id="edit-fmo-user-{{ $u->id }}">
                            <div class="p-3" style="background:var(--surface-2);border-top:1px solid var(--line)">
                                <form method="POST" action="{{ route('fmo.users.update', $u) }}" class="row g-3 mb-3">@csrf @method('PUT')
                                    <div class="col-md-3"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ $u->name }}" required></div>
                                    <div class="col-md-3"><label class="form-label">Email</label><input class="form-control" value="{{ $u->email }}" readonly aria-readonly="true"></div>
                                    <div class="col-md-2">
                                        <label class="form-label">Role</label>
                                        <select class="form-select" name="role">
                                            @foreach($roles as $r)<option value="{{ $r }}" @selected($u->role === $r)>{{ ucwords(str_replace('_', ' ', $r)) }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Department</label>
                                        <select class="form-select" name="department_id">
                                            <option value="">No department</option>
                                            @foreach($departments as $department)<option value="{{ $department->id }}" @selected((int) $u->department_id === (int) $department->id)>{{ $department->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Account</label>
                                        <select class="form-select" name="is_approved">
                                            <option value="0" @selected(!$u->is_approved)>Deactivated</option>
                                            <option value="1" @selected($u->is_approved)>Active</option>
                                        </select>
                                    </div>
                                    <div class="col-12"><button class="btn-primaryx"><i class="bi bi-save"></i> Save Changes</button></div>
                                </form>

                                <form method="POST" action="{{ route('fmo.users.reset-password', $u) }}" class="row g-3">@csrf
                                    <div class="col-md-3"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" required></div>
                                    <div class="col-md-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
                                    <div class="col-md-3 d-flex align-items-end"><button class="btn-soft"><i class="bi bi-key"></i> Reset Password</button></div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-state">No facilities users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $users->links('vendor.pagination.custom') }}</div>
</div>

<div class="note-callout mt-3">
    <i class="bi bi-shield-check"></i>
    <div>This list is filtered in the database query, not in the page. Asset Management Super Admin, Asset Management Admins, approvers and OPEX-only requestors can never appear here, and a forged request aimed at one of those accounts is rejected on the server.</div>
</div>
@endsection
