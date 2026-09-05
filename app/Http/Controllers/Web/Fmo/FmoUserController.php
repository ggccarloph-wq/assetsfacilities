<?php

namespace App\Http\Controllers\Web\Fmo;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Users tab for the FMO Super Admin.
 *
 * Every query in here runs through User::facilitiesSide(), so Asset
 * Management Super Admin / Admin accounts, approvers and OPEX-only requestors
 * are filtered out in SQL -- not merely hidden in Blade. Each write action
 * re-checks the target with guardTarget() so a hand-crafted POST against an
 * Asset Management user id is rejected too.
 */
class FmoUserController extends Controller
{
    /** The only roles an FMO Super Admin may hand out. */
    public const ASSIGNABLE_ROLES = ['fmo_super_admin', 'fmo', 'housekeeping', 'requestor'];

    private function hashPassword(string $value): string
    {
        return Hash::driver('bcrypt')->make($value, ['rounds' => (int) config('security.bcrypt_rounds', 12)]);
    }

    /**
     * Refuses any write that targets an account outside the Facilities scope.
     * This is the privilege-escalation guard: without it, a forged form POST
     * could edit or delete the Asset Management Super Admin.
     */
    private function guardTarget(User $user): void
    {
        $isFacilitiesSide = User::query()->facilitiesSide()->whereKey($user->id)->exists();

        abort_unless($isFacilitiesSide, 403, 'This account does not belong to the Facilities Management side of the system.');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $role = (string) $request->string('role');
        if (!in_array($role, self::ASSIGNABLE_ROLES, true)) {
            $role = '';
        }

        $users = User::query()
            ->facilitiesSide()
            ->with('department')
            ->when($role !== '', fn ($q) => $q->where('role', $role))
            ->when($search !== '', fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%")))
            ->orderByRaw('CASE WHEN is_approved = 0 THEN 0 ELSE 1 END')
            ->orderByRaw("CASE WHEN role = 'fmo_super_admin' THEN 0 WHEN role = 'fmo' THEN 1 WHEN role = 'housekeeping' THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('fmo.users.index', [
            'users' => $users,
            'departments' => Department::orderBy('name')->get(),
            'roles' => self::ASSIGNABLE_ROLES,
            'search' => $search,
            'role' => $role,
            'pendingCount' => User::query()->facilitiesSide()->where('is_approved', false)->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'role' => ['required', Rule::in(self::ASSIGNABLE_ROLES)],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->hashPassword($data['password']),
            'role' => $data['role'],
            'account_type' => $data['role'] === 'requestor' ? 'student' : ($data['role'] === 'housekeeping' ? 'housekeeping' : 'fmo_staff'),
            'access_scope' => 'fmo',
            'approver_type' => null,
            'department_id' => $data['department_id'] ?? null,
            'is_approved' => true,
            'approved_at' => now(),
            'email_verified_at' => now(),
        ]);

        return back()->with('success', 'Facilities user account created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->guardTarget($user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(self::ASSIGNABLE_ROLES)],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        if ($user->id === auth()->id() && $data['role'] !== $user->role) {
            return back()->withErrors(['role' => 'You cannot change your own FMO Super Admin role.']);
        }

        if ($user->role === 'fmo_super_admin' && $data['role'] !== 'fmo_super_admin'
            && User::where('role', 'fmo_super_admin')->count() <= 1) {
            return back()->withErrors(['role' => 'At least one FMO Super Admin account must remain in the system.']);
        }

        $data['approver_type'] = null;
        $data['is_approved'] = $request->boolean('is_approved');
        $data['approved_at'] = $data['is_approved'] ? ($user->approved_at ?? now()) : null;
        $data['access_scope'] = 'fmo';
        $data['account_type'] = $data['role'] === 'requestor'
            ? 'student'
            : ($data['role'] === 'housekeeping' ? 'housekeeping' : ($data['role'] === 'fmo_super_admin' ? 'fmo_super_admin' : 'fmo_staff'));

        $user->update($data);

        return back()->with('success', 'Facilities user updated.');
    }

    /** Quick activate / deactivate without opening the edit panel. */
    public function toggle(User $user): RedirectResponse
    {
        $this->guardTarget($user);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account while signed in.']);
        }

        $activate = !$user->is_approved;
        $user->update(['is_approved' => $activate, 'approved_at' => $activate ? ($user->approved_at ?? now()) : null]);

        return back()->with('success', $user->name . ' is now ' . ($activate ? 'active' : 'deactivated') . '.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $this->guardTarget($user);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        $user->update(['password' => $this->hashPassword($data['password'])]);

        return back()->with('success', 'Password reset for ' . $user->name . '.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->guardTarget($user);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account while signed in.']);
        }

        if ($user->role === 'fmo_super_admin' && User::where('role', 'fmo_super_admin')->count() <= 1) {
            return back()->withErrors(['user' => 'The last FMO Super Admin account cannot be deleted.']);
        }

        // Requestors with reservation history are kept so the approval trail on
        // old reservations never loses its "Submitted By" name. Deactivate them
        // instead of deleting.
        if ($user->facilityReservations()->exists() || $user->activityProposals()->exists()) {
            return back()->withErrors([
                'user' => 'This account has existing reservation or activity proposal records. Deactivate it instead so the historical approval trail stays intact.',
            ]);
        }

        $user->delete();

        return back()->with('success', 'Facilities user account deleted.');
    }
}
