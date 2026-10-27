<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipPlanController extends Controller
{
    public function index()
    {
        $plans = MembershipPlan::orderBy('price')->paginate(10);

        return view('membership-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('membership-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:membership_plans,name'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:87600'],
            'duration_unit' => ['required', Rule::in(['days', 'hours'])],
            'is_active' => ['required', Rule::in(['1', '0', 1, 0])],
        ]);

        $validated['is_active'] = (bool) $validated['is_active'];

        MembershipPlan::create($validated);

        return redirect()
            ->route('membership-plans.index')
            ->with('success', 'Membership plan created successfully.');
    }

    public function edit(MembershipPlan $membershipPlan)
    {
        return view('membership-plans.edit', [
            'plan' => $membershipPlan,
        ]);
    }

    public function update(Request $request, MembershipPlan $membershipPlan)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('membership_plans', 'name')->ignore($membershipPlan->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:87600'],
            'duration_unit' => ['required', Rule::in(['days', 'hours'])],
            'is_active' => ['required', Rule::in(['1', '0', 1, 0])],
        ]);

        $validated['is_active'] = (bool) $validated['is_active'];

        $membershipPlan->update($validated);

        return redirect()
            ->route('membership-plans.index')
            ->with('success', 'Membership plan updated successfully.');
    }
}