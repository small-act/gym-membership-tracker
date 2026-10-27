<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SystemResetController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()?->role === 'super_admin',
            403,
            'Only the Super Admin can reset system data.'
        );

        return view('system-reset.index');
    }

    public function store(Request $request)
    {
        abort_unless(
            $request->user()?->role === 'super_admin',
            403,
            'Only the Super Admin can reset system data.'
        );

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'confirmation' => ['required', 'in:RESET'],
        ]);

        if (!Hash::check(
            $validated['current_password'],
            $request->user()->password
        )) {
            throw ValidationException::withMessages([
                'current_password' =>
                    'The current password is incorrect. No data was deleted.',
            ]);
        }

        DB::transaction(function () {
            // Delete dependent records before their parent records.
            DB::table('payments')->delete();
            DB::table('attendances')->delete();

            // These accounts are linked to members and cannot remain
            // after their associated member records are deleted.
            DB::table('member_accounts')->delete();

            DB::table('memberships')->delete();
            DB::table('members')->delete();
            DB::table('membership_plans')->delete();

            // Reset SQLite auto-increment counters if the table exists.
            $sequenceTableExists = DB::selectOne(
                "SELECT name
                 FROM sqlite_master
                 WHERE type = 'table'
                   AND name = 'sqlite_sequence'"
            );

            if ($sequenceTableExists) {
                DB::table('sqlite_sequence')
                    ->whereIn('name', [
                        'payments',
                        'attendances',
                        'member_accounts',
                        'memberships',
                        'members',
                        'membership_plans',
                    ])
                    ->delete();
            }
        });

        return redirect()
            ->route('system-reset.index')
            ->with(
                'success',
                'System reset completed. Member, member-login, membership, attendance, payment, and membership-plan records were cleared. Staff and user accounts were preserved.'
            );
    }
}
