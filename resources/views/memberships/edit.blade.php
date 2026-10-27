$editView = @'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Membership | Gym Membership Tracker</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 25px; background: #081426; color: #edf4ff; font-family: Arial, sans-serif; }
        main { max-width: 750px; margin: 25px auto; }
        .panel { padding: 25px; background: #0d1e33; border: 1px solid #20344e; border-radius: 12px; }
        label { display: block; margin: 16px 0 7px; color: #cbd5e1; }
        input, select, textarea { width: 100%; padding: 12px; background: #081426; color: white; border: 1px solid #334967; border-radius: 7px; }
        textarea { min-height: 100px; resize: vertical; }
        .button { display: inline-block; margin-top: 18px; padding: 11px 16px; border: 0; border-radius: 8px; background: #2563eb; color: white; text-decoration: none; cursor: pointer; font-weight: bold; }
        .secondary { background: #263b56; }
        .error { margin-top: 15px; padding: 12px; background: #4b2933; color: #fecdd3; border-radius: 8px; }
    </style>
</head>
<body>
<main>
    <a href="{{ route('memberships.index') }}" style="color:#7dd3fc">← Back to Memberships</a>
    <h1>Edit Membership #{{ $membership->id }}</h1>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="panel" method="POST" action="{{ route('memberships.update', $membership) }}">
        @csrf
        @method('PUT')

        <label for="member_id">Member</label>
        <select id="member_id" name="member_id" required>
            @foreach($members as $member)
                <option value="{{ $member->id }}" @selected(old('member_id', $membership->member_id) == $member->id)>
                    {{ $member->full_name }} — {{ $member->member_number }}
                </option>
            @endforeach
        </select>

        <label for="membership_plan_id">Membership Plan</label>
        <select id="membership_plan_id" name="membership_plan_id" required>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" @selected(old('membership_plan_id', $membership->membership_plan_id) == $plan->id)>
                    {{ $plan->name }} — ₱{{ number_format((float) $plan->price, 2) }} ({{ $plan->duration_days }} days)
                </option>
            @endforeach
        </select>

        <label for="start_date">Start Date</label>
        <input id="start_date" type="date" name="start_date"
               value="{{ old('start_date', $membership->start_date->format('Y-m-d')) }}" required>

        <label for="notes">Notes</label>
        <textarea id="notes" name="notes">{{ old('notes', $membership->notes) }}</textarea>

        <button class="button" type="submit">Save Changes</button>
        <a class="button secondary" href="{{ route('memberships.index') }}">Cancel</a>
    </form>
</main>
</body>
</html>
'@

$editPath = ".\resources\views\memberships\edit.blade.php"
New-Item -ItemType Directory -Path (Split-Path $editPath) -Force | Out-Null
$encoding = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText((Join-Path (Get-Location) $editPath), $editView, $encoding)

Write-Host "Membership edit form created." -ForegroundColor Green