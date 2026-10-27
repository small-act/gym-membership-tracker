<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Password Management | Gym Tracker</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            background: #071426;
            color: #eaf1fb;
            font-family: Arial, sans-serif;
        }
        main {
            max-width: 1050px;
            margin: auto;
        }
        .panel {
            background: #0d2038;
            border: 1px solid #213b59;
            border-radius: 12px;
            padding: 22px;
            margin: 20px 0;
        }
        .muted { color: #93a8c3; line-height: 1.6; }
        .user {
            padding: 18px 0;
            border-bottom: 1px solid #213b59;
        }
        input {
            width: 100%;
            max-width: 320px;
            padding: 11px;
            margin: 5px 0;
            border: 1px solid #304967;
            border-radius: 7px;
            background: #08182b;
            color: white;
        }
        button, .btn {
            display: inline-block;
            padding: 10px 14px;
            margin: 5px 3px 5px 0;
            border: 0;
            border-radius: 7px;
            background: #2876d2;
            color: white;
            cursor: pointer;
            text-decoration: none;
        }
        .secondary { background: #253a55; }
        .danger { background: #b42336; }
        .success {
            padding: 14px;
            border-radius: 8px;
            background: #123b32;
            color: #aaf2d6;
            overflow-wrap: anywhere;
        }
        .error { color: #ffc3cb; }
        .warning {
            padding: 12px;
            background: #332c19;
            color: #fde68a;
            border-radius: 8px;
        }
        .password-output {
            display: block;
            padding: 12px;
            margin: 10px 0;
            background: #08182b;
            border: 1px solid #304967;
            border-radius: 7px;
            overflow-wrap: anywhere;
            font-family: Consolas, monospace;
        }
    </style>
</head>
<body>
<main>
    <a class="btn secondary" href="{{ route('staff.index') }}">
        Back to Staff Management
    </a>
    <a class="btn secondary" href="{{ route('dashboard') }}">
        Dashboard
    </a>

    <h1>Staff Password Management</h1>
    <p class="muted">
        Super Admin only. Reset forgotten passwords or generate a new
        temporary password for an eligible staff account.
    </p>

    @if(session('success'))
        <div class="success">
            <strong>{{ session('success') }}</strong>

            @if(session('temporary_password'))
                <p>
                    New password for
                    <strong>{{ session('temporary_password_user') }}</strong>:
                </p>

                <code class="password-output" id="temporaryPassword">{{ session('temporary_password') }}</code>

                <button type="button" onclick="copyTemporaryPassword()">
                    Copy New Password
                </button>

                <p class="warning">
                    Copy and provide this password securely now. It will not
                    be available on this page after this confirmation expires.
                    Ask the staff member to change it after signing in.
                </p>
            @endif
        </div>
    @endif

    @if(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="panel">
        <h2>Staff Accounts</h2>
        <p class="muted">
            Existing passwords cannot be viewed. You can set a password
            or generate a new temporary one.
        </p>

        @forelse($users as $user)
            <div class="user">
                <h3>{{ $user->name }}</h3>
                <p class="muted">
                    {{ $user->email }} ·
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </p>

                <form method="POST"
                      action="{{ route('staff.reset-password', $user) }}"
                      onsubmit="return confirm('Reset this staff member password to the one you entered?');">
                    @csrf
                    @method('PATCH')

                    <input type="password" name="password"
                           placeholder="New password (minimum 12 characters)"
                           minlength="12" autocomplete="new-password" required>

                    <input type="password" name="password_confirmation"
                           placeholder="Confirm new password"
                           minlength="12" autocomplete="new-password" required>

                    <button type="submit">Set New Password</button>
                </form>

                <form method="POST"
                      action="{{ route('staff.generate-password', $user) }}"
                      onsubmit="return confirm('Generate a new temporary password for {{ addslashes($user->name) }}? Their current password will stop working.');">
                    @csrf
                    @method('PATCH')
                    <button class="danger" type="submit">
                        Generate Temporary Password
                    </button>
                </form>
            </div>
        @empty
            <p>No Admin, Cashier, or Receptionist accounts found.</p>
        @endforelse

        <div style="margin-top:20px">
            {{ $users->links() }}
        </div>
    </div>
</main>

<script>
function copyTemporaryPassword() {
    const element = document.getElementById('temporaryPassword');
    if (!element) return;

    const password = element.textContent;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(password)
            .then(() => alert('New password copied. Store it securely.'))
            .catch(() => alert('Copy failed. Please select and copy the password manually.'));
    } else {
        alert('Please select and copy the displayed password manually.');
    }
}
</script>
</body>
</html>
