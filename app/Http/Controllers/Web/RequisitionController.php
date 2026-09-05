<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Item;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Notifications\RequisitionStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequisitionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('search'));
        $status = $request->string('status')->toString();
        $user = Auth::user();

        $requisitions = Requisition::with(['department','user','items.item'])
            ->when($user->isRequestor(), fn ($query) => $query->where('user_id', $user->id))
            ->when($user->isApprover(), function ($query) use ($user) {
                if ($user->isDeanApprover()) {
                    $query->where('status', 'pending_college_dean');
                }
                if ($user->isExecutiveApprover()) {
                    $query->where('status', 'pending_executive_director');
                }
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('requisition_no', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhere('branch', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('items.item', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('requisitions.index', compact('requisitions', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $departments = Auth::user()->isAdmin()
            ? Department::orderBy('name')->get()
            : Department::whereKey(Auth::user()->department_id)->get();

        $items = Item::where('is_active', true)
            ->where('item_type', 'OPEX')
            ->where('availability_status', '!=', 'Out of Stock')
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                $item->latest_unit_cost = (float) $item->unit_price;
                return $item;
            });

        $departmentBudgets = $departments->mapWithKeys(fn ($department) => [
            $department->id => [
                'limit' => (float) $department->opex_limit,
                'consumed' => $department->opexConsumed(),
                'remaining' => $department->opexRemaining(),
            ],
        ]);

        return view('requisitions.create', [
            'departments' => $departments,
            'items' => $items,
            'selectedItemId' => $request->integer('item_id') ?: null,
            'departmentBudgets' => $departmentBudgets,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id' => ['required','exists:departments,id'],
            'branch' => ['required','string','max:255'],
            'charge_to_budget_item' => ['required','string','max:255'],
            'csf_no' => ['nullable','string','max:255'],
            'purpose' => ['nullable','string'],
            'requested_by_name' => ['required','string','max:255'],
            'checked_by_name' => ['nullable','string','max:255'],
            'approved_by_name' => ['nullable','string','max:255'],
            'items' => ['required','array','min:1'],
            'items.*.item_id' => ['required','exists:items,id'],
            'items.*.quantity_requested' => ['required','integer','min:1'],
            'items.*.remarks' => ['nullable','string','max:255'],
        ]);

        if (!Auth::user()->isAdmin() && (int) $data['department_id'] !== (int) Auth::user()->department_id) {
            return back()->withErrors(['department_id' => 'You can only request for your assigned department.'])->withInput();
        }

        // OPEX charge slips are charged against the requesting user's department
        // budget as soon as they're submitted, so the total peso amount of this
        // request must fit inside whatever is left of that department's OPEX
        // budget before anything is saved.
        $department = Department::findOrFail($data['department_id']);
        $estimatedTotal = 0.0;
        foreach ($data['items'] as $row) {
            $item = Item::find($row['item_id']);
            $estimatedTotal += (float) ($item->unit_price ?? 0) * (int) $row['quantity_requested'];
        }

        $remainingBudget = $department->opexRemaining();
        if ($estimatedTotal > $remainingBudget) {
            return back()->withInput()->withErrors([
                'items' => "You don't have enough budget. {$department->name} has ₱".number_format($remainingBudget, 2)." remaining in its OPEX budget, but this request needs ₱".number_format($estimatedTotal, 2).".",
            ]);
        }

        try {
            $requisition = DB::transaction(function () use ($data) {
                // Re-check the budget once more inside the transaction with a row
                // lock, in case another request for the same department was
                // submitted at nearly the same moment and would otherwise both
                // pass the earlier check and together overspend the budget.
                $lockedDepartment = Department::whereKey($data['department_id'])->lockForUpdate()->firstOrFail();
                $lockedRemaining = $lockedDepartment->opexRemaining();
                $lockedTotal = 0.0;
                foreach ($data['items'] as $row) {
                    $item = Item::findOrFail($row['item_id']);
                    $lockedTotal += (float) $item->unit_price * (int) $row['quantity_requested'];
                }
                if ($lockedTotal > $lockedRemaining) {
                    throw ValidationException::withMessages([
                        'items' => "You don't have enough budget. {$lockedDepartment->name} has ₱".number_format($lockedRemaining, 2)." remaining in its OPEX budget, but this request needs ₱".number_format($lockedTotal, 2).".",
                    ]);
                }

                $requisition = Requisition::create([
                    'requisition_no' => 'REQ-' . now()->format('YmdHis'),
                    'user_id' => Auth::id(),
                    'department_id' => $data['department_id'],
                    'branch' => $data['branch'],
                    'charge_to_budget_item' => $data['charge_to_budget_item'],
                    'csf_no' => $data['csf_no'] ?? null,
                    'requested_by_name' => $data['requested_by_name'],
                    'checked_by_name' => $data['checked_by_name'] ?? null,
                    'approved_by_name' => $data['approved_by_name'] ?? null,
                    'status' => 'pending_asset_management',
                    'purpose' => $data['purpose'] ?? null,
                    'requested_at' => now(),
                ]);

                foreach ($data['items'] as $row) {
                    $item = Item::findOrFail($row['item_id']);
                    if ($item->item_type !== 'OPEX') {
                        throw ValidationException::withMessages([
                            'items' => 'Only OPEX items can be requested through the requisition form.',
                        ]);
                    }

                    $quantity = (int) $row['quantity_requested'];
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'item_id' => $item->id,
                        'quantity_requested' => $quantity,
                        'remarks' => $row['remarks'] ?? null,
                        'unit_price' => $item->unit_price,
                        'total_amount' => round((float) $item->unit_price * $quantity, 2),
                    ]);
                }

                return $requisition;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            $hint = str_contains($e->getMessage(), 'Unknown column') || str_contains($e->getMessage(), 'no such column')
                ? ' (It looks like a recent database update has not been applied yet -- please run "php artisan migrate" on the server.)'
                : '';
            return back()->withInput()->withErrors([
                'items' => 'Could not submit this charge slip due to a server error.' . $hint . ' If this keeps happening, please contact the system administrator.',
            ]);
        }

        // Notifications are a side effect, not the actual charge slip -- if mail/notify
        // fails (e.g. SMTP not configured), the requisition itself must still be
        // considered submitted successfully. Each recipient is notified inside its own
        // try/catch: earlier, one failed notify() call aborted the whole loop, so admins
        // later in the list silently never got notified. That's fixed here.
        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            try {
                $admin->notify(new RequisitionStatusNotification(
                    $requisition->fresh('department', 'user'),
                    'New requisition awaiting Asset Management review',
                    'A new charge slip request was submitted and is now waiting for Asset Management validation.'
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('requisitions.index')->with('success', 'Charge slip requisition submitted successfully.');
    }

    public function show(Requisition $requisition)
    {
        $this->authorizeAccess($requisition);
        $requisition->load(['department','user','items.item','assetReviewer','deanApprover','executiveApprover']);
        return view('requisitions.show', compact('requisition'));
    }

    /**
     * Printable e-receipt for the requestor once their requisition has been
     * issued -- gives them a copy they can print, and doubles as the in-system
     * e-receipt they can reopen any time from their requisition's detail page.
     */
    public function receipt(Requisition $requisition)
    {
        $this->authorizeAccess($requisition);
        abort_unless($requisition->status === 'issued', 404, 'A receipt is only available once this requisition has been issued.');

        $requisition->load(['department', 'user', 'items.item', 'issuance.issuer', 'issuance.receiver']);
        abort_unless($requisition->issuance, 404, 'No issuance record found for this requisition yet.');

        return view('requisitions.receipt', compact('requisition'));
    }

    /**
     * Only Super Admins may delete a requisition/charge slip entirely -- this is
     * user-submitted data with an approval trail, so it's locked to the highest
     * privilege role rather than the general Asset Management Admin role.
     */
    public function destroy(Requisition $requisition)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $requisition->items()->delete();
        $requisition->delete();
        return redirect()->route('requisitions.index')->with('success', 'Requisition deleted successfully.');
    }

    public function approve(Request $request, Requisition $requisition)
    {
        $this->authorizeAccess($requisition);
        $user = Auth::user();

        $pendingNotifications = [];

        try {
            DB::transaction(function () use ($request, $requisition, $user, &$pendingNotifications) {
                $requisition->load('items.item');

                if ($user->isAdmin() && $requisition->isAwaitingAssetManagement()) {
                    $validated = $request->validate([
                        'items' => ['required','array','min:1'],
                        'items.*.id' => ['required','exists:requisition_items,id'],
                        'items.*.quantity_approved' => ['required','integer','min:0'],
                        'items.*.remarks' => ['nullable','string','max:255'],
                    ]);

                    $hasApprovedQty = false;
                    foreach ($validated['items'] as $row) {
                        $reqItem = $requisition->items->firstWhere('id', (int) $row['id']);
                        if (!$reqItem || !$reqItem->item) {
                            // Item was deleted/unavailable after the requisition was
                            // filed -- skip it instead of crashing on a null relation.
                            continue;
                        }
                        $approvedQty = (int) $row['quantity_approved'];
                        if ($approvedQty > $reqItem->item->quantity) {
                            throw ValidationException::withMessages([
                                'items' => 'Approved quantity for '.$reqItem->item->name.' exceeds available stock.',
                            ]);
                        }
                        if ($approvedQty > $reqItem->quantity_requested) {
                            throw ValidationException::withMessages([
                                'items' => 'Approved quantity cannot be greater than requested quantity.',
                            ]);
                        }
                        $reqItem->update([
                            'quantity_approved' => $approvedQty,
                            'remarks' => $row['remarks'] ?? $reqItem->remarks,
                        ]);
                        $hasApprovedQty = $hasApprovedQty || $approvedQty > 0;
                    }

                    if (!$hasApprovedQty) {
                        throw ValidationException::withMessages([
                            'items' => 'At least one item must have an approved quantity greater than zero.',
                        ]);
                    }

                    $requisition->update([
                        'status' => 'pending_college_dean',
                        'asset_reviewed_by' => $user->id,
                        'asset_reviewed_at' => now(),
                    ]);

                    $pendingNotifications[] = [
                        'users' => \App\Models\User::where('role', 'approver')->where('approver_type', 'dean')->get(),
                        'subject' => 'Requisition awaiting College Dean approval',
                        'message' => 'Asset Management has reviewed the requisition and forwarded it to the College Dean.',
                    ];
                    return;
                }

                if ($user->isDeanApprover() && $requisition->isAwaitingCollegeDean()) {
                    $requisition->update([
                        'status' => 'pending_executive_director',
                        'dean_approved_by' => $user->id,
                        'dean_approved_at' => now(),
                    ]);

                    $pendingNotifications[] = [
                        'users' => \App\Models\User::where('role', 'approver')->where('approver_type', 'executive')->get(),
                        'subject' => 'Requisition awaiting Executive Director approval',
                        'message' => 'The College Dean has approved the requisition and it now needs Executive Director approval.',
                    ];
                    return;
                }

                if ($user->isExecutiveApprover() && $requisition->isAwaitingExecutiveDirector()) {
                    foreach ($requisition->items as $reqItem) {
                        if (!$reqItem->item) {
                            continue;
                        }
                        $approvedQty = (int) ($reqItem->quantity_approved ?? 0);
                        if ($approvedQty > 0) {
                            $reqItem->item->decrement('quantity', $approvedQty);
                            \App\Models\InventoryUsageLog::create([
                                'item_id' => $reqItem->item_id,
                                'requisition_id' => $requisition->id,
                                'usage_date' => now()->toDateString(),
                                'quantity_used' => $approvedQty,
                                'source' => 'requisition',
                                'remarks' => 'Auto-logged from requisition #'.$requisition->requisition_no,
                            ]);
                        }
                    }

                    $isPartial = $requisition->items->contains(fn ($item) => (int) ($item->quantity_approved ?? 0) < (int) $item->quantity_requested);
                    $requisition->update([
                        'status' => $isPartial ? 'partially_approved' : 'approved',
                        'executive_approved_by' => $user->id,
                        'executive_approved_at' => now(),
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                        'finalized_at' => now(),
                    ]);

                    if ($requisition->user) {
                        $pendingNotifications[] = [
                            'users' => collect([$requisition->user]),
                            'subject' => 'Your requisition has been finalized',
                            'message' => $isPartial
                                ? 'Your requisition was partially approved based on available stock.'
                                : 'Your requisition was fully approved and finalized.',
                        ];
                    }

                    // Fully-approved requisitions now sit in the Issuance & Returns
                    // queue waiting to be recorded -- let Asset Management know
                    // instead of relying on them to keep checking the tab.
                    if (!$isPartial) {
                        $pendingNotifications[] = [
                            'users' => \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get(),
                            'subject' => 'New issuance ready to record',
                            'message' => 'Requisition "' . $requisition->requisition_no . '" has been fully approved and needs to be recorded in Issuance & Returns.',
                            'notification' => \App\Notifications\IssuanceNeededNotification::class,
                        ];
                    }
                    return;
                }

                abort(403, 'You are not allowed to approve this requisition at its current stage.');
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            $hint = str_contains($e->getMessage(), 'Unknown column') || str_contains($e->getMessage(), 'no such column')
                ? ' (It looks like a recent database update has not been applied yet -- please run "php artisan migrate" on the server.)'
                : '';
            return back()->withErrors([
                'items' => 'Could not process this requisition due to a server error.' . $hint . ' If this keeps happening, please contact the system administrator.',
            ]);
        }

        $requisition->refresh()->load('department', 'user');
        foreach ($pendingNotifications as $entry) {
            $notificationClass = $entry['notification'] ?? RequisitionStatusNotification::class;
            foreach ($entry['users'] as $notifyUser) {
                try {
                    $notification = $notificationClass === RequisitionStatusNotification::class
                        ? new RequisitionStatusNotification($requisition, $entry['subject'], $entry['message'])
                        : new $notificationClass($requisition);
                    $notifyUser->notify($notification);
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        return redirect()->route('requisitions.show', $requisition)->with('success', 'Requisition updated successfully.');
    }

    public function reject(Request $request, Requisition $requisition)
    {
        $this->authorizeAccess($requisition);
        $request->validate(['reason' => ['required','string']]);
        $requisition->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'finalized_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        try {
            $requisition->user?->notify(new RequisitionStatusNotification(
                $requisition->fresh('department', 'user'),
                'Your requisition has been rejected',
                'Your requisition was rejected. Please review the rejection reason in the system.'
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('requisitions.show', $requisition)->with('success', 'Requisition rejected successfully.');
    }

    private function authorizeAccess(Requisition $requisition): void
    {
        $user = Auth::user();
        if ($user->isRequestor() && (int) $requisition->user_id !== (int) $user->id) {
            abort(403);
        }
    }
}
