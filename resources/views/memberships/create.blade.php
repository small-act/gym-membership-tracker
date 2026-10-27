<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Membership</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 25px 15px; background: #081426; color: #edf4ff; font-family: Arial, sans-serif; }
        .panel { max-width: 760px; margin: 20px auto; padding: 26px; background: #0d1e33; border: 1px solid #20344e; border-radius: 13px; }
        h1 { margin-top: 0; }
        .muted { color: #91a6c2; font-size: 13px; line-height: 1.6; }
        label { display: block; margin: 18px 0 7px; font-weight: bold; font-size: 13px; }
        input, select, textarea { width: 100%; padding: 12px; background: #081426; color: #edf4ff; border: 1px solid #38516e; border-radius: 7px; font: inherit; }
        textarea { min-height: 85px; resize: vertical; }
        .button { display: inline-block; padding: 12px 16px; background: #2563eb; color: white; border: 0; border-radius: 7px; cursor: pointer; text-decoration: none; font-weight: bold; }
        .secondary { background: #263b56; }
        .error { color: #fecdd3; font-size: 13px; margin-top: 5px; }
        .actions { display: flex; gap: 10px; margin-top: 22px; flex-wrap: wrap; }
        .notice { padding: 13px; margin-top: 15px; background: #172d49; border-radius: 8px; color: #bfd7f7; font-size: 13px; }
    </style>
</head>
<body>
<div class="panel">
    <h1>Register Membership</h1>
    <p class="muted">Select a member and an active plan. The plan price and expiry date will be calculated automatically.</p>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($members->isEmpty())
        <div class="notice">
            No members are registered yet.
            <a href="{{ route('members.create') }}" style="color:#93c5fd">Register a member first.</a>
        </div>
    @elseif($plans->isEmpty())
        <div class="notice">
            No active membership plans are available.
            <a href="{{ route('membership-plans.create') }}" style="color:#93c5fd">Create or activate a plan first.</a>
        </div>
    @else
        <form method="POST" action="{{ route('memberships.store') }}">
            @csrf

            <label for="member_id">Member *</label>
            <select id="member_id" name="member_id" required>
                <option value="">Select a member</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" @selected((string) old('member_id') === (string) $member->id)>
                        {{ $member->full_name }} — {{ $member->member_number }}
                    </option>
                @endforeach
            </select>
            @error('member_id') <div class="error">{{ $message }}</div> @enderror

            <label for="membership_plan_id">Membership Plan *</label>
            <select id="membership_plan_id" name="membership_plan_id" required>
                <option value="">Select a plan</option>
                @foreach($plans as $plan)
                    <option
                        value="{{ $plan->id }}"
                        data-price="{{ $plan->price }}"
                        data-duration="{{ $plan->duration_days }}"
                        @selected((string) old('membership_plan_id') === (string) $plan->id)
                    >
                        {{ $plan->name }} — ₱{{ number_format((float) $plan->price, 2) }} / {{ $plan->duration_days }} days
                    </option>
                @endforeach
            </select>
            @error('membership_plan_id') <div class="error">{{ $message }}</div> @enderror

            <label for="start_date">Start Date *</label>
            <input id="start_date" type="date" name="start_date"
                   value="{{ old('start_date', now()->toDateString()) }}" required>
            @error('start_date') <div class="error">{{ $message }}</div> @enderror

            <div class="notice" id="plan-summary">
                Choose a plan to preview its price and expiry date.
            </div>

            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" maxlength="2000" placeholder="Optional membership notes">{{ old('notes') }}</textarea>
            @error('notes') <div class="error">{{ $message }}</div> @enderror

            <div class="actions">
                <button class="button" type="submit">Register Membership</button>
                <a class="button secondary" href="{{ route('memberships.index') }}">Cancel</a>
            </div>
        </form>

        <script>
            const planSelect = document.getElementById('membership_plan_id');
            const startInput = document.getElementById('start_date');
            const summary = document.getElementById('plan-summary');

            function updateSummary() {
                const option = planSelect.options[planSelect.selectedIndex];
                const price = option?.dataset?.price;
                const duration = Number(option?.dataset?.duration);
                const start = startInput.value;

                if (!price || !duration || !start) {
                    summary.textContent = 'Choose a plan and start date to preview its price and expiry date.';
                    return;
                }

                const date = new Date(start + 'T12:00:00');
                date.setDate(date.getDate() + duration - 1);

                const expiry = date.toLocaleDateString(undefined, {
                    year: 'numeric', month: 'short', day: 'numeric'
                });

                summary.textContent =
                    'Plan price: ₱' + Number(price).toLocaleString('en-PH', {
                        minimumFractionDigits: 2, maximumFractionDigits: 2
                    }) + ' · Duration: ' + duration + ' days · Expiry date: ' + expiry;
            }

            planSelect.addEventListener('change', updateSummary);
            startInput.addEventListener('change', updateSummary);
            updateSummary();
        </script>
    @endif
</div>
</body>
</html>
