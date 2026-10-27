<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Cashier Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #071426;
            color: #f1f5f9;
            margin: 0;
            padding: 30px;
        }
        .panel {
            max-width: 1000px;
            margin: auto;
            background: #10233e;
            padding: 24px;
            border-radius: 12px;
        }
        .user {
            padding: 18px 0;
            border-bottom: 1px solid #334155;
        }
        input, button {
            padding: 10px;
            margin: 5px 4px 5px 0;
            border-radius: 6px;
        }
        button {
            background: #2563eb;
            color: white;
            border: 0;
            cursor: pointer;
        }
        a { color: #93c5fd; }
        .success { color: #86efac; }
        .error { color: #fca5a5; }
    </style>
</head>
<body>
<div class="panel">
    <h1>Cashier Password Reset</h1>
    <p>Set a new password when a cashier forgets theirs.
       Existing passwords are never displayed.</p>

    <p><a href="{{ route('dashboard') }}">Back to Dashboard</a></p>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @forelse ($users as $user)
        <div class="user">
            <h3>{{ $user->name }}</h3>
            <p>{{ $user->email }}</p>

            <form method="POST"
                  action="{{ route('cashier-passwords.update', $user) }}">
                @csrf
                @method('PATCH')

                <input type="password" name="password"
                       placeholder="New password (12+ characters)"
                       minlength="12" autocomplete="new-password" required>

                <input type="password" name="password_confirmation"
                       placeholder="Confirm new password"
                       minlength="12" autocomplete="new-password" required>

                <button type="submit">Reset Password</button>
            </form>
        </div>
    @empty
        <p>No receptionist/cashier accounts were found.</p>
    @endforelse

    {{ $users->links() }}
</div>
</body>
</html>
