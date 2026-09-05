<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Roles the Asset Management Super Admin / Admin may assign. The FMO roles
     * are deliberately absent, so a forged POST cannot mint an
     * fmo_super_admin (or an fmo staff account) from this side of the system.
     */
    public const ASSIGNABLE_ROLES = ['super_admin', 'admin', 'approver', 'housekeeping', 'requestor'];

    /**
     * Rejects any write aimed at an FMO account. Without this an edited form
     * request could target an FMO user id even though the list never shows it.
     */
    private function guardTarget(User $user): void
    {
        $isAssetSide = User::query()->assetManagementSide()->whereKey($user->id)->exists();
        abort_unless($isAssetSide, 403, 'This account belongs to Facilities Management and cannot be managed from Asset Management.');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        // Backend-level separation: the Asset Management Users tab never lists
        // FMO Super Admin or FMO staff accounts -- those belong to the FMO
        // Super Admin's own Users page.
        $users = User::query()
            ->assetManagementSide()
            ->with('department')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('approver_type', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("CASE WHEN NOT is_approved THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 WHEN role = 'approver' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'departments' => Department::orderBy('name')->get(),
            'roles' => self::ASSIGNABLE_ROLES,
            'approverTypes' => ['dean', 'executive', 'adviser', 'sdao', 'academic_director'],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->guardTarget($user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(self::ASSIGNABLE_ROLES)],
            'approver_type' => ['nullable', Rule::in(['dean', 'executive', 'adviser', 'sdao', 'academic_director'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        if ($user->id === auth()->id() && in_array($user->role, ['admin', 'super_admin'], true) && $data['role'] !== $user->role) {
            return back()->withErrors(['role' => 'You cannot remove your own admin access.']);
        }

        if ($user->role === 'admin' && $data['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['role' => 'At least one Asset Management Admin account must remain in the system.']);
            }
        }

        if ($user->role === 'super_admin' && $data['role'] !== 'super_admin') {
            $superAdminCount = User::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return back()->withErrors(['role' => 'The Super Admin role cannot be removed — there must always be exactly one Super Admin account.']);
            }
        }

        if ($data['role'] === 'super_admin' && $user->role !== 'super_admin') {
            if (User::where('role', 'super_admin')->exists()) {
                return back()->withErrors(['role' => 'A Super Admin account already exists. There can only be one.']);
            }
        }

        if ($data['role'] !== 'approver') {
            $data['approver_type'] = null;
        }

        // Keep the stored account type meaningful when an Asset admin changes
        // the role of an older account. Asset-side requestors use the unified
        // Requestor account type.
        $data['access_scope'] = 'asset';
        if ($data['role'] === 'super_admin') {
            $data['account_type'] = 'asset_super_admin';
        } elseif ($data['role'] === 'admin') {
            $data['account_type'] = 'asset_admin';
        } elseif ($data['role'] === 'approver') {
            $data['account_type'] = 'approver';
        } elseif ($data['role'] === 'housekeeping') {
            $data['account_type'] = 'housekeeping';
        } elseif ($data['role'] === 'requestor') {
            $data['account_type'] = 'requestor';
        }

        $data['is_approved'] = $request->boolean('is_approved');
        $data['approved_at'] = $data['is_approved'] ? ($user->approved_at ?? now()) : null;

        $user->update($data);

        return back()->with('success', 'User profile and role updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->guardTarget($user);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account while logged in.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['user' => 'At least one Asset Management Admin account must remain in the system.']);
        }

        if ($user->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return back()->withErrors(['user' => 'The Super Admin account cannot be deleted — there must always be exactly one.']);
        }

        $user->delete();

        return back()->with('success', 'User account deleted successfully.');
    }
}
