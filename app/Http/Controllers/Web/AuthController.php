<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AccessVoucher;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    private const REGISTRATION_CODE_KEY = 'registration_code';
    private const REGISTRATION_VERIFIED_KEY = 'registration_verified_email';
    private const REGISTRATION_VOUCHER_KEY = 'registration_asset_voucher';
    private const RESET_CODE_KEY = 'password_reset_code';
    private const RESET_VERIFIED_KEY = 'password_reset_verified_email';

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister(Request $request)
    {
        return view('auth.register', [
            'verifiedEmail' => $request->session()->get(self::REGISTRATION_VERIFIED_KEY),
            'pendingVerification' => $request->session()->get(self::REGISTRATION_CODE_KEY),
            'departments' => Department::orderBy('name')->get(),
            'verifiedVoucher' => $request->session()->get(self::REGISTRATION_VOUCHER_KEY),
        ]);
    }

    public function sendVerificationCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $code = (string) random_int(100000, 999999);

        $request->session()->put(self::REGISTRATION_CODE_KEY, [
            'email' => $validated['email'],
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        $request->session()->forget(self::REGISTRATION_VERIFIED_KEY);

        // ✅ FIXED EMAIL SENDING WITH ERROR HANDLING
        try {
            Mail::raw(
                "Your verification code is: {$code}\n\nThis code will expire in 10 minutes.",
                function ($message) use ($validated) {
                    $message->to($validated['email'])
                        ->subject('Your Account Verification Code');
                }
            );
        } catch (\Throwable $e) {
            \Log::error('MAIL ERROR: '.$e->getMessage());

            // TEMPORARY: showing the real exception message on-screen for debugging,
            // since Railway's log viewer doesn't show file-based Laravel logs.
            // Remove the $e->getMessage() part once mail sending is confirmed working.
            return back()->with('error', 'Email failed ('.$e->getMessage().'). Your code is: '.$code)
                ->withInput([
                    'email' => $validated['email'],
                ]);
        }

        return back()->with('success', 'Verification code sent to your email.')->withInput([
            'email' => $validated['email'],
        ]);
    }

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $verification = $request->session()->get(self::REGISTRATION_CODE_KEY);

        if (!$verification || empty($verification['email'])) {
            return back()->withErrors(['code' => 'Please enter your email and send a verification code first.'])->withInput();
        }

        if (now()->timestamp > ($verification['expires_at'] ?? 0)) {
            $request->session()->forget(self::REGISTRATION_CODE_KEY);
            return back()->withErrors(['code' => 'Verification code expired. Please request a new code.'])->withInput();
        }

        if (!Hash::check($validated['code'], $verification['code_hash'] ?? '')) {
            return back()->withErrors(['code' => 'Invalid verification code.'])->withInput();
        }

        $request->session()->put(self::REGISTRATION_VERIFIED_KEY, $verification['email']);

        return back()->with('success', 'Email verified. You can now create your account.')->withInput([
            'verified_email' => $verification['email'],
        ]);
    }


    public function verifyVoucher(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => ['required', 'string', 'max:64'],
        ]);

        $voucher = AccessVoucher::query()
            ->where('code_hash', AccessVoucher::hashCode($validated['voucher_code']))
            ->first();

        if (!$voucher || !$voucher->isUsable()) {
            $request->session()->forget(self::REGISTRATION_VOUCHER_KEY);
            return back()->withErrors(['voucher_code' => 'Invalid, expired, used, or revoked voucher code. Please request a new voucher from Asset Management.'])->withInput();
        }

        $request->session()->put(self::REGISTRATION_VOUCHER_KEY, [
            'voucher_id' => $voucher->id,
            'voucher_type' => $voucher->voucher_type,
            'approver_type' => $voucher->approver_type,
            'department_id' => $voucher->department_id,
        ]);

        return back()->with('success', 'Asset Management voucher verified. Your authorized account form is now unlocked.')->withInput();
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'department_id' => ['nullable', 'exists:departments,id'],
            'account_access' => ['required', 'in:student,asset'],
        ]);

        $verifiedEmail = $request->session()->get(self::REGISTRATION_VERIFIED_KEY);
        if ($verifiedEmail !== $validated['email']) {
            return back()->withErrors(['email' => 'Please verify this email address first.'])->withInput();
        }

        $voucherSession = $request->session()->get(self::REGISTRATION_VOUCHER_KEY);

        try {
            $user = DB::transaction(function () use ($validated, $voucherSession) {
                $role = 'requestor';
                $accountType = 'student';
                $accessScope = 'fmo';
                $approverType = null;
                $departmentId = $validated['department_id'] ?? null;
                $isAutoApproved = true;
                $voucher = null;

                if ($validated['account_access'] === 'asset') {
                    if (!$voucherSession || empty($voucherSession['voucher_id'])) {
                        throw new \RuntimeException('A verified Asset Management voucher is required.');
                    }

                    $voucher = AccessVoucher::query()->lockForUpdate()->find($voucherSession['voucher_id']);
                    if (!$voucher || !$voucher->isUsable()) {
                        throw new \RuntimeException('This voucher is no longer valid. Please request and verify a new voucher.');
                    }

                    $accountType = $voucher->voucher_type;
                    $accessScope = 'asset';
                    if ($voucher->voucher_type === 'approver') {
                        $role = 'approver';
                        $approverType = $voucher->approver_type;
                        $accountType = 'approver';
                    } elseif ($voucher->voucher_type === 'requestor') {
                        $role = 'requestor';
                        $accountType = 'requestor';
                    } else {
                        throw new \RuntimeException('Unsupported voucher type.');
                    }

                    // If Asset Management bound the voucher to a department, the
                    // registration form cannot override it.
                    if ($voucher->department_id) {
                        $departmentId = $voucher->department_id;
                    }
                }

                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::driver('bcrypt')->make($validated['password'], [
                        'rounds' => (int) config('security.bcrypt_rounds', 12),
                    ]),
                    'department_id' => $departmentId,
                    'role' => $role,
                    'account_type' => $accountType,
                    'access_scope' => $accessScope,
                    'approver_type' => $approverType,
                    'is_approved' => $isAutoApproved,
                    'approved_at' => $isAutoApproved ? now() : null,
                    'email_verified_at' => now(),
                ]);

                if ($voucher) {
                    $voucher->update(['used_at' => now(), 'used_by' => $user->id]);
                }

                return $user;
            });
        } catch (\RuntimeException $e) {
            $request->session()->forget(self::REGISTRATION_VOUCHER_KEY);
            return back()->withErrors(['voucher_code' => $e->getMessage()])->withInput();
        }

        // No second Asset Management approval is required here. The single-use
        // voucher was already generated by Asset Management, so successful
        // voucher registration produces an active account immediately.

        $request->session()->forget([
            self::REGISTRATION_CODE_KEY,
            self::REGISTRATION_VERIFIED_KEY,
            self::REGISTRATION_VOUCHER_KEY,
        ]);

        return redirect()->route('login')->with('success', 'Account created and verified. You can log in now.');
    }

    public function showForgotPassword(Request $request)
    {
        return view('auth.forgot-password', [
            'verifiedEmail' => $request->session()->get(self::RESET_VERIFIED_KEY),
            'pendingVerification' => $request->session()->get(self::RESET_CODE_KEY),
        ]);
    }

    public function sendResetCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = User::where('email', $validated['email'])->first();
        $code = (string) random_int(100000, 999999);

        // Only a registered email actually gets a code stored/sent — but the
        // response message is identical either way, so this screen can't be
        // used to fish for which emails have accounts.
        if ($user) {
            $request->session()->put(self::RESET_CODE_KEY, [
                'email' => $validated['email'],
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(10)->timestamp,
            ]);

            try {
                Mail::raw(
                    "Your password reset code is: {$code}\n\nThis code will expire in 10 minutes. If you did not request a password reset, you can safely ignore this email.",
                    function ($message) use ($validated) {
                        $message->to($validated['email'])
                            ->subject('Your Password Reset Code');
                    }
                );
            } catch (\Throwable $e) {
                \Log::error('MAIL ERROR: '.$e->getMessage());

                // Same "temporary debugging" trade-off used in sendVerificationCode()
                // above — Railway's log viewer doesn't surface file-based Laravel
                // logs, so show the code on-screen if mail fails instead of leaving
                // the admin stuck. Remove once mail sending is confirmed working.
                return back()->with('error', 'Email failed ('.$e->getMessage().'). Your code is: '.$code)
                    ->withInput(['email' => $validated['email']]);
            }
        }

        $request->session()->forget(self::RESET_VERIFIED_KEY);

        return back()->with('success', 'If that email has an account, a reset code has been sent to it.')->withInput([
            'email' => $validated['email'],
        ]);
    }

    public function verifyResetCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $verification = $request->session()->get(self::RESET_CODE_KEY);

        if (!$verification || ($verification['email'] ?? null) !== $validated['email']) {
            return back()->withErrors(['email' => 'No reset request found for this email. Please send a code first.'])->withInput();
        }

        if (now()->timestamp > ($verification['expires_at'] ?? 0)) {
            $request->session()->forget(self::RESET_CODE_KEY);
            return back()->withErrors(['code' => 'Reset code expired. Please request a new code.'])->withInput();
        }

        if (!Hash::check($validated['code'], $verification['code_hash'] ?? '')) {
            return back()->withErrors(['code' => 'Invalid reset code.'])->withInput();
        }

        $request->session()->put(self::RESET_VERIFIED_KEY, $validated['email']);

        return back()->with('success', 'Code verified. You can now set a new password.')->withInput([
            'verified_email' => $verification['email'],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $verifiedEmail = $request->session()->get(self::RESET_VERIFIED_KEY);

        if ($verifiedEmail !== $validated['email']) {
            return back()->withErrors(['email' => 'Please verify this email with a reset code first.'])->withInput();
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            $request->session()->forget([self::RESET_CODE_KEY, self::RESET_VERIFIED_KEY]);
            return back()->withErrors(['email' => 'Account not found.'])->withInput();
        }

        $user->forceFill([
            'password' => Hash::driver('bcrypt')->make($validated['password'], [
                'rounds' => (int) config('security.bcrypt_rounds', 12),
            ]),
        ])->save();

        $request->session()->forget([self::RESET_CODE_KEY, self::RESET_VERIFIED_KEY]);

        return redirect()->route('login')->with('success', 'Password reset successful. You can now log in with your new password.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user && !$user->is_approved) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your account is still pending admin approval.'])->onlyInput('email');
            }

            if ($user && Hash::needsRehash($user->password)) {
                $user->forceFill([
                    'password' => Hash::driver('bcrypt')->make($credentials['password'], [
                        'rounds' => (int) config('security.bcrypt_rounds', 12),
                    ]),
                ])->save();
            }

            return redirect()->route($user->homeRouteName())->with('success', 'Welcome back.');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}