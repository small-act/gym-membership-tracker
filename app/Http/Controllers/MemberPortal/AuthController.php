<?php

namespace App\Http\Controllers\MemberPortal;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberAccount;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('member-portal.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255', 'lowercase'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $email = mb_strtolower(trim($validated['email']));

        $member = Member::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (
            !$member ||
            $member->status !== 'active' ||
            MemberAccount::where('member_id', $member->id)->exists() ||
            MemberAccount::whereRaw('LOWER(email) = ?', [$email])->exists()
        ) {
            throw ValidationException::withMessages([
                'email' => 'We could not create an account with those details. Check your registered email or contact the gym.',
            ]);
        }

        $account = MemberAccount::create([
            'member_id' => $member->id,
            'email' => $email,
            'password' => $validated['password'],
        ]);

        Auth::guard('member')->login($account);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')
            ->with('success', 'Your member portal account has been created.');
    }

    public function showLogin(): View
    {
        return view('member-portal.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $credentials['email'] = mb_strtolower(trim($credentials['email']));

        if (!Auth::guard('member')->attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => 'The supplied credentials could not be verified.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('member.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login')
            ->with('success', 'You have been logged out.');
    }
}
