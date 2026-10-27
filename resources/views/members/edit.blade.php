<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Member | Gym Membership Tracker</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 25px; background: #081426; color: #edf4ff; font-family: Arial,sans-serif; }
        .panel { max-width: 900px; margin: 25px auto; padding: 28px; background: #11213a; border: 1px solid #263a56; border-radius: 14px; }
        .grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 18px; }
        label { display: block; margin-bottom: 7px; color: #b8c8dc; }
        input,select,textarea { width: 100%; padding: 12px; background: #081426; color: white; border: 1px solid #334967; border-radius: 8px; }
        textarea { min-height: 85px; }
        .field { margin-bottom: 16px; }
        .btn { display: inline-block; padding: 12px 16px; background: #1677e8; color: white; border: 0; border-radius: 8px; cursor: pointer; text-decoration: none; }
        .secondary { background: #263a56; }
        .error { color: #fda4af; }
        @media(max-width:600px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="panel">
    <a href="{{ route('members.index') }}">← Back to Members</a>
    <h1>Edit Member</h1>
    <p>Member Number: <strong>{{ $member->member_number }}</strong></p>

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('members.update', $member) }}">
        @csrf
        @method('PUT')

        <div class="grid">
            <div class="field">
                <label>Full Name *</label>
                <input name="full_name" value="{{ old('full_name', $member->full_name) }}" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $member->email) }}">
            </div>
            <div class="field">
                <label>Phone</label>
                <input name="phone" value="{{ old('phone', $member->phone) }}">
            </div>
            <div class="field">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}" max="{{ now()->subDay()->toDateString() }}">
            </div>
            <div class="field">
                <label>Gender</label>
                <select name="gender">
                    <option value="">Prefer not to say</option>
                    @foreach(['Male','Female','Other'] as $gender)
                        <option value="{{ $gender }}" @selected(old('gender', $member->gender) === $gender)>{{ $gender }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Emergency Contact</label>
                <input name="emergency_contact" value="{{ old('emergency_contact', $member->emergency_contact) }}">
            </div>
        </div>

        <div class="field">
            <label>Address</label>
            <textarea name="address">{{ old('address', $member->address) }}</textarea>
        </div>

        <div class="field">
            <label>Status *</label>
            <select name="status" required>
                <option value="active" @selected(old('status', $member->status) === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $member->status) === 'inactive')>Inactive</option>
            </select>
        </div>

        <button class="btn" type="submit">Update Member</button>
        <a class="btn secondary" href="{{ route('members.index') }}">Cancel</a>
    </form>
</div>
</body>
</html>
