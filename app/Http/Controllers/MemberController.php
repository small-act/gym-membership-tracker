<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $members = Member::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('member_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('members.index', compact('members', 'search'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $createAccount = $request->boolean('create_account');

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                $createAccount ? 'required' : 'nullable',
                'email',
                'max:255',
                'unique:members,email',
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'address' => ['nullable', 'string', 'max:2000'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'create_account' => ['nullable', 'boolean'],
            'password' => [
                Rule::requiredIf($createAccount),
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        if (
            $createAccount &&
            MemberAccount::where('email', $validated['email'])->exists()
        ) {
            throw ValidationException::withMessages([
                'email' => 'A member login account already uses this email address.',
            ]);
        }

        $memberData = [
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'status' => $validated['status'],
            'member_number' => $this->generateMemberNumber(),
        ];

        $member = DB::transaction(function () use (
            $memberData,
            $createAccount,
            $validated
        ) {
            $member = Member::create($memberData);

            if ($createAccount) {
                MemberAccount::create([
                    'member_id' => $member->id,
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]);
            }

            return $member;
        });

        $message = "Member {$member->member_number} registered successfully.";

        if ($createAccount) {
            $message .= ' Their member portal login account was also created.';
        }

        return redirect()->route('members.index')->with('success', $message);
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('members', 'email')->ignore($member->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'address' => ['nullable', 'string', 'max:2000'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $member->update($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Member information updated successfully.');
    }

    public function destroy(Member $member)
    {
        try {
            DB::transaction(function () use ($member) {
                MemberAccount::where('member_id', $member->id)->delete();
                $member->delete();
            });

            return redirect()
                ->route('members.index')
                ->with('success', 'Member deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return redirect()
                ->route('members.index')
                ->with(
                    'error',
                    'This member could not be deleted because related records may exist. The deletion was rolled back. Consider deactivating the member instead.'
                );
        }
    }

    private function generateMemberNumber(): string
    {
        do {
            $number = 'GYM-' . now()->format('Ymd') . '-' .
                strtoupper(bin2hex(random_bytes(3)));
        } while (Member::where('member_number', $number)->exists());

        return $number;
    }
}
