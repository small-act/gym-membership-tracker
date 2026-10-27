<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#081426">
    <title>Register Member | Gym Membership Tracker</title>

    <style>
        
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            background: #081426;
            color: #edf4ff;
            font-family: Arial, sans-serif;
        }

        .panel {
            max-width: 900px;
            margin: 25px auto;
            padding: 28px;
            background: #11213a;
            border: 1px solid #263a56;
            border-radius: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #b8c8dc;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            background: #081426;
            color: white;
            border: 1px solid #334967;
            border-radius: 8px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: 2px solid #1677e8;
            outline-offset: 1px;
        }

        textarea {
            min-height: 85px;
            resize: vertical;
        }

        .field {
            margin-bottom: 16px;
        }

        .btn {
            display: inline-block;
            padding: 12px 16px;
            background: #1677e8;
            color: white;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .secondary {
            background: #263a56;
        }

        .error {
            margin-bottom: 18px;
            padding: 12px;
            color: #fda4af;
            background: #3b1725;
            border-radius: 8px;
        }

        .required {
            color: #fda4af;
        }

        @media (max-width: 600px) {
            .grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            body {
                padding: 12px;
            }

            .panel {
                padding: 18px;
                margin: 10px auto;
            }
            
        }
    </style>
</head>

<body>
<div class="panel">
    <a class="btn secondary" href="{{ route('members.index') }}">
        &larr; Back to Members
    </a>

    <h1>Register Member</h1>
    <p>Enter the member's personal and contact information.</p>

    @if ($errors->any())
        <div class="error">
            <strong>Please correct the following errors:</strong>

            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('members.store') }}">
        @csrf

        <div class="grid">
            <div class="field">
                <label for="full_name">
                    Full Name <span class="required">*</span>
                </label>
                <input
                    id="full_name"
                    type="text"
                    name="full_name"
                    value="{{ old('full_name') }}"
                    maxlength="255"
                    autocomplete="name"
                    required
                >
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    maxlength="255"
                    autocomplete="email"
                >
                
<!-- <div class="field full" style="margin-top: 12px;">
    <label style="display:flex; align-items:center; gap:10px;">
        <input
            type="checkbox"
            name="create_account"
            value="1"
            {{ old('create_account') ? 'checked' : '' }}
            onchange="document.getElementById('member-account-fields').hidden = !this.checked;"
            style="width:auto;"
        >
        Create a Member Portal Login Account
    </label> -->

    <!-- <small>
        Enable this option if the member should be able to log in
        and view their own membership and attendance information.
    </small>
</div> -->

<div
    id="member-account-fields"
    class="field full"
    style="margin-top:12px;"
    {{ old('create_account') ? '' : 'hidden' }}
>
    <div class="grid">
        <div class="field">
            <label for="password">Member Login Password</label>
            <input
                id="password"
                type="password"
                name="password"
                minlength="8"
                autocomplete="new-password"
                {{ old('create_account') ? 'required' : '' }}
            >
            @error('password')
                <small style="color:#dc2626;">{{ $message }}</small>
            @enderror
            <small>Use at least 8 characters.</small>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                minlength="8"
                autocomplete="new-password"
                {{ old('create_account') ? 'required' : '' }}
            >
        </div>
    </div>

    @error('email')
        <small style="color:#dc2626;">{{ $message }}</small>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.querySelector('[name="create_account"]');
        const fields = document.getElementById('member-account-fields');
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');

        function updateAccountFields() {
            fields.hidden = !checkbox.checked;
            password.required = checkbox.checked;
            confirmation.required = checkbox.checked;
        }

        checkbox.addEventListener('change', updateAccountFields);
        updateAccountFields();
    });
</script>

            </div>

            <div class="field">
                <label for="phone">Phone</label>
                <input
                    id="phone"
                    type="tel"
                    name="phone"
                    value="{{ old('phone') }}"
                    maxlength="30"
                    autocomplete="tel"
                >
            </div>

            <div class="field">
                <label for="date_of_birth">Date of Birth</label>
                <input
                    id="date_of_birth"
                    type="date"
                    name="date_of_birth"
                    value="{{ old('date_of_birth') }}"
                    max="{{ now()->subDay()->toDateString() }}"
                >
            </div>

            <div class="field">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">Prefer not to say</option>

                    @foreach (['Male', 'Female', 'Other'] as $gender)
                        <option
                            value="{{ $gender }}"
                            @selected(old('gender') === $gender)
                        >
                            {{ $gender }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="emergency_contact">Emergency Contact</label>
                <input
                    id="emergency_contact"
                    type="text"
                    name="emergency_contact"
                    value="{{ old('emergency_contact') }}"
                    maxlength="255"
                >
            </div>
        </div>

        <div class="field">
            <label for="address">Address</label>
            <textarea
                id="address"
                name="address"
                maxlength="2000"
            >{{ old('address') }}</textarea>
        </div>

        <div class="field">
            <label for="status">
                Member Status <span class="required">*</span>
            </label>

            <select id="status" name="status" required>
                <option
                    value="active"
                    @selected(old('status', 'active') === 'active')
                >
                    Active
                </option>

                <option
                    value="inactive"
                    @selected(old('status') === 'inactive')
                >
                    Inactive
                </option>
            </select>
        </div>

        <button class="btn" type="submit">Save Member</button>

        <a class="btn secondary" href="{{ route('members.index') }}">
            Cancel
        </a>
    </form>
</div>
</body>
</html>
