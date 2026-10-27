<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MemberAccountController extends Controller
{
    public function index()
    {
        $members = Member::query()
            ->with('memberAccount')
            ->orderBy('full_name')
            ->paginate(15);

        return view('member-accounts.index', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => [
                'required',
                'integer',
                'exists:members,id',
                Rule::unique('member_accounts', 'member_id'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'lowercase',
                'unique:member_accounts,email',
                'unique:users,email',
            ],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $member = Member::findOrFail($validated['member_id']);

        if ($member->status !== 'active') {
            return back()->withInput()->withErrors([
                'member_id' => 'Only active members can receive a portal account.',
            ]);
        }

        DB::transaction(function () use ($validated, $member) {
            MemberAccount::create([
                'member_id' => $member->id,
                'email' => mb_strtolower(trim($validated['email'])),
                'password' => $validated['password'],
            ]);
        });

        return redirect()->route('member-accounts.index')
            ->with('success', 'Member login account created successfully.');
    }

    public function destroy(MemberAccount $memberAccount)
    {
        $memberAccount->delete();

        return redirect()->route('member-accounts.index')
            ->with('success', 'Member login access removed. The member record and history were preserved.');
    }
}