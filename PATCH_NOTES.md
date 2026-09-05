# PATCH NOTES — Asset Management / FMO Super Admin Separation + FMO Reservation Features

Date: 2026-09-06
Base project: `upd-forgot-password` (NU Clark CAPEX/OPEX + Facilities System, Laravel)

---

## 1. Summary of the changes

This update splits the system into two fully independent administrative domains and adds
the FMO reservation features that were requested.

**Separation**

- The existing Super Admin (`super_admin`) is now **Asset Management only**. Every
  Facilities/FMO menu, route, controller action and user record is closed to it.
- A brand new role **`fmo_super_admin`** owns the Facilities side: dashboard,
  reservations, venues, items, services and FMO user accounts.
- Enforcement is on the **backend** (middleware + policy checks + query scopes), not just
  by hiding sidebar links. Typing an `/fmo/...` URL as an Asset Management Super Admin
  returns 403, and vice versa.

**New FMO features (from the follow-up request)**

- The FMO account now sees the **full approval trail** of each reservation — who has
  already approved, who is holding it right now, and who has not acted yet.
- Reservation Requests has a **"View All Details"** button opening a page with everything
  the requestor filled up, plus the entire approver process.
- The **"Items Needed" checkboxes now accept quantities** (how many speakers, tables,
  ITSO personnel, etc.).
- Ticking **"Others"** reveals a free-text field where the requestor types what else they need.
- The **Activity Proposals tab was removed from the FMO account** (it is a requestor form).
  FMO still signs proposals — the sign button moved onto the reservation detail page.
- Reservation Requests now has **status filters (All / Pending / Approved / Rejected)** and a
  **search bar** for requestor name, activity title, reservation number and venue.

No existing data, workflow, approval process, reservation, OPEX requisition or user account
was removed or reset. Every schema change is additive.

---

## 2. Files modified

| File | What changed |
|---|---|
| `app/Models/User.php` | New role helpers (`isFmoSuperAdmin`, `isFmoSide`, `canManageFmoUsers`, `canDeleteFacilityRecords`, `canAccessAssetManagement`); `canManageFacilities()` no longer returns true for the Asset Management Super Admin; new `facilitiesSide()` / `assetManagementSide()` query scopes; `homeRouteName()` sends FMO accounts to the FMO dashboard; added `facilityReservations()` and `activityProposals()` relations. |
| `app/Models/FacilityReservation.php` | New fillable columns; `approvalTrail()`, `approvalProgress()`, `approvedByNames()`, `pendingApproverNames()`, `requirementLines()`, `requirementOtherNote()`, `isApproved()`, `isRejected()`. |
| `app/Models/ActivityProposal.php` | Added `equipment_details` / `equipment_other_note` to `$fillable`; new `requirementLines()` helper. |
| `app/Http/Controllers/Web/FacilityController.php` | Venue CRUD moved out to the FMO controllers. `index()` now redirects by role. `storeReservation()` stores structured quantities and the Others note; notifies FMO roles; redirects by role. `destroyReservation()` is now FMO Super Admin only. |
| `app/Http/Controllers/Web/ActivityProposalController.php` | Loads the DB-driven item/service catalogue; stores quantities + Others note on both the proposal and its reservation; every `isAdmin()` override replaced with `isFmoSuperAdmin()` / `isFmoSide()` so Asset Management can no longer sign, reject or delete Facilities records. |
| `app/Http/Controllers/Web/AdminUserController.php` | Query now runs through `assetManagementSide()`; `ASSIGNABLE_ROLES` no longer contains any FMO role; new `guardTarget()` blocks writes aimed at FMO accounts. |
| `app/Http/Controllers/Web/AuthController.php` | New FMO staff registrations are routed to the FMO Super Admin for approval instead of Asset Management. |
| `app/Notifications/FacilityReservationStatusNotification.php` | New `linkFor()` sends FMO recipients to the reservation detail page and everyone else to a URL their role can actually open. |
| `bootstrap/app.php` | Registered `fmo_access`, `fmo_super_admin` and `asset_management` middleware aliases. |
| `routes/web.php` | New `/fmo` route group; Facilities routes gated; Asset Management/OPEX routes gated with `asset_management`. |
| `resources/views/layouts/admin.blade.php` | Sidebar rewritten into two mutually exclusive branches; Activity Proposals removed from FMO and from Asset Management Super Admin; brand and topbar labels follow the signed-in side; new CSS for trail states, filter chips and the quantity picker. |
| `resources/views/facilities/reserve.blade.php` | Uses the new items/services picker instead of a plain textarea. |
| `resources/views/activity_proposals/create.blade.php` | Hard-coded checkbox list replaced by the DB-driven picker with quantities and Others. |
| `resources/views/activity_proposals/show.blade.php` | Items now render with quantities and the Others note; role checks updated. |
| `resources/views/activity_proposals/index.blade.php` | Role checks updated. |
| `database/seeders/DatabaseSeeder.php` | Seeds the FMO Super Admin account and the facility item/service catalogue (idempotent `updateOrCreate`). |

---

## 3. New files created

**Models / support**
- `app/Models/FacilityItem.php`
- `app/Support/FacilityRequirements.php`

**Middleware**
- `app/Http/Middleware/FmoAccessMiddleware.php`
- `app/Http/Middleware/FmoSuperAdminMiddleware.php`
- `app/Http/Middleware/AssetManagementMiddleware.php`

**Controllers**
- `app/Http/Controllers/Web/Fmo/FmoDashboardController.php`
- `app/Http/Controllers/Web/Fmo/FmoReservationController.php`
- `app/Http/Controllers/Web/Fmo/FmoVenueController.php`
- `app/Http/Controllers/Web/Fmo/FacilityItemController.php`
- `app/Http/Controllers/Web/Fmo/FmoUserController.php`

**Views**
- `resources/views/fmo/dashboard.blade.php`
- `resources/views/fmo/reservations/index.blade.php`
- `resources/views/fmo/reservations/show.blade.php`
- `resources/views/fmo/venues/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`, `form.blade.php`
- `resources/views/fmo/catalog/index.blade.php`
- `resources/views/fmo/users/index.blade.php`
- `resources/views/facilities/partials/requirements-picker.blade.php`

**Removed (intentionally replaced by the FMO venue screens)**
- `resources/views/facilities/index.blade.php`
- `resources/views/facilities/create.blade.php`
- `resources/views/facilities/edit.blade.php`
- `resources/views/facilities/form.blade.php`

---

## 4. Database migrations added

`database/migrations/2026_09_06_000016_add_fmo_super_admin_and_facility_catalog.php`

- **Creates `facility_items`**: `id, name, type (item|service), unit, description,
  allows_quantity, is_active, sort_order, timestamps`.
- **Adds to `activity_proposals`**: `equipment_details` (nullable text/JSON),
  `equipment_other_note` (nullable string).
- **Adds to `facility_reservations`**: `resources_details` (nullable text/JSON),
  `resources_other_note` (nullable string).
- **Back-fills the catalogue** with the previously hard-coded options plus the new ones
  (Table, Chairs, Sound System, Speaker, Projector, Extension Cord, Microphone, Flag,
  Whiteboard, ITSO Services, Technical Assistance, Audio/Visual Support, Janitors,
  Electricians) — **only when the table is empty**, so re-running migrations never
  duplicates or overwrites rows the FMO Super Admin has edited.

Notes:
- Every column is added with a `Schema::hasColumn` guard, so the migration is safe to run
  against an already-updated database.
- `users.role` is a plain `string` column, so the new `fmo_super_admin` value needed **no
  schema change** and no existing role was rewritten.
- The old `equipment_needed` / `resources_needed` string columns are untouched and still
  written to, so every old screen, export and notification keeps working.

---

## 5. Roles / permissions added or changed

| Role | Asset Mgmt | Facilities | FMO Users | Notes |
|---|---|---|---|---|
| `super_admin` (Asset Management Super Admin) | Full | **DENIED** | **DENIED** | Lost all Facilities access |
| `admin` (Asset Management Admin) | Full | **DENIED** | **DENIED** | Lost Activity Proposal override powers |
| `fmo_super_admin` (**NEW**) | **DENIED** | Full | Full | Venues, items, services, reservations, FMO users |
| `fmo` (FMO Staff) | **DENIED** | Manage (no deletes) | No | Existing responsibilities preserved |
| `approver` | Unchanged | Signs proposals as before | No | Unchanged |
| `requestor` | Unchanged | Submits reservations | No | Unchanged |
| `housekeeping` | Scans only | No | No | Unchanged |

Destructive Facilities actions (delete venue, delete reservation, delete catalogue entry,
delete activity proposal, delete FMO user) are **FMO Super Admin only**.

---

## 6. Asset Management Super Admin changes

- Sidebar no longer renders Facilities, Activity Proposals, or anything FMO.
- `canManageFacilities()` returns `false` — every `abort_unless` in the Facilities
  controllers now rejects it.
- All `/fmo/*` routes return **403** via `FmoAccessMiddleware`.
- `/facilities` redirects it to its own Asset Management home instead of showing venues.
- Its Users page runs `assetManagementSide()`, so FMO Super Admin and FMO staff accounts
  are not returned by the query at all.
- It can no longer assign `fmo` or `fmo_super_admin` roles — those values fail validation.
- It can no longer sign, reject or delete Activity Proposals.

## 7. FMO Super Admin features

- **Facilities Dashboard** (`/fmo/dashboard`) — pending/approved/rejected counts, venue
  counts, upcoming confirmed schedules, item/service/user counts, review queue.
- **Reservation Requests** (`/fmo/reservations`) — status filter, search bar, approval
  progress bar, View All Details.
- **Reservation Details** (`/fmo/reservations/{id}`) — full request data, linked Activity
  Proposal data, items and services with quantities, Others note, complete approval trail,
  approve/reject, and "Sign as Facilities Management".
- **Venues** (`/fmo/venues`) — full CRUD, activate/deactivate, detail view with history.
- **Facility Items** (`/fmo/items`) and **Facility Services** (`/fmo/services`) — add, edit,
  activate/deactivate, delete.
- **FMO Users** (`/fmo/users`) — list, add, edit, activate/deactivate, reset password, delete.
- No CAPEX, OPEX, requisition, issuance, supplier, forecast, scan or reference-data module
  appears anywhere in this account.

## 8. Venue management changes

- Full CRUD plus activate/deactivate, moved to `FmoVenueController`.
- Venues marked **active** appear automatically on the Reservation Request form and the
  Activity Proposal form (both read `Facility::where('is_active', true)`).
- **Delete protection:** a venue linked to any reservation record, or referenced by any
  Activity Proposal, cannot be deleted. The UI shows a "Protected" badge and the backend
  refuses the request with an explanation. Deactivating hides it from new reservations
  while all historical bookings stay intact.

## 9. Facility item / service management changes

- The reservation checklist is now **database-driven** through `facility_items`. Nothing is
  hard-coded in the Blade views anymore.
- Each entry has a name, a type (item or service), an optional unit, an optional
  description, a quantity-input toggle, an active flag and a display order.
- Adding an entry makes it appear on the Reservation form immediately; deactivating or
  deleting removes it from **new** forms only.
- Old reservations keep their own saved copy of the item name and quantity, so removing a
  catalogue entry never corrupts or blanks a historical record.

## 10. FMO user management changes

- The list is built with `User::query()->facilitiesSide()`, which returns only:
  FMO Super Admin, FMO staff, housekeeping, requestors from a facility-only department,
  and any requestor who has actually filed a facility reservation or activity proposal.
- Asset Management Super Admin, Asset Management Admins, approvers and OPEX-only
  requestors are excluded **in SQL**.
- Assignable roles are limited to `fmo_super_admin`, `fmo`, `housekeeping`, `requestor`.
- Actions: view, add, edit, activate/deactivate, reset password, delete.
- Guards: cannot change your own role, cannot remove the last FMO Super Admin, cannot
  delete your own account, cannot delete a user who has reservation or proposal history
  (deactivate instead, so the approval trail keeps their name).

## 11. Security improvements

- **Three new middleware** enforce the split at the routing layer: `fmo_access`,
  `fmo_super_admin`, `asset_management`.
- **Direct URL access is blocked both ways.** Asset Management accounts get 403 on
  `/fmo/*`; FMO accounts get 403 on dashboard, CAPEX, OPEX, requisitions, issuances, QR,
  reports, suppliers, allocations, departments, forecast, scans, reference data and the
  Asset Management users page.
- **No privilege escalation through edited form requests.** Both user controllers validate
  `role` against a whitelist that excludes the other side's roles, so a hand-crafted POST
  cannot mint an `fmo_super_admin` from Asset Management or a `super_admin` from FMO.
- **Target guards on every write.** `FmoUserController::guardTarget()` re-runs the
  facilities-side query against the target id, and `AdminUserController::guardTarget()`
  rejects FMO targets — so forging a user id in the URL fails even though the list never
  showed that user.
- Quantity input is clamped server-side to 1–100000 and item ids are resolved against the
  database, so arbitrary values cannot be injected into a reservation.
- Deletion of venues, reservations, catalogue entries and proposals is restricted to the
  FMO Super Admin at both the route (`fmo_super_admin` middleware) and controller level.

## 12. Routes / middleware changes

**New middleware aliases** (`bootstrap/app.php`): `fmo_access`, `fmo_super_admin`, `asset_management`.

**New route group** — all under `fmo_access`:

```
GET    /fmo/dashboard                         fmo.dashboard
GET    /fmo/reservations                      fmo.reservations.index
GET    /fmo/reservations/{reservation}        fmo.reservations.show
POST   /fmo/reservations/{reservation}/approve fmo.reservations.approve
POST   /fmo/reservations/{reservation}/reject  fmo.reservations.reject
DELETE /fmo/reservations/{reservation}        fmo.reservations.destroy   (fmo_super_admin)
GET    /fmo/venues                            fmo.venues.index
GET    /fmo/venues/create                     fmo.venues.create
POST   /fmo/venues                            fmo.venues.store
GET    /fmo/venues/{venue}                    fmo.venues.show
GET    /fmo/venues/{venue}/edit               fmo.venues.edit
PUT    /fmo/venues/{venue}                    fmo.venues.update
POST   /fmo/venues/{venue}/toggle             fmo.venues.toggle
DELETE /fmo/venues/{venue}                    fmo.venues.destroy         (fmo_super_admin)
GET    /fmo/items                             fmo.items.index
POST   /fmo/items                             fmo.items.store
GET    /fmo/services                          fmo.services.index
POST   /fmo/services                          fmo.services.store
PUT    /fmo/catalog/{facilityItem}            fmo.catalog.update
POST   /fmo/catalog/{facilityItem}/toggle     fmo.catalog.toggle
DELETE /fmo/catalog/{facilityItem}            fmo.catalog.destroy        (fmo_super_admin)
GET    /fmo/users                             fmo.users.index            (fmo_super_admin)
POST   /fmo/users                             fmo.users.store            (fmo_super_admin)
PUT    /fmo/users/{user}                      fmo.users.update           (fmo_super_admin)
POST   /fmo/users/{user}/toggle               fmo.users.toggle           (fmo_super_admin)
POST   /fmo/users/{user}/reset-password       fmo.users.reset-password   (fmo_super_admin)
DELETE /fmo/users/{user}                      fmo.users.destroy          (fmo_super_admin)
```

**Changed routes**

- `Route::resource('facilities', ...)` (venue CRUD) removed — replaced by `/fmo/venues`.
- `facilities.reservations.approve` / `.reject` now behind `fmo_access`.
- `facilities.reservations.destroy` now behind `fmo_super_admin`.
- `GET /facilities` kept as a redirect so old bookmarks and previously-sent notification
  links never 404 or 403.
- `asset_management` middleware added to: dashboard, items, departments, suppliers,
  allocations, reference-data, requisitions, issuances, QR, reports, forecast, asset-scans
  and the Asset Management users routes.

## 13. Testing performed

Verified by code review and route/flow tracing (see §14 for what this does not cover):

- Asset Management Super Admin: sidebar renders no Facilities/FMO links; `canManageFacilities()`
  is false; `/fmo/*` hits `FmoAccessMiddleware` before the controller; users query excludes FMO
  roles; role validation rejects `fmo` and `fmo_super_admin`.
- FMO Super Admin: lands on `/fmo/dashboard`; sidebar shows only Facilities links; no Activity
  Proposals tab; `asset_management` middleware blocks every Asset Management route.
- FMO staff: same Facilities navigation minus the Users tab; delete buttons hidden and the
  matching routes refuse them via `fmo_super_admin`.
- Reservation flow: submitting the form writes the summary string, the JSON breakdown and the
  Others note; the FMO detail page renders all three plus the trail.
- Old records: `FacilityRequirements::decode()` falls back to splitting the legacy comma string,
  so pre-update reservations still display their items (without quantities, which they never had).
- Migration re-run safety: `hasColumn` / `hasTable` guards and the empty-table check on the
  catalogue seed.
- Structural syntax check (brace/paren/bracket balance) run across all modified and new PHP files.

## 14. Remaining limitations

1. **No PHP runtime was available in the environment used to prepare this build**, so
   `php artisan migrate`, `php artisan serve` and the automated test suite were **not
   executed**. The code was reviewed and structurally checked, but please run the commands
   in §15 and click through the flows before treating this as production-ready. If
   anything fails on first run, the error will almost certainly be in one of the new files
   listed in §3.
2. Existing reservations created before this update have **no quantity data** — they only
   ever stored a comma-separated list. They display as item names without a quantity. This
   is a data limitation, not a bug.
3. Reservations that are **not** linked to an Activity Proposal have a short two-step trail
   (Submitted → FMO Confirmation), because there is no signature chain to show for them.
4. The **Activity Proposal routes still exist** for FMO accounts (the tab is removed from the
   sidebar only). This is deliberate: the FMO signature action posts to
   `activity-proposals.sign-facilities`, so blocking those routes would break signing.
5. `housekeeping` accounts appear in the FMO Users list because they sit in the FMO
   department, but they still use the Asset Scans module. Move them to another department
   if you want them out of that list.
6. The Asset Management Admin lost its Activity Proposal override powers (it could
   previously sign or reject any stage). If you want that back, it must be added
   deliberately — it conflicts with the separation as specified.
7. No automated PHPUnit tests were written for the new controllers.

## 15. Commands needed to run the updated project

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment (an .env is already included; regenerate the key if needed)
php artisan key:generate

# 3. Run the new migration on your EXISTING database (additive, keeps all data)
php artisan migrate

# 4. Optional — add the FMO Super Admin account and the item/service catalogue
#    to an existing database. This seeder uses updateOrCreate, so it will not
#    duplicate or reset existing records.
php artisan db:seed

# 5. Clear caches after the route/view changes
php artisan optimize:clear

# 6. Run
php artisan serve
```

**For a clean demo database only** (this DROPS everything — never run it on live data):

```bash
php artisan migrate:fresh --seed
```

### Demo accounts

| Account | Email | Password |
|---|---|---|
| Asset Management Super Admin | `superadmin@nuclark.local` | `super123` |
| Asset Management Admin | `admin@nuclark.local` | `admin123` |
| **FMO Super Admin (new)** | `fmosuperadmin@nuclark.local` | `fmosuper123` |
| FMO Staff | `fmo@nuclark.local` | `fmo12345` |
| Requestor | `requestor@nuclark.local` | `request123` |

Change these passwords before any real deployment.

## 2026-09-06 — Voucher-Gated Account Creation & Admin Scope Separation

### Account creation
- Replaced the public Requestor / Approver / FMO role selector with two registration paths:
  - **Student Access** — no voucher required; account is created as a Student requestor and is routed to **Activity Proposals** only.
  - **Asset Management Access** — locked until a valid voucher from Asset Management is verified.
- Public registration no longer allows users to self-select `approver`, `fmo`, `admin`, or `super_admin` roles.
- Added explicit user fields:
  - `account_type` (`student`, `staff`, `organization`, `approver`, etc.)
  - `access_scope` (`fmo` or `asset`)
- Staff and Organization Asset requestors receive **OPEX + Requisition + Activity Proposals** access.
- Approver accounts remain **Pending Review** after voucher registration until Asset Management approves them.

### Asset Management access vouchers
- Added **Access Vouchers** page for Asset Management Admin / Super Admin.
- Voucher types are intentionally separate:
  - Staff
  - Organization
  - Approver
- Approver vouchers are bound to an exact approver type (`Adviser`, `Dean`, `SDAO`, `Academic Director`, or `Executive Director`). The account creator cannot change it.
- Optional department binding is supported. If a voucher is bound to a department, registration cannot override that department.
- Vouchers are:
  - Single-use
  - Expiring (24 hours / 3 days / 7 days)
  - Revocable before use
  - Server-side verified
  - Stored as SHA-256 hashes; the full voucher is shown only once when generated
  - Audited with generator, usage, revocation, and timestamp fields

### User Management separation
- New Student requestors are owned by the **FMO / Facilities** side and do not appear in Asset Management Users.
- New Staff and Organization requestors, Approvers, Asset Admins, and Asset Super Admin remain on the **Asset Management** side.
- FMO can still process facility/activity requests submitted by Staff or Organizations, but does not own or edit those Asset-side accounts.
- Write endpoints now re-check account scope, preventing a hand-crafted request from Asset Management from editing an FMO-owned Student account.

### Authorization hardening
- `access_scope` is now enforced by `AssetManagementMiddleware` through `User::canAccessAssetManagement()`.
- A Student account (`scope=fmo`) is blocked from direct Asset Management URLs even if a sidebar link is hidden.
- Legacy accounts without `account_type` / `access_scope` retain the previous role/department fallback behavior for compatibility.

### Database migration
Run after deploying this patch:

```bash
php artisan migrate --force
```

New migration:
`2026_09_06_000017_add_account_types_and_access_vouchers.php`

## Registration UI refinement
- Combined the email send-code and 6-digit verification-code sections into one Step 1.
- Removed the duplicate "enter the same email" field; verification now uses the email stored in the registration session.
- Renumbered registration to 3 steps: Verify Email, Choose Account Access, Finish Account Creation.
- The email in the final account form is now read-only and comes only from the verified email in Step 1.
- Staff, Organization, and Approver voucher registrations now remain Pending Review until Asset Management Admin/Super Admin approval.
- Asset Management admins receive the existing database notification for all pending voucher-created Asset accounts, not only Approvers.

## 2026-09-06 — Voucher accounts active immediately + real account types

- Staff, Organization, and Approver accounts created with a valid Asset Management voucher are now **active immediately** after successful registration.
- Removed the second/pending Asset Management approval step for voucher-created accounts. The voucher issuance is now the authorization step.
- Removed the registration-side "pending approval" notification for Asset Management voucher accounts.
- Existing Staff / Organization / Approver rows that are still pending are activated by migration `2026_09_06_000018_backfill_account_types_and_activate_voucher_accounts.php`.
- Asset Management Users now shows **Account Status: Active / Deactivated** instead of Approved / Pending Review.
- Removed the user-facing **Legacy** value from the Account Type column.
- Existing accounts are backfilled to meaningful account types such as Staff, Approver, Asset Management Admin, Asset Management Super Admin, FMO Staff, FMO Super Admin, Student, or Housekeeping.
- New voucher registrations continue storing the exact type: `staff`, `organization`, or `approver`.
- Important: old requestor records created before account types existed did not store whether they were Staff vs Organization. Facility-only old requestors are classified as Student; remaining old requestors are classified as Staff. New Organization registrations remain correctly identified as Organization.

### Deploy

```bash
php artisan optimize:clear
php artisan migrate --force
```


## Requestor voucher simplification + voucher history deletion
- Removed **Staff** and **Organization** as new Asset Management voucher/account choices.
- Asset Management voucher types are now only **Requestor** and **Approver**.
- Requestor accounts retain OPEX, Requisition, and Activity Proposals access.
- Existing Asset-side Staff/Organization accounts are migrated to `account_type=requestor`.
- Existing Staff/Organization voucher history is migrated to `voucher_type=requestor`.
- Added permanent Voucher History deletion for the **Asset Management Super Admin only**. Regular Asset Management Admins can still generate/revoke vouchers but cannot delete history.
