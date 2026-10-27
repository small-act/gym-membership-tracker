<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Accounts | Gym Membership Tracker</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;padding:24px;background:#081426;color:#edf4ff;font-family:Arial,sans-serif}
        main{max-width:1100px;margin:auto}
        section{background:#11213a;border:1px solid #263a56;border-radius:12px;padding:22px;margin:18px 0}
        h1,h2{margin-top:0}
        p,small{color:#b8c8dc}
        label{display:block;margin:12px 0 6px}
        input,select{width:100%;padding:11px;background:#081426;color:white;border:1px solid #334967;border-radius:7px}
        button,.btn{display:inline-block;padding:10px 14px;border:0;border-radius:7px;background:#1677e8;color:white;text-decoration:none;cursor:pointer}
        .danger{background:#a52a3a}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
        .message{padding:12px;background:#173c36;border-radius:7px;margin:12px 0}
        .error{background:#3b1725;color:#fda4af}
        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse}
        th,td{text-align:left;padding:12px;border-bottom:1px solid #263a56}
        th{color:#9db0c9}
        @media(max-width:650px){.grid{grid-template-columns:1fr}body{padding:12px}section{padding:15px}}
    </style>
</head>
<body>
<main>
    <a class="btn" href="{{ route('dashboard') }}">Back to Dashboard</a>
    <h1>Member Accounts</h1>
    <p>Create and manage portal logins for existing gym members. Members cannot register themselves.</p>

    @if(session('success'))
        <div class="message">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="message error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section>
        <h2>Create Member Login</h2>
        <form method="POST" action="{{ route('member-accounts.store') }}">
            @csrf
            <div class="grid">
                <div>
                    <label for="member_id">Existing active member</label>
                    <select id="member_id" name="member_id" required>
                        <option value="">Select a member without an account</option>
                        @foreach($members as $member)
                            @if(!$member->memberAccount && $member->status === 'active')
                                <option value="{{ $member->id }}" @selected((string)old('member_id') === (string)$member->id)>
                                    {{ $member->full_name }} — {{ $member->member_number }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="email">Member login email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="off">
                </div>
                <div>
                    <label for="password">Temporary password (minimum 12 characters)</label>
                    <input id="password" type="password" name="password" required minlength="12" autocomplete="new-password">
                </div>
                <div>
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="12" autocomplete="new-password">
                </div>
            </div>
            <p>Give the login details to the member securely. Passwords are stored as hashes and cannot be displayed later.</p>
            <button type="submit">Create Member Account</button>
        </form>
    </section>

    <section>
        <h2>Existing Members</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Member</th><th>Member Number</th><th>Status</th><th>Portal Account</th><th>Login Email</th><th>Action</th></tr></thead>
                <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>{{ $member->full_name }}</td>
                        <td>{{ $member->member_number }}</td>
                        <td>{{ ucfirst($member->status) }}</td>
                        <td>{{ $member->memberAccount ? 'Enabled' : 'Not created' }}</td>
                        <td>{{ $member->memberAccount?->email ?? '—' }}</td>
                        <td>
                            @if($member->memberAccount)
                                <form method="POST" action="{{ route('member-accounts.destroy', $member->memberAccount) }}" onsubmit="return confirm('Remove this member login? Their member record, membership, attendance, and payments will remain.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger" type="submit">Remove Login</button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No members found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $members->links() }}
    </section>
</main>
</body>
</html>