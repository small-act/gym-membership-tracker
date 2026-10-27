<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::with(['member', 'plan'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('memberships.index', compact('memberships'));
    }

    public function create()
    {
        $members = Member::orderBy('full_name')->get();

        $plans = MembershipPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('memberships.create', compact('members', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => [
                'required',
                Rule::exists('members', 'id'),
            ],
            'membership_plan_id' => [
                'required',
                Rule::exists('membership_plans', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'start_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $plan = MembershipPlan::where('is_active', true)
            ->findOrFail($validated['membership_plan_id']);

        $startDate = \Illuminate\Support\Carbon::parse(
            $validated['start_date']
        )->startOfDay();

        // The start date counts as day one.
        $endDate = $startDate->copy()
            ->addDays(max(0, (int) $plan->duration_days - 1));

        Membership::create([
            'member_id' => $member->id,
            'membership_plan_id' => $plan->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'price' => $plan->price,
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('memberships.index')
            ->with('success', 'Membership registered successfully.');
    }

    public function renew(Membership $membership)
    {
        $membership->load(['member', 'plan']);

        if (!$membership->member || !$membership->plan) {
            return redirect()
                ->route('memberships.index')
                ->with('error', 'The member or plan could not be found.');
        }

        if (!$membership->plan->is_active) {
            return redirect()
                ->route('memberships.index')
                ->with('error', 'This plan is inactive. Activate it or select another plan before renewing.');
        }

        $today = now()->startOfDay();

        // Continue after the current end date if it has not expired.
        $nextStart = $membership->end_date->copy()->startOfDay();

        if ($nextStart->lt($today)) {
            $nextStart = $today->copy();
        } else {
            $nextStart->addDay();
        }

        $nextEnd = $nextStart->copy()
            ->addDays(max(0, (int) $membership->plan->duration_days - 1));

        Membership::create([
            'member_id' => $membership->member_id,
            'membership_plan_id' => $membership->membership_plan_id,
            'start_date' => $nextStart->toDateString(),
            'end_date' => $nextEnd->toDateString(),
            'price' => $membership->plan->price,
            'status' => 'active',
            'notes' => 'Renewal of membership #' . $membership->id,
        ]);

        return redirect()
            ->route('memberships.index')
            ->with('success', 'Membership renewed. The previous record was preserved.');
    }

    public function edit(Membership $membership)
    {
        $members = Member::orderBy('full_name')->get();

        $plans = MembershipPlan::where('is_active', true)
            ->orWhere('id', $membership->membership_plan_id)
            ->orderBy('price')
            ->get();

        return view('memberships.edit', compact('membership', 'members', 'plans'));
    }

    public function update(Request $request, Membership $membership)
    {
        $validated = $request->validate([
            'member_id' => ['required', Rule::exists('members', 'id')],
            'membership_plan_id' => [
                'required',
                Rule::exists('membership_plans', 'id'),
            ],
            'start_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $plan = MembershipPlan::findOrFail($validated['membership_plan_id']);

        $startDate = \Illuminate\Support\Carbon::parse(
            $validated['start_date']
        )->startOfDay();

        $endDate = $startDate->copy()
            ->addDays(max(0, (int) $plan->duration_days - 1));

        $membership->update([
            'member_id' => $validated['member_id'],
            'membership_plan_id' => $plan->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'price' => $plan->price,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('memberships.index')
            ->with('success', 'Membership updated successfully.');
    }

    public function destroy(Membership $membership)
    {
        try {
            $membership->delete();

            return redirect()
                ->route('memberships.index')
                ->with('success', 'Membership deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return redirect()
                ->route('memberships.index')
                ->with(
                    'error',
                    'This membership could not be deleted because related records may exist. No related records were intentionally deleted.'
                );
        }
    }
}
