<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StaffAccountController extends Controller
{
    private function allowedRoles(): array
    {
        return ['admin', 'cashier'];
    }

    public function index()
    {
        $users = User::orderBy('name')->paginate(15);

        return view('staff.index', compact('users'));
    }

    public function passwords()
    {
        abort_unless(auth()->user()?->role === 'super_admin', 403);

        $users = User::whereIn('role', $this->allowedRoles())
            ->orderBy('name')
            ->paginate(15);

        return view('staff.passwords', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($this->allowedRoles())],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff account created successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Super Admin accounts are protected.');
        }

        if ($request->user()->is($user)) {
            return back()->with('error', 'You cannot change your own role here.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in($this->allowedRoles())],
        ]);

        $user->update(['role' => $validated['role']]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff role updated successfully.');
    }

    public function resetPassword(Request $request, User $user)
    {
        abort_unless(
            $request->user()?->role === 'super_admin',
            403,
            'Only Super Admin can use this password management feature.'
        );

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Super Admin passwords cannot be reset here.');
        }

        if ($request->user()->is($user)) {
            return back()->with('error', 'Use your own profile password settings.');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('staff.passwords')
            ->with('success', 'Password reset successfully.')
            ->with('temporary_password', $validated['password'])
            ->with('temporary_password_user', $user->name);
    }

    public function resetGeneratedPassword(Request $request, User $user)
    {
        abort_unless(
            $request->user()?->role === 'super_admin',
            403
        );

        if ($user->role === 'super_admin' || $request->user()->is($user)) {
            return back()->with('error', 'This account is protected.');
        }

        $temporaryPassword = Str::password(16);

        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        return redirect()->route('staff.passwords')
            ->with('success', 'A new temporary password was generated.')
            ->with('temporary_password', $temporaryPassword)
            ->with('temporary_password_user', $user->name);
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()?->role === 'super_admin', 403);

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Super Admin accounts cannot be deleted here.');
        }

        if ($request->user()->is($user)) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff account deleted successfully.');
    }

    public function destroyAll(Request $request)
    {
        abort_unless($request->user()?->role === 'super_admin', 403);

        $deleted = User::whereIn('role', $this->allowedRoles())->delete();

        return redirect()->route('staff.index')
            ->with(
                'success',
                "{$deleted} staff account(s) deleted. Super Admin accounts were preserved."
            );
    }
}
