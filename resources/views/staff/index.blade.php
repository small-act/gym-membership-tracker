
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071426">
    <title>Management Center | Gym Tracker</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #071426;
            color: #eaf1fb;
            font-family: Arial, sans-serif;
        }

        main {
            max-width: 1200px;
            margin: auto;
            padding: 28px 18px;
        }

        h1 { margin: 22px 0 8px; }
        h2 { font-size: 18px; margin-top: 0; }
        .muted { color: #93a8c3; font-size: 13px; line-height: 1.6; }

        .panel {
            background: #0d2038;
            border: 1px solid #213b59;
            border-radius: 13px;
            padding: 20px;
            margin: 22px 0;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: end;
            flex-wrap: wrap;
            gap: 16px;
        }

        .category-box { width: min(330px, 100%); }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #c5d4e7;
            font-size: 13px;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #304967;
            border-radius: 8px;
            background: #08182b;
            color: white;
            margin-bottom: 8px;
        }

        button, .btn {
            display: inline-block;
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: #2876d2;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }

        button:hover, .btn:hover { background: #3688e8; }
        .danger { background: #b42336; }
        .danger:hover { background: #d12e43; }
        .secondary { background: #253a55; }
        .secondary:hover { background: #304967; }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-top: 16px;
        }

        .success { background: #123b32; color: #aaf2d6; }
        .error { background: #451f2a; color: #ffc3cb; }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th, td {
            padding: 13px 10px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid #213750;
            font-size: 13px;
        }

        th { color: #9fb5d0; }

        .role-form {
            display: flex;
            gap: 8px;
            align-items: center;
            min-width: 210px;
        }

        .role-form select { min-width: 120px; }

        .badge {
            display: inline-block;
            background: #253a55;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            text-transform: capitalize;
        }

        .role-cashier { background: #174b58; color: #a5f3fc; }
        .role-admin { background: #263e72; color: #bfdbfe; }
        .role-receptionist { background: #493b21; color: #fde68a; }
        .role-super_admin { background: #493b21; color: #fde68a; }

        details summary {
            cursor: pointer;
            color: #93c5fd;
            margin-bottom: 10px;
        }

        .action-form { min-width: 190px; }
        .action-form button { margin-top: 4px; }
        .pagination { margin-top: 18px; }

        .notice {
            border-left: 3px solid #2876d2;
            padding: 10px 13px;
            background: #08182b;
            color: #b8cce5;
            font-size: 13px;
            line-height: 1.6;
            border-radius: 5px;
        }

        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
            .role-form { align-items: stretch; flex-direction: column; }
            .role-form select { min-width: 0; }
            .panel { padding: 15px; }
        }
    </style>
</head>
<body>
<main>
    <div class="topbar">
        <div>
            <a class="btn secondary" href="{{ route('dashboard') }}">
                &#8592; Dashboard
            </a>
            <a class="btn" href="{{ route('member-accounts.index') }}">
                Member Accounts
            </a>
            <h1>Management Center</h1>
            <p class="muted">
                Manage members, staff, and administrator accounts.
            </p>
        </div>

        <div class="category-box">
            <label for="managementCategory">Management Category</label>
       <select id="managementCategory"
        onchange="if (this.value) window.location.href = this.value;">
    <!-- <option value="{{ route('members.index') }}">
        Members Management
    </option> -->
    <option value="{{ route('staff.index') }}" selected>
        Staff &amp; Admin Management
    </option>
    <option value="{{ route('staff.passwords') }}">
        Staff Password Management
    </option>
</select>
        </div>
    </div>

    @if(session('success'))
        <div class="message success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="message error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="message error" role="alert">
            <strong>Please correct these errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="panel">
        <h2>Create Staff or Admin Account</h2>
        <p class="muted">
            Create an account for an Admin or Cashier. Use a strong temporary
            password of at least 12 characters and provide it securely.
        </p>

        <form method="POST" action="{{ route('staff.store') }}">
            @csrf

            <div class="grid">
                <div>
                    <label for="name">Full name *</label>
                    <input id="name" type="text" name="name"
                           value="{{ old('name') }}" required maxlength="255"
                           autocomplete="name">
                </div>

                <div>
                    <label for="email">Email address *</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}" required maxlength="255"
                           autocomplete="email">
                </div>

                <div>
                    <label for="role">Account role *</label>
                    <select id="role" name="role" required>
                        <option value="admin"
                            @selected(old('role') === 'admin')>
                            Admin
                        </option>
                        <option value="cashier"
                            @selected(old('role', 'cashier') === 'cashier')>
                            Cashier
                        </option>
                        <!-- <option value="receptionist"
                            @selected(old('role') === 'receptionist')>
                            Receptionist (legacy)
                        </option> -->
                    </select>
                </div>

                <div>
                    <label for="password">Initial password *</label>
                    <input id="password" type="password" name="password"
                           required minlength="12"
                           autocomplete="new-password">
                </div>

                <div>
                    <label for="password_confirmation">Confirm password *</label>
                    <input id="password_confirmation" type="password"
                           name="password_confirmation" required minlength="12"
                           autocomplete="new-password">
                </div>

                <div style="align-self:end">
                    <button type="submit">Create Account</button>
                </div>
            </div>
        </form>
    </section>

    <section class="panel">
        <h2>Existing Staff Accounts</h2>

        <p class="notice">
            Passwords are not displayed. Use Reset Password to set a new
            password. Super Admin accounts and your own account are protected
            from the actions below.
        </p>

        @if(auth()->user()->role === 'super_admin')
            <form method="POST"
                  action="{{ route('staff.delete-all') }}"
                  onsubmit="return confirm('WARNING: This permanently deletes all eligible Admin, Receptionist, and Cashier accounts. This cannot be undone. Continue?');"
                  style="margin:16px 0 22px;">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger">
                    Delete All Staff Accounts
                </button>
            </form>
        @endif

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Change Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge role-{{ $user->role }}">
                                    {{ str_replace('_', ' ', $user->role ?? 'unknown') }}
                                </span>
                            </td>

                            <td>
                                @if($user->role === 'super_admin')
                                    <span class="muted">Protected account</span>
                                @elseif(auth()->id() === $user->id)
                                    <span class="muted">Your account</span>
                                @else
                                    <form class="role-form" method="POST"
                                          action="{{ route('staff.role.update', $user) }}">
                                        @csrf
                                        @method('PATCH')

                                        <select name="role" required
                                                aria-label="Role for {{ $user->name }}">
                                            <option value="admin"
                                                @selected($user->role === 'admin')>
                                                Admin
                                            </option>
                                            <option value="cashier"
                                                @selected($user->role === 'cashier')>
                                                Cashier
                                            </option>
                                            <option value="receptionist"
                                                @selected($user->role === 'receptionist')>
                                                Receptionist
                                            </option>
                                        </select>

                                        <button type="submit">Save</button>
                                    </form>
                                @endif
                            </td>

                            <td>
                                @if($user->role !== 'super_admin' && auth()->id() !== $user->id)
                                    <details>
                                        <summary>Reset Password</summary>

                                        <form class="action-form" method="POST"
                                              action="{{ route('staff.reset-password', $user) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input type="password" name="password"
                                                   placeholder="New password (12+)"
                                                   minlength="12" required
                                                   autocomplete="new-password">

                                            <input type="password"
                                                   name="password_confirmation"
                                                   placeholder="Confirm new password"
                                                   minlength="12" required
                                                   autocomplete="new-password">

                                            <button type="submit">
                                                Save New Password
                                            </button>
                                        </form>
                                    </details>

                                    <form method="POST"
                                          action="{{ route('staff.destroy', $user) }}"
                                          onsubmit="return confirm('Delete this account permanently?');"
                                          style="margin-top:10px;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger" type="submit">
                                            Delete Account
                                        </button>
                                    </form>
                                @else
                                    <span class="muted">Protected</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No staff accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $users->links() }}
        </div>
    </section>
</main>
</body>
</html>


